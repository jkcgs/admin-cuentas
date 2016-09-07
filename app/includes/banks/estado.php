<?php defined("INCLUDED") or die("Denied"); try_logged();
require_once("app/includes/base_bank.php");

class Estado extends Bank {
    protected $bank_name = "Banco Estado";

    private $url_prefix = "https://bancapersonas.bancoestado.cl/eBankingBech/";
    private $url_resume = "superCartola/superCartola.htm";
    private $url_home = "home/home.htm";

    function login() {
        $loginform = $this->url_prefix . "login/login.htm";
        $data = $this->curl->get($loginform);

        if(!$data) {
            throw new \Exception("No se pudo cargar los datos remotos");
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

        $this->logout();
        throw_data($data);
    }

    function getAccounts() {
        throw new \Exception("Not implemented");
    }

    function logout() {
        return !!$this->curl->get($this->url_prefix . "seguridad/logoutIN.htm");
    }
}
