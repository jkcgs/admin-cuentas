<?php defined("INCLUDED") or die("Denied");

$res_users = $db->query("SELECT id, user, enabled, is_admin FROM usuarios");
if(!$res_users) {
    throw_error("Error de DB: " . $db->error);
}

$users = db_fetch_all($res_users);
throw_data($users);