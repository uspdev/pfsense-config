<?php
require_once __DIR__ . '/../app/bootstrap.php';

use raelgc\view\Template;
use Uspdev\Pfconfig\Pfsense;

$usr = is_auth();

if (!$usr->admin) {
    exit;
}

$tpl = new Template('../tpl/config.html');

$config = Pfsense::obterConfig(true);

$nat = $config['nat']['rule'];

foreach ($nat as $rule) {
    //echo '<pre>';print_r(json_decode(json_encode($rule)));exit;
    $rule = json_decode(json_encode($rule));
    if (empty($rule->source->address)) {
        $rule->source->address = '';
    }
    if (empty($rule->{'associated-rule-id'})) {
        $rule->fw = '';
    } else {
        $rule->fw = 'fw ok';
    }
    preg_match('/\(.*?\)/', $rule->descr, $matches);
    $rule->date = substr($matches[0], 1, -1);

    // vamos formatar os dados da ultima atualização
    $rule->update = str_replace(' (Local Database)', '', $rule->updated->username);
    $rule->update .= $rule->update ? ' em ' . date('d/m/Y', $rule->updated->time) : '';

    $tpl->rule = $rule;
    //$tpl->timeUpdate = $rule->updated->time;
    $tpl->block('block_nat_rule');
}

$tpl->show();
//echo json_encode($nat);
//echo json_encode($config);
