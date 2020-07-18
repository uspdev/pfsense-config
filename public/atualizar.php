<?php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user'])) {
    header('Location:login/');
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'];
$usr = $_SESSION['user']['loginUsuario'];

exec("ssh $pfsense_ssh pfSsh.php playback updateNat $codpes", $fw);
$fw = json_decode($fw[0], true);

foreach ($fw as $rule) {
    exec("ssh $pfsense_ssh pfSsh.php playback updateNat $codpes $ip", $fw);
}
header('Location:index.php');