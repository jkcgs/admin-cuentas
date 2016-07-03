<?php defined("INCLUDED") or die("nel");

$gump = new GUMP();
$_POST = $gump->sanitize($_POST); // You don't have to sanitize, but it's safest to do so.

$gump->validation_rules(
    array(
        'nombre'            => 'required|text',
        'descripcion'       => 'required|text',
        'fecha_compra'      => 'required|date',
        'fecha_facturacion' => 'required|yearmonth',
        'monto_original'    => 'required|float',
        'divisa_original'   => 'required|exact_len,3',
        'monto'             => 'required|float',
        'cuotas'            => 'required|integer',
        'pagado'            => 'exact_len,1|contains,1',
        'info'              => 'text'
    )
);

$gump->filter_rules(
    array(
        'nombre'            => 'trim',
        'descripcion'       => 'trim',
        'fecha_compra'      => 'trim',
        'fecha_facturacion' => 'trim',
        'monto_original'    => 'trim',
        'monto'             => 'trim',
        'divisa_original'   => 'trim|upper',
        'cuotas'            => 'trim|default,0',
        'pagado'            => 'trim|default,0',
        'info'              => 'trim'
    )
);

$data = $gump->run($_POST);

header("Content-Type: text/json");
if ($data === false) {
     die(jerr(var_export($gump->get_readable_errors(), true)));
}

$stmt = $db->prepare(
    'INSERT INTO cuentas (nombre, descripcion, fecha_compra, fecha_facturacion, monto_original, divisa_original, monto, num_cuotas, info, pagado)'.
    'VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
   
if (!$stmt) {
    die(jerr($db->error));
}

if(!isset($data['pagado'])) $data['pagado'] = 0;
$stmt->bind_param(
    'ssssdsdisi', $data['nombre'], $data['descripcion'], $data['fecha_compra'], $data['fecha_facturacion'],
    $data['monto_original'], $data['divisa_original'], $data['monto'], $data['cuotas'], $data['info'], $data['pagado']
);
$stmt->execute();

if (!empty($stmt->error)) {
    die(jerr($stmt->error));
}

$data['id'] = $stmt->insert_id;
echo json_encode($data);