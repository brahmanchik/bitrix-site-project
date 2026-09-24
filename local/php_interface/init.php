<?php

// ID инфоблоков по умолчанию для локального окружения.
$homework3Config = [
    'doctorsIblockId' => 16,
    'proceduresIblockId' => 17,
];

// На сервере этот файл сможет переопределить локальные ID.
$homework3ConfigFile = __DIR__ . '/homework3_config.php';

if (is_file($homework3ConfigFile)) {
    $environmentConfig = require $homework3ConfigFile;

    if (is_array($environmentConfig)) {
        $homework3Config = array_replace(
            $homework3Config,
            $environmentConfig
        );
    }
}

if (!defined('HW3_DOCTORS_IBLOCK_ID')) {
    define(
        'HW3_DOCTORS_IBLOCK_ID',
        (int)$homework3Config['doctorsIblockId']
    );
}

if (!defined('HW3_PROCEDURES_IBLOCK_ID')) {
    define(
        'HW3_PROCEDURES_IBLOCK_ID',
        (int)$homework3Config['proceduresIblockId']
    );
}

unset(
    $homework3Config,
    $homework3ConfigFile,
    $environmentConfig
);

// Подключаем автозагрузчик классов проекта.
include_once __DIR__ . '/../app/autoload.php';