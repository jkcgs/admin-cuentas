<?php defined("INCLUDED") or die("nel");

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

$stmt = $db->prepare('INSERT INTO deudores (nombre, descripcion) VALUES (?, ?)');
$stmt->bind_param('ss', $data['nombre'], $data['descripcion']);
$stmt->execute();

if(!empty($stmt->error)) {
    die(jerr($stmt->error));
}

$data['id'] = $stmt->insert_id;
echo json_encode($data);