<?php

namespace Notamedia\i18n\Iblock\Exception;

use Notamedia\i18n\Iblock\Exception;

class InternationalizeException extends \Exception
{
    protected $errors = [];
    
    public function __construct(array $errors = [], $code = 0, Exception $previous = null)
    {
        $this->errors = $errors;
        
        parent::__construct('Error converting the information block: ' . implode(';', $errors), $code, $previous);
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
}