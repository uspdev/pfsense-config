<?php

namespace Uspdev\Pfconfig;

class Log{
    public static function access($arr) {
        $log = '';
        foreach($arr as $k=>$v) {
            $log .= $k.'='.$v.';';
        }
        file_put_contents(__DIR__ . '/../log/access.log', $log . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public static function update($arr) {
        $log = '';
        foreach($arr as $k=>$v) {
            $log .= $k.'='.$v.';';
        }
        file_put_contents(__DIR__ . '/../log/update.log', $log . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}