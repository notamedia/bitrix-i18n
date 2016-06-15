<?php

namespace Notamedia\i18n\Iblock\Ui;

use Bitrix\Main\Localization\LanguageTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Users field with select box of language for elements of info blocks.
 */
class LangField extends \CUserTypeString
{
    const USER_TYPE = 'NOTAMEDIA_I18N_UF_LANG';
    
    public function GetUserTypeDescription()
    {
        return [
            'USER_TYPE_ID' => static::USER_TYPE,
            'CLASS_NAME' => get_called_class(),
            'DESCRIPTION' => Loc::getMessage('NOTAMEDIA_I18N_FIELD_LANG'),
            'BASE_TYPE' => 'string',
        ];
    }

    public function GetSettingsHTML($userField = false, $htmlControl, $varsFromForm)
    {
        return '';
    }

    public function GetEditFormHTML($userField, $htmlControl)
    {
        return InterfaceHelper::getLangFieldHtml($htmlControl['NAME'], $htmlControl['VALUE'], InterfaceHelper::MODEL_SECTION);
    }

    public function GetAdminListViewHTML($userField, $htmlControl)
    {
        $rsLang = LanguageTable::query()
            ->setFilter(['LID' => $htmlControl['VALUE']])
            ->setSelect(['NAME'])
            ->exec();

        if ($lang = $rsLang->fetch()) {
            return $lang['NAME'];
        }
    }

    public function GetAdminListEditHTML($userField, $htmlControl)
    {
        return static::GetEditFormHTML($userField, $htmlControl);
    }

    public function GetFilterHTML($userField, $htmlControl)
    {
        return static::GetEditFormHTML($userField, $htmlControl);
    }
}