<?php defined('INCLUDED') or die('Denied'); try_logged();

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

$stmt = $db->prepare('INSERT INTO deudores (nombre, descripcion) VALUES (?, ?)');
$stmt->bind_param('ss', $data['nombre'], $data['descripcion']);
$stmt->execute();

if(!empty($stmt->error)) {
    throw_error($stmt->error);
}

$data['id'] = $stmt->insert_id;
throw_data($data);