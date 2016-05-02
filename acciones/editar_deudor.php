<?php defined("INCLUDED") or die("nel");

$id = verificar_id('deudores');

$gump = new GUMP();
$_POST = $gump->sanitize($_POST);

$gump->validation_rules(array(
    'nombre'            => 'required|text',
    'descripcion'       => 'required|text'
));
$gump->filter_rules(array(
    'nombre'            => 'trim',
    'descripcion'       => 'trim'
));

$data = $gump->run($_POST);

header("Content-Type: text/json");
if($data === false) {
    die(jerr(var_export($gump->get_readable_errors(), true)));
}

$stmt = $db->prepare('UPDATE deudores SET nombre = ?, descripcion = ? WHERE id = ?');
   
if(!$stmt) {
    die(jerr($db->error));
}

$stmt->bind_param('ssi', $data['nombre'], $data['descripcion'], $id);
$stmt->execute();

if(!empty($stmt->error)) {
    die(jerr($stmt->error));
}

echo json_encode($data);