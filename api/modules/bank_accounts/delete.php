<?php defined("INCLUDED") or die("Denied"); try_logged();

if(count($route_args) < 1) {
    throw_error("No se envió el ID");
}

delete_obj('cuenta_bancaria', $route_args[0], $UID);