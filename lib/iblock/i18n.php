<?php

namespace Notamedia\i18n\Iblock;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\DatetimeField;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;

/**
 * Table of internationalization settings of info block.
 */
class i18nTable extends DataManager
{
    public static function getTableName()
    {
        return 'notamedia_i18n_iblock';
    }

    public static function getMap()
    {
        return [
            new IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true
            ]),
            new IntegerField('IBLOCK_ID'),
            new StringField('PROP_CODE_PUBLIC_ID'),
            new StringField('PROP_CODE_LANG'),
            new DatetimeField('TIMESTAMP_X')
        ];
    }

    /**
     * Create or update item.
     * 
     * @param array $data
     * @return \Bitrix\Main\Entity\AddResult|\Bitrix\Main\Entity\UpdateResult
     * @throws \Exception
     */
    public static function save(array $data)
    {
        $rsSetting = static::query()
            ->setFilter(['IBLOCK_ID' => $data['IBLOCK_ID']])
            ->setSelect(['ID'])
            ->exec();

        if ($setting = $rsSetting->fetch()) {
            return static::update($setting, $data);
        } else {
            return static::add($data);
        }
    }
}