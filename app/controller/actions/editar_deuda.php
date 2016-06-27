<?php defined("INCLUDED") or die("nel");

$id = verificar_id('deudas');

$gump = new GUMP();
$_POST = $gump->sanitize($_POST);

$gump->validation_rules(array(
    'deudor'        => 'required|integer',
    'descripcion'   => 'required|text',
    'monto'         => 'required|integer',
    'fecha'         => 'required|date',
    'pagada'        => 'exact_len,1|contains,1',
));

$gump->filter_rules(array(
    'deudor'        => 'trim',
    'descripcion'   => 'trim',
    'monto'         => 'trim',
    'fecha'         => 'trim',
    'pagada'        => 'trim|default,0',
));

$data = $gump->run($_POST);

header("Content-Type: text/json");
if($data === false) {
    die(jerr(var_export($gump->get_readable_errors(), true)));
}

$stmt = $db->prepare('UPDATE deudas SET deudor = ?, descripcion = ?, monto = ?, fecha = ?, pagada = ? WHERE id = ?');

if(!isset($data['pagada'])) $data['pagada'] = 0;
$stmt->bind_param('isisii', $data['deudor'], $data['descripcion'], $data['monto'], $data['fecha'], $data['pagada'], $id);
$stmt->execute();

if(!empty($stmt->error)) {
    die(jerr($stmt->error));
}

echo json_encode($data);