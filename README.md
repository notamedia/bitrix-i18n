# i18n in Bitrix CMS

Module of internationalization of content in info blocks for Bitrix CMS. Allows you to create multiple language 
versions of the item with a public ID.

See the screenshots of the interface of internationalization in the [wiki](https://github.com/notamedia/bitrix-i18n/wiki).

## Installation

Download the library using Composer:

```bash
composer require notamedia/bitrix-i18n
```

Create migration file with contents:

```php
<?php

use Bitrix\Main\Loader;
use Notamedia\i18n\Iblock\Converter\IblockManager;

// 1. Installation module notamedia.i18n

// 2. Convertion info block
if (Loader::includeModule('notamedia.i18n')) {
    $iblockId = 1; // ID of your info block

    $manager = new IblockManager($iblockId);
    $manager->convert('PUBLIC_ID', 'LANG', 'ru');
}
```

## Requirements

* PHP >= 5.4
* Bitrix CMS >= 15.5.10