<?php
require_once __DIR__ . '/../../app/bootstrap.php';

unset($_SESSION);
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Firewall do SET</title>
    <meta charset="UTF-8">
</head>
<body>
    <h1>Configurador do firewall do SET</h1>

    Você saiu do sistema.<br>
    Volte à <a href="../">página inicial</a> para entrar novamente.
</body>

</html>