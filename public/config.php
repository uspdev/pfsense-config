<?php
require_once __DIR__ . '/../app/bootstrap.php';
use Uspdev\Pfconfig\Pfsense;

$usr = is_auth();

if (!$usr->admin) {
    exit;
}

$config = Pfsense::obterConfig();

echo json_encode($config);