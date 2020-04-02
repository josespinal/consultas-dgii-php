<?php
namespace DGII\Consultas\Helpers;

class Config
{
  public $fileName = __DIR__.'/../../../../config.json';
  public $dataJson;

  public function __construct()
  {
    if (!file_exists($this->fileName)) {
      die('El archivo config no existe');
    }

    $handle = fopen($this->fileName, 'r');
    $this->dataJson = json_decode(fread($handle, filesize($this->fileName)), true);
    fclose($handle);
  }
}
