<?php

namespace Uspdev\Pfconfig;

class Log
{

    const log_file = __DIR__ . '/../log/user.log';

    public static function access($arr)
    {
        $log = '';
        $arr['type'] = 'access';
        foreach ($arr as $k => $v) {
            $log .= $k . '=' . $v . ';';
        }
        file_put_contents(SELF::log_file, $log . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public static function update($arr)
    {
        $log = '';
        $arr['type'] = 'update';
        foreach ($arr as $k => $v) {
            $log .= $k . '=' . $v . ';';
        }
        file_put_contents(SELF::log_file, $log . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public static function listar()
    {
        return file_get_contents(SELF::log_file);
    }
    
    public static function listar2($numLogFiles = 5)
    {
        $files = glob(SELF::log_file);
        arsort($files, SORT_STRING);
        $i = 0;
        $logs = [];
        foreach ($files as $file) {
            $logs = array_merge($logs, file($file, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES));
            if ($i < $numLogFiles) {
                $i++;
            } else {
                break 1;
            }
        }
        return $logs;
    }
}
