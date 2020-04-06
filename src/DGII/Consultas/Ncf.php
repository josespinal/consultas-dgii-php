<?php
namespace DGII\Consultas;

class Ncf extends Base
{
    private $anchor;
    private $rncField;
    private $ncfField;

    public function __construct()
    {
        parent::__construct();

        $this->url = $this->dataJson['ncf']['url'].'/'.$this->dataJson['ncf']['web_resource'];
        $this->anchor = $this->dataJson['ncf']['request_parameters']['formAnchor'];
        $this->rncField = $this->dataJson['ncf']['request_parameters']['rncFieldName'];
        $this->ncfField = $this->dataJson['ncf']['request_parameters']['ncfFieldName'];
    }

    private function getResource(string $rnc, string $ncf)
    {
        $client = new \Goutte\Client();

        $crawler = $client->request('GET', $this->url);
        $form = $crawler->selectButton($this->anchor)->form();
        $crawler = $client->submit($form, [$this->rncField => $rnc, $this->ncfField => $ncf]);

        $data = [];
        $crawler->filter('table.detailview tr')->each(function ($node) use (&$data) {
            $data[] = $node->filter('td')->text();
        });

        // Keys for data
        $keys = [
            'rnc',
            'nombre',
            'tipo_ncf',
            'ncf',
            'estatus',
            'fecha_valido',
        ];

    # Add keys to data
    if (count($data) > 0) {
      $data = array_combine($keys, $data);
    } else {
      $data = false;
    }

    # Return JSON
    return json_encode($data);
  }

  public function getNCF($rnc, $ncf)
  {
    $validation = $this->validator->validate($rnc, $ncf, true, true);

    if (array_key_exists('errors', $validation)) {
      return json_encode( $validation );
    }

    return $this->getResource($rnc, $ncf);
  }
}
