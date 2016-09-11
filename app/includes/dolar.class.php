
<?php 

/**
* Código para obtener el precio del dolar desde la Bolsa Electrónica de Santiago
* Basado en: https://gist.github.com/isseu/8099933
*/
class DolarAPI
{
	private $dominio = "http://www.bolchile.cl";
	private $url = "/portlets/Dolar2Portlet/XmlApiGraf?idioma=es&intervalo=60&periodo=HOY&mayorA10=true";

	function getCurrent()
	{
		$datos_cvs = $this->getData();
		$arrResult = [];
		while (($data = fgetcsv($datos_cvs, 1000, ";")) !== FALSE) {
      		$arrResult[] = $data;
    	}

    	$val = (float)(str_replace(',','.',end($arrResult)[1]));
		return $val == 0 ? $this->getFromSite() : $val;
	}

	private function getData()
	{
		return fopen($this->dominio . $this->url, "r");
	}

	private function getFromSite() {
		$site = file_get_contents("http://www.bolchile.cl/Dolar?menu=DOLAR");
		if(empty(trim($site))) {
			return 0;
		}

		// Encontrar el texto con el valor
		$init = strpos($site, 'id="ultimoPrecio"');
		$d_init = strpos($site, ">", $init) + 1;
		$d_end = strpos($site, "<", $d_init) - $d_init;

		$val = substr($site, $d_init, $d_end);
		return floatval(str_replace(",", ".", $val));
	}
}
