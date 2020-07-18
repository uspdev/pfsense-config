<?php
require_once __DIR__ . '/../../config.php';

$auth = new Uspdev\Senhaunica\Senhaunica($senhaunica);
$res = $auth->login();

$_SESSION['user'] = $res;
header('location:../index.php');