<?php

function throw_error($msg) {
    $m = json_encode([
        "success" => false,
        "message" => $msg,
        "data" => null
    ]);

    die($m);
}

function json_data($data) {
    return json_encode([
        "success" => true,
        "message" => null,
        "data" => $data
    ]);
}

function try_logged() {
    if(!isset($_SESSION['logged']) || !$_SESSION['logged']) {
        header("HTTP/1.1 401 Unauthorized");
        throw_error("Not logged");
    }
}