<?php defined("INCLUDED") or die("Denied"); try_logged();

require_once "includes/database.php";
require_once "includes/encryption.class.php";

$res = $db->query("SELECT user, password, nombre FROM cuenta_bancaria WHERE usuario_id = $UID AND tipo = 1");
$bank_accounts = [];
while($account = $res->fetch_assoc()) {
    $bank_accounts[] = [
        "user" => $account['user'],
        "pass" => Encryption::decrypt_user_pass($account['password']),
        "bank" => $account['nombre']
    ];
}

$bank_ins = [];

foreach($bank_accounts as $acc) {
    $bpath = "includes/banks/{$acc["bank"]}.php";
    if(file_exists($bpath)) {
        require_once($bpath);
        $bn = "Bank_" . ucfirst($acc["bank"]);
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