<?php defined("INCLUDED") or die("nel");

//// Init
use PHPHtmlParser\Dom;

$randint = random_int(PHP_INT_MIN, PHP_INT_MAX);
$urlBase = "https://www.ripley.cl/tarjeta/";
$urlLogin = $urlBase."login.do";
$urlMov = $urlBase."movimientos/movimientos.do";
$urlLogout = $urlBase."cerrarSesion.do?_r=$randint";

$ch = init_curl();
$data = array(
    'rut' => $config['tr_user'],
    'password' => $config['tr_pass']
);

//// Login
$result = $ch->post($urlLogin, $data);

if (strpos($result, "\"logueado\" : true") === false) {
    die(jerr("No se pudo iniciar sesión en el sitio externo: " . $result));
}

// Cargar movimientos y cerrar sesión
$movimientos = $ch->get($urlMov);
$ch->get($urlLogout);
$ch->close();

// Parsear página buscando movimientos
$dom = new Dom;
$dom->load($movimientos);

$cuentasPags = $dom->getElementsByClass('datos_no_facturados');
$cuentas = [];

foreach ($cuentasPags as $pag) {
    $cols = $pag->find('tbody tr');
    foreach ($cols as $cuenta) {
        $conts = $cuenta->find('td');
        if (count($conts) < 1) {
            continue;
        }

        $contf = [];
        foreach ($conts as $cont) {
            $contf[] = str_replace("&nbsp;", "", $cont->text);
        }

        $nc = [
            'fecha' => trim($contf[0]),
            'comercio' => trim($contf[1]),
            'monto' => intval(preg_replace("/[\$\.]/", "", $contf[2])),
            'cuotas' => intval(trim($contf[3])),
            'valor' => intval(preg_replace("/[\$\.]/", "", $contf[4])),
            'documento' => trim($contf[5])
        ];

        $cuentas[] = $nc;
    }
}

echo json_encode(array("message" => "ok", "data" => array_reverse($cuentas)));
