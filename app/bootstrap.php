<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

if (version_compare(phpversion(), '7.0', '<')) {
    // php version isn't high enough
    $dotenv = Dotenv\Dotenv::create(__DIR__ . '/../');
} else {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
}
$dotenv->load();

function is_auth()
{
    if (!isset($_SESSION['user'])) {
        header('Location:login/');
        exit;
    }

    $ret = $_SESSION['user'];
    $ret['nome'] = $_SESSION['user']['nomeUsuario'];
    $ret['codpes'] = $_SESSION['user']['loginUsuario'];
    return json_decode(json_encode($ret));
}
