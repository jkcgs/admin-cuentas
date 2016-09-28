<?php defined('INCLUDED') or die('Denied'); try_logged();

$id = verify_id('deudores');

$gump = new GUMP();
$_POST = $gump->sanitize($_POST);

$gump->validation_rules(array(
    'nombre'            => 'required|text',
    'descripcion'       => 'text'
));
$gump->filter_rules(array(
    'nombre'            => 'trim',
    'descripcion'       => 'trim'
));

$data = $gump->run($_POST);

if($data === false) {
    throw_error($gump->get_errors_array(), true);
}

$stmt = $db->prepare('UPDATE deudores SET nombre = ?, descripcion = ? WHERE id = ?');
   
if(!$stmt) {
    throw_error($db->error);
}

$stmt->bind_param('ssi', $data['nombre'], $data['descripcion'], $id);
$stmt->execute();

if(!empty($stmt->error)) {
    throw_error($stmt->error);
}

throw_data($data);