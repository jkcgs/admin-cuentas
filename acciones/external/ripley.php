<?php defined("INCLUDED") or die("nel");

$is_win = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
$nullfile = $is_win ? 'NUL' : '/dev/null';

$rut = $config['tr_user'];
$pass = $config['tr_pass'];
$url_login = "https://www.ripley.cl/tarjeta/login.do";
$url_mov = "https://www.ripley.cl/tarjeta/movimientos/movimientos.do";

$data = array('rut' => $rut, 'password' => $pass);
$ch = curl_init();
$result = wpost($url_login, $data, $ch);

if(strpos($result, "\"logueado\" : true") === false) {
    die(jerr("No se pudo iniciar sesión en el sitio externo"));
}

$movimientos = wget($url_mov, $ch);
curl_close($ch);

$d = "cellspacing=\"0\">-->";
$a = strpos($movimientos, $d);
$b = strpos($movimientos, "</table>", $a);
$cont = substr($movimientos, $a + strlen($d), $b - $a);

$res = null;
preg_match_all("/<td( class=\"bgDest\")?>(.*)<\/td>/", $cont, $res);
$res = array_map(function($d){ return str_replace("&nbsp;", "", $d); }, $res[2]);
$res = array_map('trim', $res);
$res = array_chunk($res, 6);

$res2 = array();
$conv = explode(",", "fecha,comercio,monto,cuotas,valor,documento");
for($i = 0; $i < count($res); $i++) {
    $d = array();
    for($j = 0; $j < count($res[$i]); $j++) {
        if($conv[$j] == "monto" || $conv[$j] == "valor") {
            $d[$conv[$j]] = intval(preg_replace("/[\$\.]/", "", $res[$i][$j]));
        } else {
            $d[$conv[$j]] = $res[$i][$j];
        }

    }

    $res2[] = $d;
}

header("Content-Type: text/json");
echo json_encode(array("message" => "ok", "data" => $res2));