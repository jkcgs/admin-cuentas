<?php defined("INCLUDED") or die("nel");
header("Content-Type: text/json");
if(!isset($_GET['ids']) || empty($_GET['ids'])) {
    die(jerr('No se enviaron IDs'));
}

$ids = explode(',', $_GET['ids']);
for($i = 0; $i < count($ids); $i++) {
    if((string)(int)$ids[$i] != $ids[$i]) {
        die(jerr('Se envió una o más IDs incorrectas'));
    }
}

$idsjoin = join(', ', $ids);

if(isset($_GET['deudas'])) {
    $s = $db->prepare("SELECT id FROM deudas WHERE id IN (".$idsjoin.")");
    $els = stmt_fetch_all($s);

    if(count($els) != count($ids)) {
        if((string)(int)$ids[$i] != $ids[$i]) {
            die(jerr('Uno o más IDs enviados no existe'));
        }
    }

    $s = $db->prepare("UPDATE deudas SET pagada = '1' WHERE id IN (".$idsjoin.")");
    $s->execute();
    if($s->error) {
        die(jerr('Error DB: ' . $s->error));
    }
} else {
    $s = $db->prepare("SELECT id FROM cuentas WHERE id IN (".$idsjoin.")");
    $els = stmt_fetch_all($s);

    if(count($els) != count($ids)) {
        if((string)(int)$ids[$i] != $ids[$i]) {
            die(jerr('Uno o más IDs enviados no existe'));
        }
    }

    $s = $db->prepare("UPDATE cuentas SET pagado = '1' WHERE id IN (".$idsjoin.")");
    $s->execute();
    if($s->error) {
        die(jerr('Error DB: ' . $s->error));
    }
}


die(jmsg('Actualizado correctamente'));