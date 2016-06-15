<?php

namespace Notamedia\i18n\Iblock\Ui;

use Bitrix\Main\Localization\LanguageTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Property with select box of language for sections of info blocks.
 */
class LangProperty
{
    const USER_TYPE = 'NOTAMEDIA_I18N_LANG';
    
    public static function getUserTypeDescription()
    {
        return [
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => static::USER_TYPE,
            'DESCRIPTION' => Loc::getMessage('NOTAMEDIA_I18N_PROP_LANG'),
            'GetAdminListViewHTML' => [get_called_class(), 'getAdminListViewHTML'],
            'GetPropertyFieldHtml' => [get_called_class(), 'getPropertyFieldHtml'],
            'GetPropertyFieldHtmlMulty' => [get_called_class(), 'getPropertyFieldHtmlMulty'],
            'GetPublicViewHTML' => [get_called_class(), 'getPublicViewHTML']
        ];
    }

    public static function getAdminListViewHTML($property, $value, $strHTMLControlName)
    {
        $rsLang = LanguageTable::query()
            ->setFilter(['LID' => $value['VALUE']])
            ->setSelect(['NAME'])
            ->exec();
        
        if ($lang = $rsLang->fetch()) {
            return $lang['NAME'];
        }
    }

    public static function getPropertyFieldHtml($property, $value, $strHTMLControlName)
    {
        return InterfaceHelper::getLangFieldHtml($strHTMLControlName['VALUE'], $value['VALUE'], InterfaceHelper::MODEL_ELEMENT);
    }

    public static function getPropertyFieldHtmlMulty($property, $value, $strHTMLControlName)
    {
        return static::getPropertyFieldHtml($property, $value, $strHTMLControlName);
    }
}