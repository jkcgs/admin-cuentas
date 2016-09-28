<?php 
$enable = true;
if(!$enable) die("Desactivado");
if(count($_POST) > 0) include "includes/encryption.class.php";

if(isset($_POST["pass"])) {
    die(Encryption::password_hash($_POST["pass"]));
}

if(isset($_POST['etext'])) {
    die(Encryption::encrypt($_POST["etext"], $_POST["ekey"]));
}

if(isset($_POST['untext'])) {
    die(Encryption::decrypt($_POST["untext"], $_POST["unkey"]));
}

?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Encriptación</title>

        <!-- Bootstrap CSS -->
        <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    </head>
    <body>
        <div class="container" style="margin-top:2em">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Encriptar contraseña</h3>
                    </div>
                    <div class="panel-body">

                        <form action="javascript:;" method="POST" class="form-horizontal" role="form" onsubmit="send_pass()">
                            <div class="form-group">
                                <label for="inputPass" class="col-sm-2 control-label">Contraseña:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="pass" id="inputPass" class="form-control" required="required" autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-10 col-sm-offset-2">

                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" id="ocultar" onclick="toggleHidePass()">
                                            Ocultar
                                        </label>
                                    </div>

                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-10 col-sm-offset-2">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>

                            <div class="form-group hidden" id="res">
                                <div class="col-sm-10 col-sm-offset-2">
                                    <strong>Respuesta:</strong><br>
                                    <span></span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Encriptar texto</h3>
                    </div>
                    <div class="panel-body">

                        <form action="javascript:;" method="POST" class="form-horizontal" role="form" onsubmit="send_text()">
                            <div class="form-group">
                                <label for="inputTexto" class="col-sm-2 control-label">Texto:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="etext" id="inputTexto" class="form-control" required="required" autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputKey" class="col-sm-2 control-label">Key:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="ekey" id="inputKey" class="form-control" required="required" autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-10 col-sm-offset-2">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" id="hidekey" onclick="toggleHideKey()">
                                            Ocultar textos
                                        </label>
                                    </div>

                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-10 col-sm-offset-2">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>

                            <div class="form-group hidden" id="reskey">
                                <div class="col-sm-10 col-sm-offset-2">
                                    <strong>Respuesta:</strong><br>
                                    <span></span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Desencriptar texto</h3>
                    </div>
                    <div class="panel-body">

                        <form action="javascript:;" method="POST" class="form-horizontal" role="form" onsubmit="send_text_un()">
                            <div class="form-group">
                                <label for="inputTexto" class="col-sm-2 control-label">Texto:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="untext" id="inputTexto" class="form-control" required="required" autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputKey" class="col-sm-2 control-label">Key:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="unkey" id="inputKey" class="form-control" required="required" autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-10 col-sm-offset-2">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" id="hideunkey" onclick="toggleHideUnKey()">
                                            Ocultar textos
                                        </label>
                                    </div>

                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-10 col-sm-offset-2">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>

                            <div class="form-group hidden" id="resunkey">
                                <div class="col-sm-10 col-sm-offset-2">
                                    <strong>Respuesta:</strong><br>
                                    <span></span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            function send(method, url, data, callback) {
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if(this.readyState != 4 || this.status != 200) return;

                    callback(this.responseText);
                };

                xhr.open(method, url, true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.send(data);
            }

            function send_post(url, data, callback) {
                send("POST", url, data, callback);
            }

            function send_pass() {
                var pass = document.querySelector("[name=pass]").value.trim();
                if(pass == "") {
                    return;
                }

                var data = "pass=" + encodeURIComponent(pass);
                send_post("?", data, function(data) {
                    var cont = document.querySelector("#res");
                    cont.className = "form-group";
                    cont.querySelector("span").innerHTML = data;
                });
            }

            function send_text() {
                var text = document.querySelector("[name=etext]").value.trim();
                var pass = document.querySelector("[name=ekey]").value.trim();
                if(text == "" || pass == "") {
                    return;
                }

                var data = "";
                data += "etext=" + encodeURIComponent(text) + "&";
                data += "ekey=" + encodeURIComponent(pass) + "&";

                send_post("?", data, function(data) {
                    var cont = document.querySelector("#reskey");
                    cont.className = "form-group";
                    cont.querySelector("span").innerHTML = data;
                });
            }

            function send_text_un() {
                var text = document.querySelector("[name=untext]").value.trim();
                var pass = document.querySelector("[name=unkey]").value.trim();
                if(text == "" || pass == "") {
                    return;
                }

                var data = "";
                data += "untext=" + encodeURIComponent(text) + "&";
                data += "unkey=" + encodeURIComponent(pass) + "&";

                send_post("?", data, function(data) {
                    var cont = document.querySelector("#resunkey");
                    cont.className = "form-group";
                    cont.querySelector("span").innerHTML = data;
                });
            }

            function toggleHidePass() {
                var e = document.querySelector("#ocultar");
                var t = document.querySelector("[name=pass]");

                t.type = e.checked ? "password" : "text";
            }

            function toggleHideKey() {
                var e = document.querySelector("#hidekey");
                var t = document.querySelector("[name=etext]");
                var p = document.querySelector("[name=ekey]");

                t.type = e.checked ? "password" : "text";
                p.type = e.checked ? "password" : "text";
            }

            function toggleHideUnKey() {
                var e = document.querySelector("#hideunkey");
                var t = document.querySelector("[name=untext]");
                var p = document.querySelector("[name=unkey]");

                t.type = e.checked ? "password" : "text";
                p.type = e.checked ? "password" : "text";
            }

            toggleHidePass();
            toggleHideKey();
            toggleHideUnKey();
        </script>
    </body>
</html>
