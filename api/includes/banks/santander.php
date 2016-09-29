<?php defined("INCLUDED") or die("Denied"); try_logged();
require_once("includes/base_bank.php");

class Santander extends Bank {
    protected $bank_name = "Banco Santander";

    private $url_prefix = "https://www.santander.cl/";

    function login() {
        $loginep = $this->url_prefix . "transa/cruce.asp";
        $form_data = [
            "rut" => $this->user,
            "pin" => $this->pass
        ];

        $data = $this->curl->post($loginep, $form_data);
        if(!$data) {
            throw new \Exception("No se pudo enviar el formulario para iniciar sesión");
        }

        if(!strpos($data, "actualiza_area_trabajo")) {
            throw new \Exception("No se pudo iniciar sesión");
        }

        return true;
    }

    function getAccounts() {
        if(!$this->login()) {
            return false;
        }

        $data = $this->curl->get($this->url_prefix . "transa/productos/tt/mis_productos/miscuentas_inicio-v2.asp");
        if(!$data || !strpos($data, "name='numcuenta'")) {
            // Página incorrecta recibida
            throw new \Exception("Wrong page received");
        }

        $cuentas = [];
        preg_match_all("/pasacuenta\(.*\)/", $data, $cuentas);
        for($i = 0; $i < count($cuentas); $i++) {
            if(!isset($cuentas[$i][0])) {
                continue;
            }

            $pasacuenta = str_replace("'", "\"", text_find($cuentas[$i][0], "pasacuenta(", ")"));
            $decoded = json_decode("[" . $pasacuenta . "]");

            $cuentas[$i] = [
                "type" => trim($decoded[2]),
                "number" => trim($decoded[0]),
                "balance" => $decoded[3],
                "bank" => $this->bank_name
            ];
        }

        return $cuentas;
    }

    function logout() {
        return !!$this->curl->get($this->url_prefix . "seguridad/logoutIN.htm");
    }
}
