<?php defined("INCLUDED") or die("Denied"); try_logged();

$bank_accounts = $config["external"]["banks"];
$bank_ins = [];

foreach($bank_accounts as $acc) {
    $bpath = "includes/banks/{$acc["bank"]}.php";
    if(file_exists($bpath)) {
        require_once($bpath);
        $bn = ucfirst($acc["bank"]);
        $bank_ins[] = new $bn($acc["user"], $acc["pass"]);
    }
}

$data = [];
foreach($bank_ins as $b) {
    $attempts = 0;
    while(true) {
        try {
            $d = $b->getAccounts();
            if($d && count($d) > 0 && isset($d[0]["bank"])) {
                $data = array_merge($data, $d);
            }
            
            break;
        } catch (Exception $e) {
            if(get_class($e) != "TemporalError" || $attempts > 3) {
                break;
            }

            $attempts++;
        }
    }
    
    

    $b->logout();
}

throw_data($data);