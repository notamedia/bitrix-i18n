<?php

namespace Notamedia\i18n\Iblock;

use Bitrix\Main\Localization\Loc;
use Notamedia\i18n\Iblock\Ui\InterfaceHelper;

Loc::loadMessages(__DIR__ . '/handler.php');

/**
 * Event handlers for sections of info blocks.
 */
class SectionHandler
{
    /**
     * @param array $fields Section fields.
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

        if (!isset($fields['UF_' . $settings['PROP_CODE_PUBLIC_ID']])) {
            $APPLICATION->ThrowException(Loc::getMessage('NOTAMEDIA_I18N_IBLOCK_HANDLER_ERROR_MISSING_PROP_PUBLIC_ID'));
            return false;
        } elseif (!isset($fields['UF_' . $settings['PROP_CODE_LANG']])) {
            $APPLICATION->ThrowException(Loc::getMessage('NOTAMEDIA_I18N_IBLOCK_HANDLER_ERROR_MISSING_PROP_LANG'));
            return false;
        }

        if (empty($fields['UF_' . $settings['PROP_CODE_PUBLIC_ID']])) {
            $addResult = i18nPublicIdTable::add(['ID' => null]);

            if ($addResult->isSuccess()) {
                $fields['UF_' . $settings['PROP_CODE_PUBLIC_ID']] = $addResult->getId();
            } else {
                $APPLICATION->ThrowException(Loc::getMessage('NOTAMEDIA_I18N_IBLOCK_HANDLER_ERROR_CREARE_PUBLIC_ID'));
                return false;
            }
        } elseif (static::checkDoubleSection($fields, $settings)) {
            return false;
        }
    }

    /**
     * @param array $fields Section fields.
     * @return bool
     */
    public static function onBeforeUpdate(&$fields)
    {
        if ($settings = InterfaceHelper::getIblockSettings($fields['IBLOCK_ID'])) {
            if (static::checkDoubleSection($fields, $settings)) {
                return false;
            }
        }
    }

    /**
     * Verify the existence of duplicate sections.
     * 
     * @param array $fields Section fields.
     * @param array $settings Internationalization settings of info block.
     * @return bool
     */
    protected static function checkDoubleSection(array $fields, $settings)
    {
        global $APPLICATION;

        $sectionModel = new \CIBlockSection();

        $rsSection = $sectionModel->GetList(
            [],
            [
                'IBLOCK_ID' => $fields['IBLOCK_ID'],
                'UF_' . $settings['PROP_CODE_PUBLIC_ID'] => $fields['UF_' . $settings['PROP_CODE_PUBLIC_ID']],
                'UF_' . $settings['PROP_CODE_LANG'] => $fields['UF_' . $settings['PROP_CODE_LANG']],
                '!ID' => $fields['ID']
            ],
            false,
            [
                'ID'
            ]
        );

        if ($section = $rsSection->Fetch()) {
            $APPLICATION->ThrowException(Loc::getMessage('NOTAMEDIA_I18N_IBLOCK_HANDLER_ELEMENT_ALREADY_EXIST'));
            return true;
        }

        return false;
    }
}