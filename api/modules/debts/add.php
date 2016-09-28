<?php defined('INCLUDED') or die('Denied'); try_logged();

$gump = new GUMP();
$_POST = $gump->sanitize($_POST);

$gump->validation_rules(
    array(
        'deudor'            => 'required|integer',
        'descripcion'       => 'required|text',
        'monto'             => 'required|integer',
        'fecha'             => 'required|date',
        'pagada'            => 'exact_len,1|contains,1 0',
    )
);

$gump->filter_rules(
    array(
        'deudor'        => 'trim',
        'descripcion'   => 'trim',
        'monto'         => 'trim',
        'fecha'         => 'trim',
        'pagada'        => 'trim|default,0',
    )
);

$data = $gump->run($_POST);

if ($data === false) {
     throw_error(var_export($gump->get_errors_array(), true));
}

$deudor = get_by_id('deudores', $data['deudor']);
if(!$deudor) {
    throw_error("El deudor no existe");
}

$stmt = $db->prepare(
    'INSERT INTO deudas (usuario_id, deudor, descripcion, monto, fecha, pagada) '.
    'VALUES (?, ?, ?, ?, ?)'
);
   
if (!$stmt) {
    throw_error($db->error);
}

if (!isset($data['pagada'])) {
    $data['pagada'] = 0;
} 

$stmt->bind_param(
    'iisdsi', $UID, $data['deudor'], $data['descripcion'], $data['monto'], $data['fecha'], $data['pagada']
);
$stmt->execute();

if (!empty($stmt->error)) {
    throw_error($stmt->error);
}

$data['id'] = $stmt->insert_id;
$data['nombreDeudor'] = $deudor['nombre'];
throw_data($data);
