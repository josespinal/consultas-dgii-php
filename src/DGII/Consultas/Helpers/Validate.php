<?php

namespace DGII\Consultas\Helpers;

class Validate
{
    private $rnc;

    /**
     * Validate RNC format.
     *
     * @return bool
     */
    public function validateRnc($rnc)
    {
        $length = strlen($rnc);
        $number = is_numeric($rnc);

        if ($number && ($length == 9 || $length == 11)) {
            return true;
        }

        return false;
    }
}
