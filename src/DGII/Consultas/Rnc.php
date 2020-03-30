<?php
namespace DGII\Consultas;

use DomDocument;
use DomXpath;
use DGII\Consultas\Helpers\Validate;

class Rnc
{
	private $fileName = __DIR__.'/../../../config.json';
	private $dataJson;
	private $url;
	private $contentType;
	private $validator;

	public function __construct()
	{
		if (!file_exists($this->fileName))
			die('El archivo config no existe');

		$handle = fopen($this->fileName, 'r');
		$this->dataJson = json_decode(fread($handle, filesize($this->fileName)), true);
		fclose($handle);

		$this->contentType = $this->dataJson{'request_headers'}{'Content-Type'};
		$this->url = $this->dataJson{'url'} . '/' . $this->dataJson{'web_resource'};
		$this->validator = new Validate();
	}

	private function getResource($query)
	{
		$fieldStr = '';
		$fields = '';
		$this->dataJson{'request_parameters'}{'txtRncCed'} = $query;

		foreach ($this->dataJson{'request_parameters'} as $key => $value) {
			$fieldStr .= $key . '=' . $value . '&';
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_POST, count(explode('&', $fieldStr)));
		curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim($fieldStr, '&'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$schemeHtml = curl_exec($ch);
		$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if($httpStatus != 200)
			return json_encode( array( 'error_message' => $this->dataJson{'http_error_string'}));

		$dom = new DomDocument();
		$dom->loadHtml($schemeHtml);
		$xpath = new DomXpath($dom);

		$tr = $xpath->query('//span[@id="lblMsg"]')->item(0);

		if ($tr->textContent)
			return json_encode( array( 'error_message' => $this->dataJson{'not_found_string'}));

		$tr = $xpath->query('//tr[@class="GridItemStyle"]')->item(0);

		foreach ($tr->childNodes as $node) {
			$rncValue[] = $node->nodeValue;
		}

		if (!headers_sent()) {
			header('Content-type: text/json');
			header('Content-type: application/json');
		}

		$keys = array(
			'rnc',
			'nombre',
			'nombre_comercial',
			'categoria',
			'regimen',
			'estatus'
		);

		# Clean the data received before combining with keys
		array_pop($rncValue);
		unset($rncValue[0]);

		# Add keys to data
		$rncValue = array_combine($keys, $rncValue);

		# Return JSON
		return json_encode($rncValue, JSON_FORCE_OBJECT);
	}

	public function getByRNC($rnc)
	{
		if (!$this->validator->validateRnc($rnc)){
			return json_encode( array( 'error_message' => $this->dataJson{'not_valid_string'}));
		}

		return $this->getResource($rnc);
	}
}
