<?php defined("INCLUDED") or die("Denied"); try_logged();

require_once __DIR__ . "/../base_bank.php";
use PHPHtmlParser\Dom;

class Bank_Santander extends Bank {
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

        $data = $this->curl->get($this->url_prefix . "transa/productos/saldoC/SaldoCnvo/saldoc.asp");
        $dom = new Dom;
        $dom->load($data);

        if(!$data || !strpos($data, "Saldos Consolidados")) {
            // Página incorrecta recibida
            throw new \Exception("Wrong page received");
        }

        $cuentas_resp = [];
        $dom_rows = $dom->find("table table")[0]->find("tr");

        for($i = 3; $i < count($dom_rows); $i++) {
            $tds = $dom_rows[$i]->find("td");

            $cuentas_resp[] = [
                "type" => trim($tds[0]->text),
                "number" => trim(str_replace("-", "", $tds[1]->text)),
                "balance" => intval(trim($tds[4]->text)),
                "bank" => trim($this->bank_name)
            ];
        }

        return $cuentas_resp;
    }

    function logout() {
        return !!$this->curl->get($this->url_prefix . "seguridad/logoutIN.htm");
    }
}
