
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

    	return (float)(str_replace(',','.',end($arrResult)[1]));
	}

	private function getData()
	{
		return fopen($this->dominio . $this->url, "r");
	}
}

?>