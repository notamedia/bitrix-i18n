<?php

namespace Notamedia\i18n\Iblock\Converter;

use Bitrix\Main\Localization\Loc;
use Notamedia\i18n\Iblock\Exception\FillDataException;
use Notamedia\i18n\Iblock\Exception\InternationalizeException;
use Notamedia\i18n\Iblock\Exception\PropertyAlreadyExistException;
use Notamedia\i18n\Iblock\Ui\LangProperty;
use Notamedia\i18n\Iblock\Ui\PublicIdProperty;

/**
 * Converter of elements info blocks.
 */
class ElementConverter extends Converter
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
        $isExistPropPublicId = $this->checkExistProperty($propCodePublicId, PublicIdProperty::USER_TYPE);
        $isExistPropLang = $this->checkExistProperty($propCodeLang, LangProperty::USER_TYPE);
        
        $propertyModel = new \CIBlockProperty();
        
        if (!$isExistPropPublicId) {
            $addResult = $propertyModel->Add([
                'NAME' => Loc::getMessage('NOTAMEDIA_I18N_ELEMENT_CONVERTER_PROP_PUBLIC_ID'),
                'ACTIVE' => 'Y',
                'CODE' => $propCodePublicId,
                'PROPERTY_TYPE' => 'S',
                'USER_TYPE' => PublicIdProperty::USER_TYPE,
                'FILTRABLE' => 'Y',
                'IBLOCK_ID' => $this->getIblockId()
            ]);

            if (!$addResult) {
                throw new InternationalizeException([$propertyModel->LAST_ERROR]);
            }
        }

        if (!$isExistPropLang) {
            $addResult = $propertyModel->Add([
                'NAME' => Loc::getMessage('NOTAMEDIA_I18N_ELEMENT_CONVERTER_PROP_LANG'),
                'ACTIVE' => 'Y',
                'CODE' => $propCodeLang,
                'PROPERTY_TYPE' => 'S',
                'USER_TYPE' => LangProperty::USER_TYPE,
                'FILTRABLE' => 'Y',
                'IS_REQUIRED' => 'Y',
                'IBLOCK_ID' => $this->getIblockId()
            ]);

            if (!$addResult) {
                throw new InternationalizeException([$propertyModel->LAST_ERROR]);
            }
        }
        
        parent::internationalize($propCodePublicId, $propCodeLang, $defaultLang);
    }

    /**
     * @param string $propCode
     * @param string $userType
     * @return bool
     * @throws PropertyAlreadyExistException
     */
    protected function checkExistProperty($propCode, $userType)
    {
        $propertyModel = new \CIBlockProperty();
        
        $rsProperty = $propertyModel->GetList([], ['IBLOCK_ID' => $this->getIblockId(), 'CODE' => $propCode]);
        
        if ($property = $rsProperty->Fetch()) {
            if ($property['USER_TYPE'] !== $userType) {
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
        $elementModel = new \CIBlockElement();
        
        $rsElements = $elementModel->GetList(
            [],
            [
                'IBLOCK_ID' => $this->getIblockId()
            ],
            false,
            false,
            [
                'ID',
                'IBLOCK_ID',
                'PROPERTY_' . $propCodePublicId,
                'PROPERTY_' . $propCodeLang
            ]
        );

        while ($element = $rsElements->Fetch())
        {
            if ($propCodePublicId) {
                if ($force === false && !empty($element['PROPERTY_' . $propCodePublicId . '_VALUE'])) {
                    continue;
                }
                
                if (!$elementModel->SetPropertyValueCode($element['ID'], $propCodePublicId, $element['ID'])) {
                    throw new FillDataException($element['ID'], $this->getIblockId());
                }
            }
            
            if ($propCodeLang && $defaultLang) {
                if ($force === false && !empty($element['PROPERTY_' . $propCodeLang . '_VALUE'])) {
                    continue;
                }
                
                if (!$elementModel->SetPropertyValueCode($element['ID'], $propCodeLang, $defaultLang)) {
                    throw new FillDataException($element['ID'], $this->getIblockId());
                }
            }
        }
    }
}