<?php

IncludeModuleLangFile(__FILE__);

class notamedia_i18n extends CModule
{
    var $MODULE_ID = 'notamedia.i18n';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;

    function __construct()
    {
        $arModuleVersion = [];

        include(__DIR__ . '/version.php');

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_NAME = GetMessage('NOTAMEDIA_I18N_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('NOTAMEDIA_I18N_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = GetMessage('NOTAMEDIA_I18N_PARTNER_NAME');
        $this->PARTNER_URI = GetMessage('NOTAMEDIA_I18N_PARTNER_URI');
    }
    
    public function DoInstall()
    {
        $this->InstallDB();
    }
    
   public function DoUninstall()
   {
       $this->UnInstallDB();
   }

    public function InstallDB()
    {
        global $DB;
        
        RegisterModule($this->MODULE_ID);

        RegisterModuleDependences(
            'iblock', 
            'OnIBlockPropertyBuildList', 
            $this->MODULE_ID, 
            '\Notamedia\i18n\Iblock\Ui\LangProperty', 
            'getUserTypeDescription'
        );

        RegisterModuleDependences(
            'iblock', 
            'OnIBlockPropertyBuildList', 
            $this->MODULE_ID, 
            '\Notamedia\i18n\Iblock\Ui\PublicIdProperty', 
            'getUserTypeDescription'
        );

        RegisterModuleDependences(
            'main', 
            'OnUserTypeBuildList', 
            $this->MODULE_ID, 
            '\Notamedia\i18n\Iblock\Ui\LangField', 
            'GetUserTypeDescription'
        );

        RegisterModuleDependences(
            'main', 
            'OnUserTypeBuildList', 
            $this->MODULE_ID, 
            '\Notamedia\i18n\Iblock\Ui\PublicIdField', 
            'GetUserTypeDescription'
        );

        RegisterModuleDependences(
            'iblock',
            'OnBeforeIBlockElementAdd',
            $this->MODULE_ID,
            '\Notamedia\i18n\Iblock\ElementHandler',
            'onBeforeAdd'
        );

        RegisterModuleDependences(
            'iblock', 
            'OnBeforeIBlockElementUpdate', 
            $this->MODULE_ID, 
            '\Notamedia\i18n\Iblock\ElementHandler', 
            'onBeforeUpdate'
        );

        RegisterModuleDependences(
            'iblock',
            'OnBeforeIBlockSectionAdd',
            $this->MODULE_ID,
            '\Notamedia\i18n\Iblock\SectionHandler',
            'onBeforeAdd'
        );

        RegisterModuleDependences(
            'iblock', 
            'OnBeforeIBlockSectionUpdate', 
            $this->MODULE_ID, 
            '\Notamedia\i18n\Iblock\SectionHandler', 
            'onBeforeUpdate'
        );

        RegisterModuleDependences(
            'main', 
            'OnAdminContextMenuShow', 
            $this->MODULE_ID, 
            '\Notamedia\i18n\Iblock\Ui\InterfaceHelper', 
            'onAdminContextMenuShow'
        );
        
        $DB->Query("CREATE TABLE IF NOT EXISTS `notamedia_i18n_iblock`
            (
              `ID` INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
              `IBLOCK_ID` INT(11) NOT NULL,
              `PROP_CODE_PUBLIC_ID` VARCHAR(255) NOT NULL,
              `PROP_CODE_LANG` VARCHAR(255) NOT NULL,
              `TIMESTAMP_X` TIMESTAMP
            )");
                
        $DB->Query("CREATE TABLE IF NOT EXISTS `notamedia_i18n_iblock_public_id`
            (
              `ID` INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT
            )");
    }

    public function UnInstallDB()
    {
        global $DB;
        
        $DB->Query("DROP TABLE IF EXISTS `notamedia_i18n_iblock`");
        $DB->Query("DROP TABLE IF EXISTS `notamedia_i18n_iblock_public_id`");
        
        UnRegisterModuleDependences(
            'iblock', 
            'OnIBlockPropertyBuildList', 
            $this->MODULE_ID, 
            '\Notamedia\i18n\Iblock\Ui\LangProperty', 
            'getUserTypeDescription'
        );
        
        UnRegisterModuleDependences(
            'iblock', 
            'OnIBlockPropertyBuildList', 
            $this->MODULE_ID, 
            '\Notamedia\i18n\Iblock\Ui\PublicIdProperty', 
            'getUserTypeDescription'
        );
        
        UnRegisterModuleDependences(
            'main', 
            'OnUserTypeBuildList', 
            $this->MODULE_ID, 
            '\Notamedia\i18n\Iblock\Ui\LangField', 
            'GetUserTypeDescription'
        );
        
        UnRegisterModuleDependences(
            'main', 
            'OnUserTypeBuildList', 
            $this->MODULE_ID, 
            '\Notamedia\i18n\Iblock\Ui\PublicIdField', 
            'GetUserTypeDescription'
        );
        
        UnRegisterModuleDependences(
            'iblock', 
            'OnBeforeIBlockElementAdd', 
            $this->MODULE_ID, 
            '\Notamedia\i18n\Iblock\ElementHandler', 
            'onBeforeAdd'
        );
        
        UnRegisterModuleDependences(
            'iblock', 
            'OnBeforeIBlockElementUpdate', 
            $this->MODULE_ID, 
            '\Notamedia\i18n\Iblock\ElementHandler', 
            'onBeforeUpdate'
        );
        
        UnRegisterModuleDependences(
            'iblock', 
            'OnBeforeIBlockSectionAdd', 
            $this->MODULE_ID, 
            '\Notamedia\i18n\Iblock\SectionHandler', 
            'onBeforeAdd'
        );
        
        UnRegisterModuleDependences(
            'iblock', 
            'OnBeforeIBlockSectionUpdate', 
            $this->MODULE_ID, 
            '\Notamedia\i18n\Iblock\SectionHandler', 
            'onBeforeUpdate'
        );
        
        UnRegisterModuleDependences(
            'main', 
            'OnAdminContextMenuShow', 
            $this->MODULE_ID, 
            '\Notamedia\i18n\Iblock\Ui\InterfaceHelper', 
            'onAdminContextMenuShow'
        );

        UnRegisterModule($this->MODULE_ID);
    }
}