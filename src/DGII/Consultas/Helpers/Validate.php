<?php
namespace DGII\Consultas\Helpers;

class Validate
{
  private $rnc;

  /**
   * Validate RNC format.
   *
   * @return boolean
   */
  public function validateRnc($rnc) {
    $length = strlen($rnc);
    $number = is_numeric($rnc);

    // echo "Number: " . $number;
    // echo "Length: " . $length;

    if ($number && ($length == 9 || $length == 11)) {
      return true;
    }

    return false;
  }
}
