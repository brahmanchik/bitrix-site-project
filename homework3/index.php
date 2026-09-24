<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Models\Lists\DoctorsPropertyValuesTable;
use Bitrix\Main\Localization\Loc;

const PROCEDURES_IBLOCK_ID = HW3_PROCEDURES_IBLOCK_ID;
Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('HW3_TITLE'));

Loader::includeModule('iblock');

$doctors = DoctorsPropertyValuesTable::getList([
    'select' => [
        'ID' => 'IBLOCK_ELEMENT_ID',
        'NAME' => 'ELEMENT.NAME',
        'PROCEDURES' => 'PROCEDURES',
    ],
    'filter' => [
        '=ELEMENT.ACTIVE' => 'Y',
    ],
    'order' => [
        'NAME' => 'ASC',
    ],
])->fetchAll();

$request = Context::getCurrent()->getRequest();
$doctorId = (int)$request->getQuery('doctor_id');

$selectedDoctor = null;

foreach ($doctors as $doctor) {
    if ((int)$doctor['ID'] === $doctorId) {
        $selectedDoctor = $doctor;
        break;
    }
}

$procedureIds = $selectedDoctor['PROCEDURES'] ?? [];
$procedures = [];

if (!empty($procedureIds)) {
    $procedures = ElementTable::getList([
        'select' => [
            'ID',
            'NAME',
        ],
        'filter' => [
            '=IBLOCK_ID' => PROCEDURES_IBLOCK_ID,
            '@ID' => $procedureIds,
            '=ACTIVE' => 'Y',
        ],
        'order' => [
            'NAME' => 'ASC',
        ],
    ])->fetchAll();
}

?>

<h1><?php $APPLICATION->ShowTitle(); ?></h1>

<?php if ($USER->IsAdmin()): ?>
    <ul>
        <li>
            <a href="/homework3/add-doctor.php">
                <?= Loc::getMessage('HW3_ADD_DOCTOR_LINK') ?>
            </a>
        </li>
        <li>
            <a href="/homework3/add-procedure.php">
                <?= Loc::getMessage('HW3_ADD_PROCEDURE_LINK') ?>
            </a>
        </li>
        <li>
            <a href="/homework3/link-procedure.php">
                <?= Loc::getMessage('HW3_LINK_PROCEDURE_LINK') ?>
            </a>
        </li>
    </ul>
<?php endif; ?>

<h2><?= Loc::getMessage('HW3_DOCTORS') ?></h2>

<ul>
    <?php foreach ($doctors as $doctor): ?>
        <li>
            <a href="?doctor_id=<?= (int)$doctor['ID'] ?>">
                <?= htmlspecialcharsbx($doctor['NAME']) ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<?php if ($selectedDoctor): ?>
    <h2><?= htmlspecialcharsbx($selectedDoctor['NAME']) ?></h2>

    <h3><?= Loc::getMessage('HW3_PROCEDURES') ?></h3>

<?php if ($procedures): ?>
    <ul>
        <?php foreach ($procedures as $procedure): ?>
            <li><?= htmlspecialcharsbx($procedure['NAME']) ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p><?= Loc::getMessage('HW3_NO_PROCEDURES') ?></p>
<?php endif; ?>
<?php else: ?>
    <p><?= Loc::getMessage('HW3_SELECT_DOCTOR') ?></p>
<?php endif; ?>

<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>