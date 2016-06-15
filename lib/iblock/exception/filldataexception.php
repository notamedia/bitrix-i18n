<?php

namespace Notamedia\i18n\Iblock\Exception;

use Notamedia\i18n\Iblock\Exception;

class FillDataException extends \Exception
{
    public function __construct($itemId, $iblockId, $message = null, $code = 0, Exception $previous = null)
    {
        if ($message) {
            $message = '. ' . $message;
        }
        
        parent::__construct(
            'Error filling item information block. Item #' . $itemId . ', info block #' . $iblockId . $message, 
            $code, 
            $previous
        );
    }
}