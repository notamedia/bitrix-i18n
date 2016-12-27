<?php

namespace Notamedia\i18n\Iblock\Ui;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\LanguageTable;
use Notamedia\i18n\Iblock\i18nTable;

/**
 * Helpers for render internalization UI in the controll panel of Bitrix.
 */
class InterfaceHelper
{
    const MODEL_ELEMENT = 'element';
    const MODEL_SECTION = 'section';

    /**
     * @param array $items
     */
    public static function onAdminContextMenuShow(&$items)
    {
        if ($menu = static::getLangMenu()) {
            $items[] = $menu;
        }
    }

    /**
     * Gets the current language for the currently viewed item.
     *
     * @param string $model Value of constant `InterfaceHelper::MODEL_*`.
     * @return null|string
     */
    public static function getCurrentLang($model)
    {
        $request = Application::getInstance()->getContext()->getRequest();

        if ($request->get('ID') && $request->get('IBLOCK_ID')) {
            $settings = static::getIblockSettings($request->get('IBLOCK_ID'));

            if ($settings) {
                if ($model === static::MODEL_ELEMENT) {
                    $elementModel = new \CIBlockElement();

                    $rsElement = $elementModel->GetList(
                        [],
                        ['IBLOCK_ID' => $request->get('IBLOCK_ID'), 'ID' => $request->get('ID')],
                        false,
                        false,
                        ['ID', 'IBLOCK_ID', 'PROPERTY_' . $settings['PROP_CODE_LANG']]
                    );

                    if ($element = $rsElement->Fetch()) {
                        return $element['PROPERTY_' . $settings['PROP_CODE_LANG'] . '_VALUE'];
                    }
                } elseif ($model === static::MODEL_SECTION) {
                    $sectionModel = new \CIBlockSection();

                    $rsSection = $sectionModel->GetList(
                        [],
                        ['IBLOCK_ID' => $request->get('IBLOCK_ID'), 'ID' => $request->get('ID')],
                        false,
                        [
                            'ID',
                            'UF_' . $settings['PROP_CODE_LANG']
                        ]
                    );

                    if ($section = $rsSection->Fetch()) {
                        return $section['UF_' . $settings['PROP_CODE_LANG']];
                    }
                }
            }
        }

        if ($request->get('i18n_lang')) {
            return $request->get('i18n_lang');
        } else {
            return $request->get('lang');
        }
    }

    /**
     * Gets i18n settings of info block.
     * 
     * @param integer $iblockId
     * @return array|null
     */
    public static function getIblockSettings($iblockId)
    {
        $rsSettings = i18nTable::query()
            ->setFilter(['IBLOCK_ID' => $iblockId])
            ->setSelect(['PROP_CODE_PUBLIC_ID', 'PROP_CODE_LANG'])
            ->exec();

        if ($settings = $rsSettings->fetch()) {
            $propIdPublicId = static::getPropIdByCode($iblockId, $settings['PROP_CODE_PUBLIC_ID']);
            $propIdLang = static::getPropIdByCode($iblockId, $settings['PROP_CODE_LANG']);

            if (!$propIdPublicId || !$propIdLang) {
                return null;
            }

            $settings['PROP_ID_PUBLIC_ID'] = $propIdPublicId;
            $settings['PROP_ID_LANG'] = $propIdLang;

            return $settings;
        }

        return null;
    }

    /**
     * Gets ID of property.
     * 
     * @param int $iblockId ID of info block.
     * @param string $propCode Code of property.
     * @return null
     */
    public static function getPropIdByCode($iblockId, $propCode)
    {
        $rsProp = \CIBlockProperty::GetList(
            [],
            ['IBLOCK_ID' => $iblockId, 'CODE' => $propCode]
        );

        if ($prop = $rsProp->Fetch()) {
            return $prop['ID'];
        } else {
            return null;
        }
    }

    /**
     * @param string $fieldName Name of field.
     * @param string|null $selected Selected value.
     * @param string $model Value of constant `InterfaceHelper::MODEL_*`.
     * @return mixed
     */
    public static function getLangFieldHtml($fieldName, $selected = null, $model)
    {
        $langs = [];

        $rsLangs = LanguageTable::query()
            ->setOrder(['SORT' => 'ASC'])
            ->setSelect(['LID', 'NAME'])
            ->exec();

        while ($lang = $rsLangs->fetch()) {
            $langs[$lang['LID']] = $lang['NAME'];
        }

        if (!$selected) {
            $selected = static::getCurrentLang($model);
        }

        return SelectBoxFromArray(
            $fieldName,
            [
                'reference' => array_values($langs),
                'reference_id' => array_keys($langs)
            ],
            $selected
        );
    }

    /**
     * Gets menu with language select box.
     * 
     * @return array|null
     */
    public static function getLangMenu()
    {
        global $APPLICATION;

        $request = Application::getInstance()->getContext()->getRequest();
        $relatedId = ($request->get('I18N_RELATED_ID')) ? $request->get('I18N_RELATED_ID') : $request->get('ID');
        $page = basename($request->getRequestedPage());

        if ($page === 'iblock_element_edit.php') {
            $model = static::MODEL_ELEMENT;
        } elseif ($page === 'iblock_section_edit.php') {
            $model = static::MODEL_SECTION;
        } else {
            $model = null;
        }

        if ($model === null || !$request->get('IBLOCK_ID') || !$relatedId) {
            return null;
        }

        $menu = [];
        $currentLang = static::getCurrentLang($model);
        $settings = static::getIblockSettings($request->get('IBLOCK_ID'));

        if ($settings === null) {
            return null;
        }

        $itemVersions = static::getItemVersions($model, $relatedId, $settings);

        $rsLangs = LanguageTable::query()
            ->setSelect(['LID', 'NAME'])
            ->exec();

        while ($lang = $rsLangs->fetch()) {
            $queryParams = 'I18N_RELATED_ID=' . $relatedId . '&i18n_lang=' . $lang['LID'];

            if (isset($itemVersions[$lang['LID']])) {
                $queryParams .= '&ID=' . $itemVersions[$lang['LID']];
            }

            $menu[$lang['LID']] = [
                'TEXT' => $lang['NAME'],
                'LINK' => $APPLICATION->GetCurPageParam(
                    $queryParams,
                    ['ID', 'i18n_lang', 'I18N_RELATED_ID']
                )
            ];
        }

        return [
            'TEXT' => $menu[$currentLang]['TEXT'],
            'ICON' => 'btn_copy',
            'MENU' => array_filter($menu, function ($key) use ($currentLang) {
                if ($key === $currentLang) {
                    return false;
                } else {
                    return true;
                }
            }, ARRAY_FILTER_USE_KEY)
        ];
    }

    /**
     * @param string $model Value of constant `InterfaceHelper::MODEL_*`.
     * @param int $relatedId ID of related item.
     * @param array $settings i18n settings of info block.
     * @return array
     */
    protected static function getItemVersions($model, $relatedId, $settings)
    {
        $request = Application::getInstance()->getContext()->getRequest();
        $versions = [];

        if ($model === static::MODEL_ELEMENT) {
            $elementModel = new \CIBlockElement();

            $rsRelatedElement = $elementModel->GetList(
                [],
                ['IBLOCK_ID' => $request->get('IBLOCK_ID'), 'ID' => $relatedId],
                false,
                false,
                ['ID', 'IBLOCK_ID', 'PROPERTY_' . $settings['PROP_CODE_PUBLIC_ID']]
            );

            if ($relatedElement = $rsRelatedElement->Fetch()) {
                $rsVersions = $elementModel->GetList(
                    [],
                    [
                        'IBLOCK_ID' => $request->get('IBLOCK_ID'),
                        'PROPERTY_' . $settings['PROP_CODE_PUBLIC_ID'] => $relatedElement['PROPERTY_' . $settings['PROP_CODE_PUBLIC_ID'] . '_VALUE']
                    ],
                    false,
                    false,
                    [
                        'ID',
                        'IBLOCK_ID',
                        'PROPERTY_' . $settings['PROP_CODE_LANG']
                    ]
                );

                while ($version = $rsVersions->Fetch()) {
                    $versions[$version['PROPERTY_' . $settings['PROP_CODE_LANG'] . '_VALUE']] = $version['ID'];
                }
            }
        } elseif ($model === static::MODEL_SECTION) {
            $sectionModel = new \CIBlockSection();

            $rsRelatedSection = $sectionModel->GetList(
                [],
                ['IBLOCK_ID' => $request->get('IBLOCK_ID'), 'ID' => $relatedId],
                false,
                ['ID', 'UF_' . $settings['PROP_CODE_PUBLIC_ID']]
            );

            if ($relatedSection = $rsRelatedSection->Fetch()) {
                $rsVersions = $sectionModel->GetList(
                    [],
                    [
                        'IBLOCK_ID' => $request->get('IBLOCK_ID'),
                        'UF_' . $settings['PROP_CODE_PUBLIC_ID'] => $relatedSection['UF_' . $settings['PROP_CODE_PUBLIC_ID']]
                    ],
                    false,
                    ['ID', 'UF_' . $settings['PROP_CODE_LANG']]
                );

                while ($version = $rsVersions->Fetch()) {
                    $versions[$version['UF_' . $settings['PROP_CODE_LANG']]] = $version['ID'];
                }
            }
        }

        return $versions;
    }
}