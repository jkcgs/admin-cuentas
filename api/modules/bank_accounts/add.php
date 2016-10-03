<?php defined("INCLUDED") or die("Denied"); try_logged();

$gump = new GUMP();
$_POST = $gump->sanitize($_POST);

$gump->validation_rules(
    array(
        'type' => 'required|int',
        'bank' => 'required|int',
        'user' => 'required|text',
        'pass' => 'required|text'
    )
);

$gump->filter_rules(
    array(
        'type' => 'trim',
        'bank' => 'trim',
        'user' => 'trim',
        'pass' => 'trim'
    )
);

$sql = "INSERT INTO cuenta_bancaria (usuario_id, tipo, nombre, user, password) VALUES (%s, %s, %s, %s, %s)";
$q = $db->query(sprintf($sql, $UID,
    $db->escape_string($_POST['type']),
    $db->escape_string($_POST['bank']),
    $db->escape_string($_POST['user']),
    $db->escape_string($_POST['pass'])
));