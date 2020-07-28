<?php

namespace Uspdev\Pfconfig;

class Pfsense
{
    const remote_script = 'pfsense-config2';

    public static function listarNat($codpes)
    {
        $config = SELF::obterConfig();

        $out = array();
        foreach ($config['nat']['rule'] as $value) {
            // procura o codpes na descricao
            if (strpos($value['descr'], $codpes) !== false) {
                SELF::replaceDash($value);
                array_push($out, $value);
            }
        }
        return json_decode(json_encode($out));
    }

    public static function listarFilter($codpes)
    {
        $config = SELF::obterConfig();

        $out = array();
        foreach ($config['filter']['rule'] as &$value) {
            // procura o codpes na descricao
            if (strpos($value['descr'], $codpes) !== false) {
                SELF::replaceDash($value);
                array_push($out, $value);
            }
        }
        return json_decode(json_encode($out));
    }

    public static function atualizarNat($usr, $associated_rule_id)
    {
        $log = array();
        $log['timpestamp'] = date('Y-m-d H:i:s');
        $log['codpes'] = $usr->codpes;
        $log['new_ip'] = $usr->ip;

        //echo $associated_rule_id;exit;
        foreach (SELF::listarNat($usr->codpes) as $nat) {
            if ($nat->associated_rule_id == $associated_rule_id) {
                $log['prev_ip'] = $nat->source->address;
                $log['target'] = $nat->destination->address . ':' . $nat->destination->port;

                $nat->source->address = $usr->ip;
                $nat->descr = preg_replace("/\(.*?\)/", "(" . date('Y-m-d') . ")", $nat->descr);

                $nat = SELF::toArray($nat);
                SELF::replaceUnderscore($nat);
                $param['nat'] = $nat;
                break;
            }
        }

        foreach (SELF::listarFilter($usr->codpes) as $filter) {
            if (!empty($filter->associated_rule_id) && $filter->associated_rule_id == $associated_rule_id) {
                $filter->source->address = $usr->ip;
                $filter->descr = preg_replace("/\(.*?\)/", "(" . date('Y-m-d') . ")", $filter->descr);

                $filter = SELF::toArray($filter);
                SELF::replaceUnderscore($filter);
                $param['filter'] = $filter;
                break;
            }
        }

        // chave para busca no caso de nat
        $param['key'] = 'associated-rule-id';

        $exec_string = sprintf(
            'ssh %s pfSsh.php playback %s nat %s',
            $_ENV['pfsense_ssh'],
            SELF::remote_script,
            base64_encode(serialize($param))
        );
        exec($exec_string, $fw);
        Log::update($log);

        // recarrega a configuração atualizada
        SELF::obterConfig(true);
    }

    public static function atualizarFilter($usr, $descr)
    {
        $log = array();
        $log['timpestamp'] = date('Y-m-d H:i:s');
        $log['codpes'] = $usr->codpes;
        $log['new_ip'] = $usr->ip;

        foreach (SELF::listarFilter($usr->codpes) as $filter) {
            if ($filter->descr == $descr) {
                $log['prev_ip'] = $filter->source->address;
                $log['target'] = $filter->destination->address;

                $filter->descr = preg_replace("/\(.*?\)/", "(" . date('Y-m-d') . ")", $filter->descr);
                $filter->source->address = $usr->ip;

                $filter = SELF::toArray($filter);
                SELF::replaceUnderscore($filter);
                $param['filter'] = $filter;
                break;
            }
        }

        // chave para busca no caso de filter
        $param['key'] = 'tracker';

        $exec_string = sprintf(
            'ssh %s pfSsh.php playback %s filter %s',
            $_ENV['pfsense_ssh'],
            SELF::remote_script,
            base64_encode(serialize($param))
        );
        exec($exec_string, $fw);
        Log::update($log);

        // recarrega a configuração atualizada
        SELF::obterConfig(true);
    }

    public static function obterConfig($atualizar = false)
    {
        if ($atualizar || empty($_SESSION['pf_config'])) {
            exec('ssh ' . $_ENV['pfsense_ssh'] . ' pfSsh.php playback pc-getConfig', $pf_config);
            $_SESSION['pf_config'] = json_decode($pf_config[0], true);
        }

        return $_SESSION['pf_config'];
    }

    protected static function replaceDash(&$array)
    {
        $array = array_combine(
            array_map(
                function ($str) {
                    return str_replace("-", "_", $str);
                },
                array_keys($array)
            ),
            array_values($array)
        );
    }

    protected static function replaceUnderscore(&$array)
    {
        $array = array_combine(
            array_map(
                function ($str) {
                    return str_replace("_", "-", $str);
                },
                array_keys($array)
            ),
            array_values($array)
        );
    }

    protected static function toObj($arr)
    {
        return json_decode(json_encode($arr));
    }

    protected static function toArray($obj)
    {
        return json_decode(json_encode($obj), true);
    }
}
