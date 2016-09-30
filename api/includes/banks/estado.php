<?php defined("INCLUDED") or die("Denied"); try_logged();

require_once __DIR__ . "/../base_bank.php";
use PHPHtmlParser\Dom;

class Bank_Estado extends Bank {
    protected $bank_name = "Banco Estado";
    private $url_prefix = "https://bancapersonas.bancoestado.cl/eBankingBech/";

    function login() {
        $loginform = $this->url_prefix . "login/login.htm";
        $data = $this->curl->get($loginform);

        if(!$data) {
            throw new TemporalError("No se pudo cargar los datos remotos");
        }

        if(!strpos($data, "BancoEstado Login")) {
            throw new \Exception("Página incorrecta recibida");
        }

        $token = text_find($data, 'ctoken" value="', '"');
        $form_data = [
            "j_username" => $this->user,
            "j_password" => $this->pass,
            "ctoken" => $token
        ];

        $loginep = $this->url_prefix . "login";
        $data = $this->curl->post($loginep, $form_data);
        if(!$data) {
            throw new \Exception("No se pudo enviar el formulario para iniciar sesión");
        }

        if(!strpos($data, "Home - BUILD")) {
            throw new \Exception("No se pudo iniciar sesión");
        }

        return true;
    }

    function getAccounts() {
        if(!$this->login()) {
            return false;
        }

        $data = $this->curl->get($this->url_prefix . "superCartola/superCartola.htm");
        if(!$data || !strpos($data, "<title>Resumen de Productos")) {
            // Página incorrecta recibida
            throw new \Exception("Wrong page received");
        }

        $dom = new Dom;
        $dom->load($data);
        $rows = $dom->find("#table_1 tbody tr");
        $cuentas = [];

        foreach ($rows as $cuenta) {
            $conts = $cuenta->find('td');
            if (count($conts) < 1) {
                continue;
            }

            $contf = [];

            for ($i = 0; $i < count($conts); $i++) {
                $cont = $conts[$i];
                if($i == 0) {
                    $cont = $cont->find("span", 0);
                }

                $contf[] = str_replace("&nbsp;", "", $cont->text);
            }

            $nc = [
                "type" => trim($contf[0]),
                "number" => trim($contf[1]),
                "balance" => intval(preg_replace("/[\$\.]/", "", $contf[3])),
                "bank" => $this->bank_name
            ];

            $cuentas[] = $nc;
        }

        return $cuentas;
    }

    function logout() {
        return !!$this->curl->get($this->url_prefix . "seguridad/logoutIN.htm");
    }
}
