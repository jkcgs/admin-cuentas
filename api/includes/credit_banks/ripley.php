<?php defined("INCLUDED") or die("Denied"); 
require_once "includes/base_bank.php";
use PHPHtmlParser\Dom;

class Credit_Ripley extends Bank {
    protected $url_prefix = "https://www.tarjetaripley.cl/tarjeta/";
    protected $bank_name = "Ripley";

    function login() {
        $data = array(
            'rut' => $this->user,
            'password' => $this->pass
        );

        $url_login = $this->url_prefix . "login.do";
        $result = $this->curl->post($url_login, $data);

        return strpos($result, "\"logueado\" : true") !== false;
    }

    function logout() {
        $url_logout = $this->url_prefix . "cerrarSesion.do";
        $res = $this->curl->get($url_logout);
        $this->curl->close();

        return !!$res;
    }

    function getAccounts($autologin = false) {
        if($autologin && !$this->login()) {
            return false;
        }

        $url_movimientos = $this->url_prefix . "movimientos/movimientos.do";
        $movimientos = $this->curl->get($url_movimientos);

        if(!$movimientos || !strpos($movimientos, "&nbsp;/ Movimientos")) {
            return false;
        }

        // Parsear página buscando movimientos
        $dom = new Dom;
        $dom->load($movimientos);

        $cuentasPags = $dom->getElementsByClass('datos_no_facturados');
        $cuentas = [];

        foreach ($cuentasPags as $pag) {
            $cols = $pag->find('tbody tr');
            foreach ($cols as $cuenta) {
                $conts = $cuenta->find('td');
                if (count($conts) < 1) {
                    continue;
                }

                $contf = [];
                foreach ($conts as $cont) {
                    $contf[] = str_replace("&nbsp;", "", $cont->text);
                }

                $nc = [
                    'fecha' => trim($contf[0]),
                    'comercio' => trim($contf[1]),
                    'monto' => intval(preg_replace("/[\$\.]/", "", $contf[2])),
                    'cuotas' => intval(trim($contf[3])),
                    'valor' => intval(preg_replace("/[\$\.]/", "", $contf[4])),
                    'documento' => trim($contf[5])
                ];

                $cuentas[] = $nc;
            }
        }

        $data = [
            "name" => $this->bank_name,
            "accounts" => array_reverse($cuentas)
        ];

        // Saldos
        $url_saldos = $this->url_prefix . "saldo/saldo.do?_r=550432025871";
        $cont_saldos = $this->curl->get($url_saldos);

        if($cont_saldos && strpos($cont_saldos, "&nbsp;/ Saldo")) {
            $dom = new Dom;
            $dom->load($cont_saldos);

            $tds = $dom->find("table")[0]->find("tr")[1]->find("td");
            $saldos = [
                "balanceUsed" => intval(preg_replace("/[^0-9]/", "", $tds[0]->text)),
                "balanceAvailable" => intval(preg_replace("/[^0-9]/", "", $tds[1]->text))
            ];

            $data = array_merge($data, $saldos);
        }

        $this->logout();
        return [$data];
    }
}