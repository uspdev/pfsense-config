<?php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user'])) {
    header('Location:login/');
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'];
$codpes = $_SESSION['user']['loginUsuario'];

exec("ssh $pfsense_ssh pfSsh.php playback updateNat $codpes", $fw);
$fw = json_decode($fw[0], true);

foreach ($fw as $rule) {
    exec("ssh $pfsense_ssh pfSsh.php playback updateNat $codpes $ip", $fw);
    $log = date('Y-m-d H:i:s') . '; ' . $codpes .
        '; target=' . $rule['destination']['address'] . ':' . $rule['destination']['port'] .
        '; old_ip=' . $rule['source']['address'] . '; new_ip=' . $ip;
    file_put_contents(__DIR__ . '/../log/update.log', $log . PHP_EOL, FILE_APPEND | LOCK_EX);
}
header('Location:index.php');
