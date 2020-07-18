<?php
$senhaunica = [
    'consumer_key' => 'eesc_set',
    'consumer_secret' => 'xxxxx',
    'callback_id' => 8, // callback_id é o sequencial no servidor
    'amb' => 'prod', // 'dev' = teste, 'prod' = producao
];

$pfsense_ssh = 'root@pfsense_ip';

session_start();
require_once __DIR__ . '/vendor/autoload.php';