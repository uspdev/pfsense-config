<?php

namespace Uspdev\Pfconfig;

class Pfsense
{
    public static function listarNat($codpes)
    {
        $config = SELF::obterConfig();

        $out = array();
        foreach ($config['nat']['rule'] as &$value) {
            // procura o codpes na descricao
            if (strpos($value['descr'], $codpes) !== false) {
                array_push($out, $value);
            }
        }
        return $out;
    }

    public static function listarFilter($codpes)
    {
        $config = SELF::obterConfig();

        $out = array();
        foreach ($config['filter']['rule'] as &$value) {
            // procura o codpes na descricao
            if (strpos($value['descr'], $codpes) !== false) {
                array_push($out, $value);
            }
        }
        return $out;
    }

    public static function listarNat2()
    {
        $config = SELF::obterConfig();
        $out = array();
        foreach ($config['nat']['rule'] as $key => $rule) {
            $tmp['id'] = $key;
            $tmp['destination'] = $rule['destination']['address'] . ':' . $rule['destination']['port'];
            $tmp['descr'] = $rule['descr'];
            $out[] = $tmp;
        }
        return $out;
        return $config['nat']['rule'];
    }

    public static function atualizarTudo($usr)
    {
        foreach (SELF::listarNat($usr->codpes) as $rule) {
            $exec = sprintf(
                'ssh %s pfSsh.php playback updateNat %s %s',
                $_ENV['pfsense_ssh'],
                $usr->codpes,
                $usr->ip
            );
            exec($exec, $fw);
            $log = array(
                'timpestamp' => date('Y-m-d H:i:s'),
                'codpes' => $usr->codpes,
                'target' => $rule['destination']['address'] . ':' . $rule['destination']['port'],
                'old_ip' => $rule['source']['address'],
                'new_ip' => $usr->ip,
            );
            Log::update($log);
        }
        SELF::obterConfig(true);
    }

    public static function atualizarNat($usr, $descr)
    {
        foreach (SELF::listarNat($usr->codpes) as $rule) {
            if ($rule['descr'] == $descr) {
                $exec = sprintf(
                    'ssh %s pfSsh.php playback pfsense-config nat %s %s',
                    $_ENV['pfsense_ssh'],
                    $usr->codpes,
                    $usr->ip
                );
                exec($exec, $fw);
                //print_r($fw);exit;
                $log = array(
                    'timpestamp' => date('Y-m-d H:i:s'),
                    'codpes' => $usr->codpes,
                    'target' => $rule['destination']['address'] . ':' . $rule['destination']['port'],
                    'old_ip' => $rule['source']['address'],
                    'new_ip' => $usr->ip,
                );
                Log::update($log);
                break;
            }
        }
        SELF::obterConfig(true);
    }

    public static function atualizarFilter($usr, $descr)
    {
        foreach (SELF::listarFilter($usr->codpes) as $rule) {
            if ($rule['descr'] == $descr) {
                $exec = sprintf(
                    'ssh %s pfSsh.php playback pfsense-config filter %s %s',
                    $_ENV['pfsense_ssh'],
                    $rule['tracker'],
                    $usr->ip
                );
                exec($exec, $fw);


                $log = array(
                    'timpestamp' => date('Y-m-d H:i:s'),
                    'codpes' => $usr->codpes,
                    'target' => $rule['destination']['address'],
                    'old_ip' => $rule['source']['address'],
                    'new_ip' => $usr->ip,
                );
                Log::update($log);

                break;
            }
        }
        SELF::obterConfig(true);
        // echo $exec;
        // print_r($fw);exit;
    }

    public static function obterConfig($atualizar = false)
    {
        if ($atualizar || empty($_SESSION['pf_config'])) {
            exec('ssh ' . $_ENV['pfsense_ssh'] . ' pfSsh.php playback pc-getConfig', $pf_config);
            $_SESSION['pf_config'] = json_decode($pf_config[0], true);
        }

        return $_SESSION['pf_config'];
    }
}
