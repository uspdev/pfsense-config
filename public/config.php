<?php
require_once __DIR__ . '/../app/bootstrap.php';

use Uspdev\Pfconfig\Pfsense;
use raelgc\view\Template;

$usr = is_auth();

if (!$usr->admin) {
    exit;
}

$tpl = new Template('../tpl/config.html');

$config = Pfsense::obterConfig(true);

$nat = $config['nat']['rule'];

foreach ($nat as $rule) {
    //print_r(json_decode(json_encode($rule)));exit;
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

    $tpl->rule = $rule;
    $tpl->block('block_nat_rule');
}

$tpl->show();
//echo json_encode($nat);
//echo json_encode($config);