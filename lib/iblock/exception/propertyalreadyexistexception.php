<?php

namespace Notamedia\i18n\Iblock\Exception;

use Notamedia\i18n\Iblock\Exception;

class PropertyAlreadyExistException extends \Exception
{
    public function __construct($property, $code = 0, Exception $previous = null)
    {
        parent::__construct('Property ' . $property . ' already exist', $code, $previous);
    }
}