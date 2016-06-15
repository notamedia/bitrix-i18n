<?php

namespace Notamedia\i18n\Iblock\Converter;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\LanguageTable;
use Bitrix\Main\Localization\Loc;
use Notamedia\i18n\Iblock\Exception\InternationalizeException;

/**
 * Manager of converting info blocks.
 */
class IblockManager
{
    /**
     * @var Converter[]
     */
    protected $converters = [];
    /**
     * @var \Bitrix\Main\DB\Connection
     */
    protected $connection;

    /**
     * IblockManager constructor.
     * 
     * @param int $iblockId ID of info block.
     */
    public function __construct($iblockId)
    {
        $this->converters = [
            new ElementConverter($iblockId),
            new SectionConverter($iblockId)
        ];

        $this->connection = Application::getConnection();
    }

    /**
     * Convert the info block to i18n.
     * 
     * @param string $propCodePublicId
     * @param string $propCodeLang
     * @param string $defaultLang
     * 
     * @throws InternationalizeException
     * @throws \Exception
     */
    public function convert($propCodePublicId, $propCodeLang, $defaultLang)
    {
        $rsDefaultLang = LanguageTable::query()
            ->setFilter(['LID' => $defaultLang])
            ->exec();

        if ($rsDefaultLang->getSelectedRowsCount() <= 0) {
            throw new InternationalizeException([Loc::getMessage('NOTAMEDIA_I18N_IMANAGER_NOT_FOUND_LANG')]);
        }

        foreach ($this->converters as $converter) {
            $this->connection->startTransaction();
            
            try {
                $converter->internationalize($propCodePublicId, $propCodeLang, $defaultLang);
                $converter->fill($propCodePublicId, $propCodeLang, $defaultLang);
            } catch (\Exception $e) {
                $this->connection->rollbackTransaction();

                throw $e;
            }

            $this->connection->commitTransaction();
        }
    }
}