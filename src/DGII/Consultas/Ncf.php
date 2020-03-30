<?php
namespace DGII\Consultas;

use DGII\Consultas\Base;
use DGII\Consultas\Helpers\Validate;
use Goutte\Client;

class Ncf extends Base
{
  private $anchor;
  private $rncField;
  private $ncfField;

  public function __construct()
  {
    parent::__construct();

    $this->url = $this->dataJson{'ncf'}{'url'} . '/' . $this->dataJson{'ncf'}{'web_resource'};
    $this->anchor = $this->dataJson{'ncf'}{'request_parameters'}{'formAnchor'};
    $this->rncField = $this->dataJson{'ncf'}{'request_parameters'}{'rncFieldName'};
    $this->ncfField = $this->dataJson{'ncf'}{'request_parameters'}{'ncfFieldName'};
  }

  private function getResource(string $rnc, string $ncf)
	{
    $client = new \Goutte\Client();

    $crawler = $client->request('GET', $this->url);
    $form = $crawler->selectButton($this->anchor)->form();
    $crawler = $client->submit($form, array($this->rncField => $rnc, $this->ncfField => $ncf));

    $data = [];
    $crawler->filter('table.detailview tr')->each(function ($node) use (&$data) {
        $data[] = $node->filter('td')->text();
    });

    # Keys for data
    $keys = array(
			'rnc',
			'nombre',
			'tipo_ncf',
			'ncf',
			'estatus',
			'fecha_valido'
    );

    # Add keys to data
		$data = array_combine($keys, $data);

    # Return JSON
    return json_encode($data);
  }

  public function getNCF($rnc, $ncf)
	{
		if (!$this->validator->validateRnc($rnc)){
			return json_encode( array( 'error_message' => $this->dataJson{'not_valid_string'}));
		}

		return $this->getResource($rnc, $ncf);
	}
}
