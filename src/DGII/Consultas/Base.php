<?php

namespace DGII\Consultas;

use DGII\Consultas\Helpers\Config;
use DGII\Consultas\Helpers\Validate;

class Base
{
  public $dataJson;
  public $url;
  public $contentType;
  public $validator;

  public function __construct()
  {
    $config = new Config();

    $this->dataJson = $config->dataJson;
    $this->validator = new Validate();
  }
}
