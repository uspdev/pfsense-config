<?php
require_once __DIR__ . '/../../app/bootstrap.php';

if (!empty($_GET['codpes']) && $_SESSION['user']['admin'] == 1) {
    $_SESSION['user']['loginUsuario'] = $_GET['codpes'];
    header('location:../index.php');
    exit;
}
$credentials = array(
    'consumer_key' => $_ENV['consumer_key'],
    'consumer_secret' => $_ENV['consumer_secret'],
    'callback_id' => $_ENV['callback_id'], 
    'amb' => $_ENV['amb']
);
$auth = new \Uspdev\Senhaunica\Senhaunica($credentials);
$res = $auth->login();

$_SESSION['user'] = $res;
$_SESSION['user']['ip'] = $_SERVER['REMOTE_ADDR'];

$admin = $_ENV['admin'];
if (strpos($admin, $_SESSION['user']['loginUsuario']) !== false) {
    $_SESSION['user']['admin'] = 1;
}

$usr = is_auth();

$log['timestamp'] = date('Y-m-d H:i:s');
$log['codpes'] = $usr->codpes;
$log['name'] = $usr->nome;
$log['from'] = $usr->ip;
Uspdev\Pfconfig\Log::access($log);

header('location:../index.php');