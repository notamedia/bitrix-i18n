<?php

namespace Notamedia\i18n\Iblock;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;

/**
 * Table of public ID for items of the info blocks.
 */
class i18nPublicIdTable extends DataManager
{
    public static function getTableName()
    {
        return 'notamedia_i18n_iblock_public_id';
    }

    public static function getMap()
    {
        return [
            new IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true
            ])
        ];
    }
}