<?php defined("INCLUDED") or die("Acceso denegado.");

if(!isset($_SESSION['logged'])) {
    $_SESSION['logged'] = false;
}

$data = [
    "logged" => $_SESSION['logged']
];

if($data["logged"]) {
    require_once "includes/database.php";
    $user_data = get_user_data();

    if(!$user_data) {
        header("HTTP/1.1 401 Unauthorized");
        throw_error("Not logged");
        exit;
    } else {
        unset($user_data["password"]);
        $data["user"] = $user_data;
    }
}

throw_data($data);