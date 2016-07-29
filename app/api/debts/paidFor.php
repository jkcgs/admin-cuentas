<?php defined('INCLUDED') or die('Denied'); try_logged();

$id = verify_id('deudores');

if(!isset($_GET['paid'])) {
    throw_error("Paid value not sent");
}

$set_paid = $_GET['paid'];
if(!in_array($set_paid, array("0", "1"))) {
    throw_error("Invalid paid value");
}


$q = $db->query("UPDATE deudas SET pagada = $set_paid WHERE deudor = $id");

if(!$q) {
    throw_error("Error: " . $db->error);
}

throw_success();