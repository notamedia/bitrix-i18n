<?php

namespace Notamedia\i18n\Iblock\Ui;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Users field for public ID of sections of the info blocks.
 */
class PublicIdProperty
{
    const USER_TYPE = 'NOTAMEDIA_I18N_PUBLIC_ID';
    
    public static function getUserTypeDescription()
    {
        return [
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => static::USER_TYPE,
            'DESCRIPTION' => Loc::getMessage('NOTAMEDIA_I18N_PROP_PUBLIC_ID'),
            'GetAdminListViewHTML' => [get_called_class(), 'getAdminListViewHTML'],
            'GetPropertyFieldHtml' => [get_called_class(), 'getPropertyFieldHtml'],
            'GetPropertyFieldHtmlMulty' => [get_called_class(), 'getPropertyFieldHtmlMulty'],
            'GetPublicViewHTML' => [get_called_class(), 'getPublicViewHTML']
        ];
    }

    public static function getAdminListViewHTML($property, $value, $strHTMLControlName)
    {
        return $value['VALUE'];
    }

    public static function getPropertyFieldHtml($property, $value, $strHTMLControlName)
    {
        $request = Application::getInstance()->getContext()->getRequest();
                
        if (empty($value['VALUE']) && $request->get('I18N_RELATED_ID')) {
            $rsRelatedElement = \CIBlockElement::GetList(
                [],
                [
                    'IBLOCK_ID' => $property['IBLOCK_ID'],
                    'ID' => $request->get('I18N_RELATED_ID')
                ],
                false,
                false,
                [
                    'ID',
                    'IBLOCK_ID',
                    'PROPERTY_' . $property['CODE']
                ]
            );
            
            if ($relatedElement = $rsRelatedElement->Fetch()) {
                $value['VALUE'] = $relatedElement['PROPERTY_' . $property['CODE'] . '_VALUE'];
            }
        }
        
        return $value['VALUE'] . '<input type="hidden" name="' . $strHTMLControlName['VALUE'] . '" value="' . $value['VALUE'] . '">';
    }

    public static function getPropertyFieldHtmlMulty($property, $value, $strHTMLControlName)
    {
        return static::getPropertyFieldHtml($property, $value, $strHTMLControlName);
    }
}