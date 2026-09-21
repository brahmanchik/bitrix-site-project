<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Models\Lists\DoctorsPropertyValuesTable;

Loc::loadMessages(__FILE__);
$APPLICATION->SetTitle(Loc::getMessage('HW3_LINK_TITLE'));

const PROCEDURES_IBLOCK_ID = 17;

$request = Context::getCurrent()->getRequest();
$error = '';
$doctors = [];
$procedures = [];

if ($USER->IsAdmin()) {
    if (!Loader::includeModule('iblock')) {
        $error = Loc::getMessage('HW3_MODULE_ERROR');
    } else {
        $doctors = DoctorsPropertyValuesTable::getList([
            'select' => [
                'ID' => 'IBLOCK_ELEMENT_ID',
                'NAME' => 'ELEMENT.NAME',
                'PROCEDURES',
            ],
            'filter' => [
                '=ELEMENT.ACTIVE' => 'Y',
            ],
            'order' => [
                'NAME' => 'ASC',
            ],
        ])->fetchAll();

        $procedures = ElementTable::getList([
            'select' => ['ID', 'NAME'],
            'filter' => [
                '=IBLOCK_ID' => PROCEDURES_IBLOCK_ID,
                '=ACTIVE' => 'Y',
            ],
            'order' => [
                'NAME' => 'ASC',
            ],
        ])->fetchAll();

        if ($request->isPost()) {
            if (!check_bitrix_sessid()) {
                $error = Loc::getMessage('HW3_SESSION_ERROR');
            } else {
                $doctorValue = $request->getPost('doctor_id');
                $procedureValue = $request->getPost('procedure_id');

                $doctorId = is_string($doctorValue)
                    ? (int)$doctorValue : 0;
                $procedureId = is_string($procedureValue)
                    ? (int)$procedureValue : 0;

                $selectedDoctor = null;
                $procedureExists = false;

                foreach ($doctors as $doctor) {
                    if ((int)$doctor['ID'] === $doctorId) {
                        $selectedDoctor = $doctor;
                        break;
                    }
                }

                foreach ($procedures as $procedure) {
                    if ((int)$procedure['ID'] === $procedureId) {
                        $procedureExists = true;
                        break;
                    }
                }

                if (!$selectedDoctor || !$procedureExists) {
                    $error = Loc::getMessage('HW3_SELECTION_ERROR');
                } else {
                    $procedureIds = array_map(
                        'intval',
                        $selectedDoctor['PROCEDURES'] ?? []
                    );

                    if (!in_array($procedureId, $procedureIds, true)) {
                        $procedureIds[] = $procedureId;

                        \CIBlockElement::SetPropertyValuesEx(
                            $doctorId,
                            DoctorsPropertyValuesTable::IBLOCK_ID,
                            [
                                'PROCEDURES' => array_values(
                                    array_unique($procedureIds)
                                ),
                            ]
                        );
                    }

                    // Проверяем, что связь действительно сохранена.
                    $savedDoctor = DoctorsPropertyValuesTable::getList([
                        'select' => ['PROCEDURES'],
                        'filter' => [
                            '=IBLOCK_ELEMENT_ID' => $doctorId,
                        ],
                    ])->fetch();

                    $savedIds = array_map(
                        'intval',
                        $savedDoctor['PROCEDURES'] ?? []
                    );

                    if (in_array($procedureId, $savedIds, true)) {
                        LocalRedirect(
                            '/homework3/link-procedure.php?linked=1'
                        );
                        exit;
                    }

                    $error = Loc::getMessage('HW3_SAVE_ERROR');
                }
            }
        }
    }
}

?>

<h1><?php $APPLICATION->ShowTitle(); ?></h1>

<p>
    <a href="/homework3/">
        <?= Loc::getMessage('HW3_BACK_TO_DOCTORS') ?>
    </a>
</p>

<?php if ($error !== ''): ?>
    <p><?= htmlspecialcharsbx($error) ?></p>
<?php endif; ?>

<?php if ($USER->IsAdmin() && $request->getQuery('linked') === '1'): ?>
    <p><?= Loc::getMessage('HW3_LINK_SUCCESS') ?></p>
<?php endif; ?>

<?php if (!$USER->IsAdmin()): ?>
    <p><?= Loc::getMessage('HW3_ADMIN_ONLY') ?></p>
<?php elseif ($doctors && $procedures): ?>
    <form method="post">
        <?= bitrix_sessid_post() ?>

        <p>
            <label for="doctor_id">
                <?= Loc::getMessage('HW3_DOCTOR') ?>
            </label>

            <select id="doctor_id" name="doctor_id" required>
                <option value="">
                    <?= Loc::getMessage('HW3_SELECT') ?>
                </option>

                <?php foreach ($doctors as $doctor): ?>
                    <option value="<?= (int)$doctor['ID'] ?>">
                        <?= htmlspecialcharsbx($doctor['NAME']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="procedure_id">
                <?= Loc::getMessage('HW3_PROCEDURE') ?>
            </label>

            <select id="procedure_id" name="procedure_id" required>
                <option value="">
                    <?= Loc::getMessage('HW3_SELECT') ?>
                </option>

                <?php foreach ($procedures as $procedure): ?>
                    <option value="<?= (int)$procedure['ID'] ?>">
                        <?= htmlspecialcharsbx($procedure['NAME']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <button type="submit">
            <?= Loc::getMessage('HW3_LINK_BUTTON') ?>
        </button>
    </form>
<?php elseif ($error === ''): ?>
    <p><?= Loc::getMessage('HW3_EMPTY_LISTS') ?></p>
<?php endif; ?>

<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>