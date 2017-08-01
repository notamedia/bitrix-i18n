<?php

namespace Notamedia\i18n\Iblock;

use Bitrix\Main\Localization\Loc;
use Notamedia\i18n\Iblock\Ui\InterfaceHelper;

Loc::loadMessages(__DIR__ . '/handler.php');

/**
 * Event handlers for elements of info blocks.
 */
class ElementHandler
{
    const ACTION_ADD = 'add';
    const ACTION_UPDATE = 'update';

    /**
     * @param array $fields Element fields.
     * @return bool
     * @throws \Exception
     */
    public static function onBeforeAdd(&$fields)
    {
        global $APPLICATION;

        $settings = InterfaceHelper::getIblockSettings($fields['IBLOCK_ID']);

        if ($settings === null) {
            return true;
        }

        if (!isset($fields['PROPERTY_VALUES'][$settings['PROP_ID_PUBLIC_ID']])) {
            $APPLICATION->ThrowException(Loc::getMessage('NOTAMEDIA_I18N_IBLOCK_HANDLER_ERROR_MISSING_PROP_PUBLIC_ID'));
            return false;
        } elseif (!isset($fields['PROPERTY_VALUES'][$settings['PROP_ID_LANG']])) {
            $APPLICATION->ThrowException(Loc::getMessage('NOTAMEDIA_I18N_IBLOCK_HANDLER_ERROR_MISSING_PROP_LANG'));
            return false;
        }

        if (empty($fields['PROPERTY_VALUES'][$settings['PROP_ID_PUBLIC_ID']]['n0']['VALUE'])) {
            $addResult = i18nPublicIdTable::add(['ID' => null]);

            if ($addResult->isSuccess()) {
                $fields['PROPERTY_VALUES'][$settings['PROP_ID_PUBLIC_ID']] = $addResult->getId();
            } else {
                $APPLICATION->ThrowException(Loc::getMessage('NOTAMEDIA_I18N_IBLOCK_HANDLER_ERROR_CREARE_PUBLIC_ID'));
                return false;
            }
        } elseif (static::checkDoubleElement($fields, $settings, static::ACTION_ADD)) {
            return false;
        }
    }

    /**
     * @param array $fields Element fields.
     * @return bool
     */
    public static function onBeforeUpdate(&$fields)
    {
        if ($settings = InterfaceHelper::getIblockSettings($fields['IBLOCK_ID'])) {
            if (static::checkDoubleElement($fields, $settings, static::ACTION_UPDATE)) {
                return false;
            }
        }
    }

    /**
     * Verify the existence of duplicate elements.
     * 
     * @param array $fields Element fields.
     * @param array $settings Internationalization settings of info block.
     * @param string $action Action name.
     * @return bool
     */
    protected static function checkDoubleElement(array $fields, $settings, $action)
    {
        global $APPLICATION;

        $propIdPublicId = InterfaceHelper::getPropIdByCode($fields['IBLOCK_ID'], $settings['PROP_CODE_PUBLIC_ID']);
        $propIdLang = InterfaceHelper::getPropIdByCode($fields['IBLOCK_ID'], $settings['PROP_CODE_LANG']);

        if ($fields['PROPERTY_VALUES'][$propIdPublicId]) {

            $tmpProperty = array_pop($fields['PROPERTY_VALUES'][$propIdPublicId]);
            $publicId    = $tmpProperty['VALUE'];
            $tmpProperty = array_pop($fields['PROPERTY_VALUES'][$propIdLang]);
            $langId      = $tmpProperty['VALUE'];
            unset($tmpProperty);

            $rsElement = \CIBlockElement::GetList(
                [],
                [
                    'IBLOCK_ID' => $fields['IBLOCK_ID'],
                    'PROPERTY_' . $settings['PROP_CODE_PUBLIC_ID'] => $publicId,
                    'PROPERTY_' . $settings['PROP_CODE_LANG'] => $langId,
                    '!ID' => $fields['ID']
                ],
                false,
                [
                    'nTopCount' => 1
                ],
                [
                    'ID',
                    'IBLOCK_ID'
                ]
            );

            if ($element = $rsElement->Fetch()) {
                $APPLICATION->ThrowException(Loc::getMessage('NOTAMEDIA_I18N_IBLOCK_HANDLER_ELEMENT_ALREADY_EXIST'));
                return true;
            }
        }

        return false;
    }
}