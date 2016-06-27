<?php defined("INCLUDED") or die("nel");

//// Inicialización
header('Content-Type: text-json');

use PHPHtmlParser\Dom;
$curl = init_curl();

//// Funciones

function check_error($c) {
    // revisar error
    if ($c->error) {
        echo json_encode([
            'error' => $c->errorMessage,
            'code' => $c->errorCode,
            'curl_error' => true
        ]);
        exit;
    }
}

function throw_wrong() {
    echo json_encode([
        'error' => 'Página incorrecta',
        'code' => -1
    ]);
    exit;
}

////////////////////////////////////////////////////////////
// Inicio proceso

// Pre-definiciones
$prefix = "https://bancapersonas.bancoestado.cl/eBankingBech/";
$loginPage = $prefix."login/login.htm";
$loginPost = $prefix."login";
$logoutPage = $prefix."seguridad/logoutIN.htm";
$resumePage = $prefix."superCartola/superCartola.htm";

//////// Paso 1: cargar login

$curl->get($loginPage);

// revisar error
check_error($curl);

// revisar página correcta
if(strpos($curl->response, "BancoEstado Login") === false) {
    throw_wrong();
}

$ctoken = find_text($curl->response, 'ctoken" value="', '"');

//////// Paso 2: Login

$curl->post($loginPost, array(
    'j_username' => $config['be_user'],
    'j_password' => $config['be_pass'],
    'ctoken' => $ctoken
));

// revisar error
check_error($curl);
if(strpos($curl->response, "Home - BUILD") === false) {
    throw_wrong();
}

//////// Paso 3: Recuperar datos

$curl->get($resumePage);

// revisar error
check_error($curl);
if(strpos($curl->response, "Resumen de Productos") === false) {
    throw_wrong();
}

$dom = new Dom;
$dom->load($curl->response);

$domcuentas = $dom->getElementById('table_1')->find('tbody tr');
$cuentas = [];

foreach($domcuentas as $cuenta) {
    $conts = $cuenta->find('td');
    $nc = [
        'nombre_banco' => 'Banco Estado',
        'nombre_cuenta' => trim($conts[0]->find('span')[0]->text),
        'numero_cuenta' => trim($conts[1]->text),
        'moneda' => trim($conts[2]->text),
        'dinero' => preg_replace('/[^0-9]/', '', trim($conts[3]->text))
    ];

    $cuentas[] = $nc;
}

echo json_encode(array("message" => "ok", "data" => $cuentas));

//////// Paso 4: Logout

$curl->get($logoutPage);
