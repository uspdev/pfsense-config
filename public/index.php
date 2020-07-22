<?php
require_once __DIR__ . '/../app/bootstrap.php';

use raelgc\view\Template;
use Uspdev\Pfconfig\Pfsense;

$usr = is_auth();

if (!empty($_POST['acao'])) {
    switch ($_POST['acao']) {
        case 'atualizarNat':
            Pfsense::atualizarNat($usr, $_POST['descr']);
            header('Location:index.php');
            break;
        case 'atualizarFilter':
            Pfsense::atualizarFilter($usr, $_POST['descr']);
            header('Location:index.php');
            break;
        case 'obterConfig':
            if ($usr->admin) {
                Pfsense::obterConfig(true);
                header('Location:index.php');
            }
            break;
    }
}

$tpl = new Template('../tpl/index.html');
$tpl->usr = $usr;

$nat = Pfsense::listarNat($usr->codpes);
foreach ($nat as $rule) {
    $tpl->nat = json_decode(json_encode($rule));
    if ($rule['source']['address'] == $usr->ip) {
        $tpl->block('block_nat_rule_ok');
    } else {
        $tpl->block('block_nat_rule_atualizar');
    }
    $tpl->block('block_nat_rule');
}

$filter = Pfsense::listarFilter($usr->codpes);
foreach ($filter as $rule) {
    $tpl->filter = json_decode(json_encode($rule));
    if (strpos($rule['descr'], 'NAT ') !== 0) {
        if ($rule['source']['address'] == $usr->ip) {
            $tpl->block('block_filter_rule_ok');
        } else {
            $tpl->block('block_filter_rule_atualizar');
        }
    }
    $tpl->block('block_filter_rule');
}

if ($usr->admin) {
    $tpl->access = file_get_contents(__DIR__ . '/../log/access.log');
    $tpl->update = file_get_contents(__DIR__ . '/../log/update.log');
    $tpl->block('block_admin');
}


$tpl->show();
