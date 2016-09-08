<?php defined("INCLUDED") or die("Denied"); try_logged();

$bank_accounts = $config["external"]["banks"];
$bank_ins = [];

foreach($bank_accounts as $acc) {
    $bpath = "app/includes/banks/".$acc["bank"].".php";
    if(file_exists($bpath)) {
        require_once($bpath);
        $bn = ucfirst($acc["bank"]);
        $bank_ins[] = new $bn($acc["user"], $acc["pass"]);
    }
}

$data = [];
foreach($bank_ins as $b) {
    $data[] = $b->getAccounts();
    $b->logout();
}

throw_data($data);