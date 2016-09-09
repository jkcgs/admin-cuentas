<?php defined("INCLUDED") or die("Denied"); // try_logged();

require_once "app/includes/dolar.class.php";
$api = new DolarAPI();

throw_data($api->getCurrent());