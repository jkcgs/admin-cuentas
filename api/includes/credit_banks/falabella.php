<?php defined("INCLUDED") or die("Denied"); 
require_once "includes/base_bank.php";
use PHPHtmlParser\Dom;

class Credit_Falabella extends Bank {
    protected $url_prefix = "https://www.cmrfalabella.com/b2cfapr/CMRCORP/logica/jsp/";
    protected $bank_name = "Falabella";

    function login() {
        $user_rut = substr($this->user, 0, -1);
        $user_div = substr($this->user, -1);

        $data = array(
            'NUMDOC' => $user_rut,
            'DV' => $user_div,
            'PINSEG' => $this->pass
        );

        $url_login = $this->url_prefix . "CMRCORPAFLoginAction.do?PAIS=CL";
        $result = $this->curl->post($url_login, $data);

        return strpos($result, "Mis Gastos") !== false;
    }

    function logout() {
        $url_logout = $this->url_prefix . "CMRCORPTFLogOff.jsp";
        $res = $this->curl->get($url_logout);
        $this->curl->close();

        return !!$res;
    }

    function getAccounts($autologin = false) {
        if($autologin && !$this->login()) {
            return false;
        }

        $url_movimientos = $this->url_prefix . "CMRCORPAFSaldosUltiMovsAction.do?TIPOTJTA=15";
        $movimientos = $this->curl->get($url_movimientos);

        if(!$movimientos || !strpos($movimientos, "Total del mes a pagar")) {
            return false;
        }

        // A alguien se le olvidó cerrar un <tr> y me jodía todo el asunto
        $movimientos = str_replace(
            "<span class='puntos-acumulados'></span></td>",
            "<span class='puntos-acumulados'></span></td></tr>",
            $movimientos
        );

        // Parsear página buscando movimientos
        $dom = new Dom;
        $dom->load($movimientos);

        $cuentasCont = $dom->getElementsByClass('tabla-compras')->find('tr');
        $cuentas = [];

        foreach ($cuentasCont as $cuenta) {
            $conts = $cuenta->find('td');
            if (count($conts) < 1 || !$cuenta->find('.fecha')) {
                continue;
            }

            $comercio = $this->byClass($cuenta, 'title');

            if(substr($comercio, 0, 8) !== "COMPRAS ") {
                continue;
            }

            $comercio = substr($comercio, 8);
            $fecha = explode("/", $this->byClass($cuenta, 'fecha'));

            $nc = [
                'fecha' => $fecha[2] . '-' . $fecha[1] . '-' . $fecha[0],
                'comercio' => $comercio,
                'monto' => intval(preg_replace("/[\$\.]/", "", $this->byClass($cuenta, 'valor-compra'))),
                'cuotas' => intval($this->byClass($cuenta, 'cuotas')),
                'valor' => intval(preg_replace("/[\$\.]/", "", $this->byClass($cuenta, 'valor-cuota'))),
                'documento' => trim($cuenta->find('[class=dato]')[0]->text)
            ];

            $cuentas[] = $nc;
        }

        $data = [
            "name" => $this->bank_name,
            "accounts" => $cuentas,
            "user" => $this->user
        ];

        // Saldos
        $url_saldos = $this->url_prefix . "CMRCORPFFMisTarjetas.jsp?TIPOTJTA=15";
        $cont_saldos = $this->curl->get($url_saldos);

        if($cont_saldos && strpos($cont_saldos, "Disponible en compras")) {
            $dom = new Dom;
            $dom->load($cont_saldos);

            $disponible = $dom->find("[class=disponible_compra]")[3]->find("strong")[1]->text;
            $utilizado = $dom->find("[class=cupo_utilizado]")[3]->find("strong")[1]->text;
            $saldos = [
                "balanceUsed" => intval(preg_replace("/[^0-9]/", "", $utilizado)),
                "balanceAvailable" => intval(preg_replace("/[^0-9]/", "", $disponible))
            ];

            $data = array_merge($data, $saldos);
        }
        
        $this->logout();
        return [$data];
    }

    private function byClass($element, $className) {
        $cont = $element->find("[class=$className]");
        return count($cont) > 0 ? trim($cont->text) : '';
    }
}