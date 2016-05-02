<?php defined("INCLUDED") or die("nel");

$db_error = false;
@$db = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
if ($db->connect_errno) {
    die("Fallo al conectar a MySQL: #{$db->connect_errno} {$db->connect_error}");
}