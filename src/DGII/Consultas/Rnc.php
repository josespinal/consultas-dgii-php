<?php
namespace DGII\Consultas;

use DGII\Consultas\Base;
use DomDocument;
use DomXpath;

class Rnc extends Base
{
	public function __construct()
	{
		parent::__construct();
		$this->url = $this->dataJson{'rnc'}{'url'} . '/' . $this->dataJson{'rnc'}{'web_resource'};
		$this->contentType = $this->dataJson{'rnc'}{'request_headers'}{'Content-Type'};
	}

	private function getResource($query)
	{
		$fieldStr = '';
		$fields = '';
		$this->dataJson{'rnc'}{'request_parameters'}{'txtRncCed'} = $query;

		foreach ($this->dataJson{'rnc'}{'request_parameters'} as $key => $value) {
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
