<?php

namespace DGII\Consultas\Helpers;

use DGII\Consultas\Helpers\Config;

class Validate
{
  /**
   * Validate RNC format.
   *
   * @return boolean
   */
  public static function validateRnc($rnc) {
    $length = strlen($rnc);
    $number = is_numeric($rnc);

    if ($number && ($length == 9 || $length == 11)) {
        return true;
    }

    return false;
  }

  /**
   * Validate NCF format.
   *
   * @return boolean
   */
  public static function validateNcf($ncf) {
    $length = strlen($ncf);
    $firstChar = $ncf[0];

    if ($length == 11 && !is_numeric($firstChar)) {
      return true;
    }

    return false;
  }

  /**
   * Validate.
   *
   * @return boolean
   */
  public function validate($rnc, $ncf = 0, $validateRnc = true, $validateNcf = false) {
    $errors = [];
    $valid = true;
    $config = new Config;
    $configData = $config->dataJson;

    if ($validateNcf == true) {
      $valid = false;

      if ($this->validateNcf($ncf) == false) {
        $errors['errors'] = $configData{'ncf'}{'not_valid_string'};
      }
    }

    return ($valid == true) ? true : $errors;
  }
}
