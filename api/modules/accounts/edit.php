<?php defined("INCLUDED") or die("Denied"); try_logged();

$id = verify_id('cuentas', $UID);

$gump = new GUMP();
$_POST = $gump->sanitize($_POST);

$gump->validation_rules(array(
    'nombre'            => 'required|text',
    'descripcion'       => 'required|text',
    'fecha_compra'      => 'required|date',
    'fecha_facturacion' => 'required|yearmonth',
    'monto_original'    => 'required|float',
    'divisa_original'   => 'required|exact_len,3',
    'monto'             => 'required|float',
    'num_cuotas'        => 'required|integer',
    'pagado'            => 'exact_len,1|contains,1 0',
    'info'              => 'text'
));

$gump->filter_rules(array(
    'nombre'            => 'trim',
    'descripcion'       => 'trim',
    'fecha_compra'      => 'trim',
    'fecha_facturacion' => 'trim',
    'monto_original'    => 'trim',
    'monto'             => 'trim',
    'divisa_original'   => 'trim|upper',
    'num_cuotas'        => 'trim|default,0',
    'pagado'            => 'trim|default,0',
    'info'              => 'trim'
));

$data = $gump->run($_POST);

if($data === false) {
    throw_error(var_export($gump->get_errors_array(), true));
}

$stmt = $db->prepare(
    'UPDATE cuentas SET nombre = ?, descripcion = ?, fecha_compra = ?, fecha_facturacion = ?, '.
    'monto_original = ?, divisa_original = ?, monto = ?, num_cuotas = ?, info = ?, pagado = ? '.
    'WHERE id = ?');

if(!isset($data['pagado'])) $data['pagado'] = 0;
$stmt->bind_param('ssssdsdisii', $data['nombre'], $data['descripcion'], $data['fecha_compra'], $data['fecha_facturacion'],
    $data['monto_original'], $data['divisa_original'], $data['monto'], $data['num_cuotas'], $data['info'], $data['pagado'], $id);
$stmt->execute();

if(!empty($stmt->error)) {
    throw_error($stmt->error);
}

throw_success($data);