<?php

namespace Notamedia\i18n\Iblock\Converter;

use Bitrix\Main\ArgumentTypeException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Notamedia\i18n\Iblock\Exception\InternationalizeException;
use Notamedia\i18n\Iblock\i18nTable;

/**
 * Abstract converter info blocks to i18n.
 */
abstract class Converter
{
    /**
     * @var int
     */
    private $iblockId;

    /**
     * Converter constructor.
     * 
     * @param int $iblockId ID of info block.
     * 
     * @throws ArgumentTypeException
     * @throws LoaderException
     */
    public function __construct($iblockId)
    {
        $this->iblockId = intval($iblockId);

        if ($this->iblockId <= 0) {
            throw new ArgumentTypeException('iblockId', 'int');
        }
        
        if (!Loader::includeModule('iblock')) {
            throw new LoaderException('Module "iblock" in not install');
        }
        
        Loc::loadMessages(__FILE__);
    }

    /**
     * Internationalize info block.
     * 
     * @param string $propCodePublicId
     * @param string $propCodeLang
     * @param string $defaultLang
     * 
     * @throws InternationalizeException
     */
    public function internationalize($propCodePublicId, $propCodeLang, $defaultLang)
    {
        $saveResult = i18nTable::save([
            'IBLOCK_ID' => $this->getIblockId(),
            'PROP_CODE_PUBLIC_ID' => $propCodePublicId,
            'PROP_CODE_LANG' => $propCodeLang
        ]);

        if (!$saveResult->isSuccess()) {
            throw new InternationalizeException($saveResult->getErrorMessages());
        }
    }

    /**
     * Fill the data items of internationalization: publick ID and language.
     * 
     * @param string|null $propCodePublicId
     * @param string|null $propCodeLang
     * @param string|null $defaultLang
     * @param bool $force
     */
    abstract public function fill($propCodePublicId = null, $propCodeLang = null, $defaultLang = null, $force = false);

    /**
     * Gets ID of the info block.
     * 
     * @return int
     */
    public function getIblockId()
    {
        return $this->iblockId;
    }
}