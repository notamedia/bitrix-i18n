<?php

namespace Notamedia\i18n\Iblock\Ui;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Users field for public ID of elements of the info blocks.
 */
class PublicIdField extends \CUserTypeString
{
    const USER_TYPE = 'NOTAMEDIA_I18N_UF_PUBLIC_ID';
    
    public function GetUserTypeDescription()
    {
        return [
            'USER_TYPE_ID' => static::USER_TYPE,
            'CLASS_NAME' => get_called_class(),
            'DESCRIPTION' => Loc::getMessage('NOTAMEDIA_I18N_FIELD_PUBLIC_ID'),
            'BASE_TYPE' => 'string',
        ];
    }

    public function GetSettingsHTML($userField = false, $htmlControl, $varsFromForm)
    {
        return '';
    }

    public function GetEditFormHTML($userField, $htmlControl)
    {
        $request = Application::getInstance()->getContext()->getRequest();
        $iblockId = intval(str_replace(['IBLOCK_', '_SECTION'], '', $userField['ENTITY_ID']));

        if (empty($userField['VALUE']) && $request->get('I18N_RELATED_ID')) {
            $sectionModel = new \CIBlockSection();
            
            $rsRelatedSection = $sectionModel->GetList(
                [],
                ['IBLOCK_ID' => $iblockId, 'ID' => $request->get('I18N_RELATED_ID')],
                false,
                ['ID', $userField['FIELD_NAME']]
            );

            if ($relatedSection = $rsRelatedSection->Fetch()) {
                $userField['VALUE'] = $relatedSection[$userField['FIELD_NAME']];
            }
        }
        
        return $userField['VALUE'] . '<input type="hidden" name="' . $htmlControl['NAME'] . '" value="' . $userField['VALUE'] . '">';
    }

    public function GetAdminListViewHTML($userField, $htmlControl)
    {
        return $htmlControl['VALUE'];
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