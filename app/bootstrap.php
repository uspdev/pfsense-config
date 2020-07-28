<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
date_default_timezone_set('America/Sao_Paulo');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

function is_auth() {
    if (!isset($_SESSION['user'])) {
        header('Location:login/');
        exit;
    }
    
    $ret = $_SESSION['user'];
    $ret['nome'] = $_SESSION['user']['nomeUsuario'];
    $ret['codpes'] = $_SESSION['user']['loginUsuario'];
    return json_decode(json_encode($ret));
}