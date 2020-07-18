<?php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user'])) {
    header('Location:login/');
    exit;
}

$codpes = $_SESSION['user']['loginUsuario'];
$username = $_SESSION['user']['nomeUsuario'];
$ip = $_SERVER['REMOTE_ADDR'];

exec("ssh $pfsense_ssh pfSsh.php playback updateNat $codpes", $fw);
$fw = json_decode($fw[0], true);
?>
<html>

<body>
    <h1>Configurador do firewall do SET</h1>
    Usuário: <?php echo $codpes ?> - <?php echo $username ?><br>
    Meu ip atual: <?php echo $ip ?><br>
    <br>
    <?php if (empty($fw)) { ?>
        Sem regras para mostrar<br>
    <?php } else {?>
        <?php foreach ($fw as $rule) {?>
            Origem: <?php echo $rule['source']['address']; ?><br>
            Destino: <?php echo $rule['destination']['address']; ?>:<?php echo $rule['destination']['port']; ?><br>
        <?php }?>
        <a href="atualizar.php">Atualizar</a><br>
        <br>
        <a href="logout/">Sair</a>
    <?php }?>
</body>

</html>