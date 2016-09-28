<?php defined('INCLUDED') or die('Denied'); try_logged();

$id = verify_id('deudas');

$gump = new GUMP();
$_POST = $gump->sanitize($_POST);

$gump->validation_rules(array(
    'deudor'        => 'required|integer',
    'descripcion'   => 'required|text',
    'monto'         => 'required|integer',
    'fecha'         => 'required|date',
    'pagada'        => 'exact_len,1|contains,1 0',
));

$gump->filter_rules(array(
    'deudor'        => 'trim',
    'descripcion'   => 'trim',
    'monto'         => 'trim',
    'fecha'         => 'trim',
    'pagada'        => 'trim|default,0',
));

$data = $gump->run($_POST);

if($data === false) {
    throw_error(var_export($gump->get_errors_array(), true));
}

$stmt = $db->prepare('UPDATE deudas SET deudor = ?, descripcion = ?, monto = ?, fecha = ?, pagada = ? WHERE id = ?');

if(!isset($data['pagada'])) $data['pagada'] = 0;
$stmt->bind_param('isisii', $data['deudor'], $data['descripcion'], $data['monto'], $data['fecha'], $data['pagada'], $id);
$stmt->execute();

if(!empty($stmt->error)) {
    throw_error($stmt->error);
}

throw_data($data);