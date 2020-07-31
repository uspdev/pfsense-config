<?php
require_once __DIR__ . '/../app/bootstrap.php';

use raelgc\view\Template;
use Uspdev\Pfconfig\Pfsense;
use Uspdev\Pfconfig\Log;

$usr = is_auth();

if (!empty($_POST['acao'])) {
    switch ($_POST['acao']) {
        case 'atualizarNat':
            Pfsense::atualizarNat($usr, $_POST['associated-rule-id']);
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
    $tpl->nat = $rule;
    //print_r($rule);exit;
    if ($rule->source->address == $usr->ip) {
        $tpl->block('block_nat_rule_ok');
    } else {
        $tpl->block('block_nat_rule_atualizar');
    }
    $tpl->block('block_nat_rule');
}

$filter = Pfsense::listarFilter($usr->codpes);
foreach ($filter as $rule) {
    //print_r($rule);//exit;
    if (empty($rule->destination->address)) {
        $rule->destination->address = $rule->interface;
    }
    $tpl->filter = $rule;
    // somente as regras que não são automáticas
    if (strpos($rule->descr, 'NAT ') !== 0) {
        if ($rule->source->address == $usr->ip) {
            $tpl->block('block_filter_rule_ok');
        } else {
            $tpl->block('block_filter_rule_atualizar');
        }
        $tpl->block('block_filter_rule');
    }
}

if ($usr->admin) {
    $tpl->access = Log::listar();
    $tpl->block('block_admin');
}


$tpl->show();
