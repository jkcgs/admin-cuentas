<?php defined("INCLUDED") or die("nel");
// https://github.com/jkcgs/BankAccountMgr/blob/master/js/banks_modules/estado.js

$ubase = "https://bancapersonas.bancoestado.cl/eBankingBech/";
$formURL = $ubase . "login/login.htm";
$postURL = $ubase . "login";
$post2URL = "https://personas.bancoestado.cl/bancoestado/process.asp?MID=&AID=LOGIN-001";
$resumenURL = $ubase . "superCartola/superCartola.htm";
$homeURL = $ubase . "home/home.htm";
$logoutURL = "seguridad/logoutIN.htm";

$ch = curl_init();
$d = wget($formURL, $ch);

$token = find_text($d, 'ctoken" value="', '"');
$login_data = array(
    'j_username' => $config['be_user'],
    'j_password' => $config['be_pass'],
    'ctoken' => $token
);
$headers = array('User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.112 Safari/537.36');
$login_res = wpost($postURL, $login_data, $ch, $headers);

if(strpos($login_res, "Home - BUILD") === false) {
    echo "no login xd\n<pre style='white-space: normal'>";
    echo str_replace("<", "&lt;", $login_res);
    echo "</pre>";

    echo "<pre style='white-space: normal'>";
    echo str_replace("<", "&lt;", wget($resumenURL, $ch));
    echo "</pre>";

    wget($logoutURL, $ch);
    curl_close($ch);
    die();
}

echo "todo bien todo correcto y yo que xd";

wget($logoutURL, $ch);
curl_close($ch);
