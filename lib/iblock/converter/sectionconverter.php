<?php

namespace Notamedia\i18n\Iblock\Converter;

use Bitrix\Main\Localization\Loc;
use Notamedia\i18n\Iblock\Exception\FillDataException;
use Notamedia\i18n\Iblock\Exception\InternationalizeException;
use Notamedia\i18n\Iblock\Exception\PropertyAlreadyExistException;
use Notamedia\i18n\Iblock\Ui\LangField;
use Notamedia\i18n\Iblock\Ui\PublicIdField;

/**
 * Converter of section info blocks.
 */
class SectionConverter extends Converter
{
    /**
     * @inheritdoc
     */
    public function __construct($iblockId)
    {
        parent::__construct($iblockId);

        Loc::loadMessages(__FILE__);
    }

    /**
     * @inheritdoc
     * 
     * @throws PropertyAlreadyExistException
     */
    public function internationalize($propCodePublicId, $propCodeLang, $defaultLang)
    {
        global $APPLICATION;

        $isExistPropPublicId = $this->checkExistProperty($propCodePublicId, PublicIdField::USER_TYPE);
        $isExistPropLang = $this->checkExistProperty($propCodeLang, LangField::USER_TYPE);
        
        $userTypeModel = new \CUserTypeEntity();
        
        if (!$isExistPropPublicId) {
            $addResult = $userTypeModel->Add([
                'ENTITY_ID' => 'IBLOCK_' . $this->getIblockId() . '_SECTION',
                'FIELD_NAME' => 'UF_' . $propCodePublicId,
                'USER_TYPE_ID' => PublicIdField::USER_TYPE,
                'XML_ID' => 'UF_' . $propCodePublicId,
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'Y',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'EDIT_FORM_LABEL' => [
                    'ru' => 'Публичный ID',
                    'en' => 'Public ID'
                ],
                'LIST_COLUMN_LABEL' => [
                    'ru' => 'Публичный ID',
                    'en' => 'Public ID'
                ],
                'LIST_FILTER_LABEL' => [
                    'ru' => 'Публичный ID',
                    'en' => 'Public ID'
                ],
            ]);

            if ($addResult === false) {
                throw new InternationalizeException([$APPLICATION->GetException()->GetString()]);
            }
        }

        if (!$isExistPropLang) {
            $addResult = $userTypeModel->Add([
                'ENTITY_ID' => 'IBLOCK_' . $this->getIblockId() . '_SECTION',
                'FIELD_NAME' => 'UF_' . $propCodeLang,
                'USER_TYPE_ID' => LangField::USER_TYPE,
                'XML_ID' => 'UF_' . $propCodeLang,
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'Y',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'EDIT_FORM_LABEL' => [
                    'ru' => 'Язык',
                    'en' => 'Language'
                ],
                'LIST_COLUMN_LABEL' => [
                    'ru' => 'Язык',
                    'en' => 'Language'
                ],
                'LIST_FILTER_LABEL' => [
                    'ru' => 'Язык',
                    'en' => 'Language'
                ],
            ]);

            if ($addResult === false) {
                throw new InternationalizeException;
            }
        }

        parent::internationalize($propCodePublicId, $propCodeLang, $defaultLang);
    }

    /**
     * @param string $propCode
     * @param string $userType
     * 
     * @return bool
     * 
     * @throws PropertyAlreadyExistException
     */
    protected function checkExistProperty($propCode, $userType)
    {
        $propertyModel = new \CUserTypeEntity();

        $rsProperty = $propertyModel->GetList(
            [], 
            ['ENTITY_ID' => 'IBLOCK_' . $this->getIblockId() . '_SECTION', 'FIELD_NAME' => 'UF_' . $propCode]
        );

        if ($property = $rsProperty->Fetch()) {
            if ($property['USER_TYPE_ID'] !== $userType) {
                throw new PropertyAlreadyExistException($propCode);
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     * 
     * @throws FillDataException
     */
    public function fill($propCodePublicId = null, $propCodeLang = null, $defaultLang = null, $force = false)
    {
        $sectionModel = new \CIBlockSection();
        
        $rsSections = $sectionModel->GetList(
            [], 
            ['IBLOCK_ID' => $this->getIblockId()],
            false,
            ['ID', 'UF_' . $propCodePublicId, 'UF_' . $propCodeLang]
        );
        
        while ($section = $rsSections->Fetch())
        {
            $updateResult = null;
            
            if ($propCodePublicId) {
                if ($force === false && !empty($section['UF_' . $propCodePublicId])) {
                    continue;
                }

                $updateResult = $sectionModel->Update($section['ID'], [
                    'UF_' . $propCodePublicId => $section['ID']
                ]);
            }
            
            if ($propCodeLang) {
                if ($force === false && !empty($section['UF_' . $propCodeLang])) {
                    continue;
                }

                $updateResult = $sectionModel->Update($section['ID'], [
                    'UF_' . $propCodeLang => $defaultLang
                ]);
            }

            if ($updateResult === false) {
                throw new FillDataException($section['ID'], $this->getIblockId(), $sectionModel->LAST_ERROR);
            }
        }
    }
}