<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);
$APPLICATION->SetTitle(Loc::getMessage('HW3_ADD_PROCEDURE'));

const PROCEDURES_IBLOCK_ID = HW3_PROCEDURES_IBLOCK_ID;

$request = Context::getCurrent()->getRequest();
$error = '';

if (
    $request->isPost()
    && $USER->IsAdmin()
) {
    if (!check_bitrix_sessid()) {
        $error = Loc::getMessage('HW3_SESSION_ERROR');
    } else {
        $nameValue = $request->getPost('procedure_name');
        $name = is_string($nameValue) ? trim($nameValue) : '';

        if ($name === '' || mb_strlen($name) > 255) {
            $error = Loc::getMessage('HW3_NAME_ERROR');
        } elseif (!Loader::includeModule('iblock')) {
            $error = Loc::getMessage('HW3_MODULE_ERROR');
        } else {
            $element = new \CIBlockElement();

            $newId = $element->Add([
                'IBLOCK_ID' => PROCEDURES_IBLOCK_ID,
                'NAME' => $name,
                'ACTIVE' => 'Y',
                'MODIFIED_BY' => (int)$USER->GetID(),
            ]);

            if ($newId) {
                LocalRedirect('/homework3/add-procedure.php?added=1');
                exit;
            }

            $error = Loc::getMessage('HW3_ADD_ERROR')
                . ' ' . $element->LAST_ERROR;
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

<?php if ($USER->IsAdmin() && $request->getQuery('added') === '1'): ?>
    <p><?= Loc::getMessage('HW3_PROCEDURE_ADDED') ?></p>
<?php endif; ?>

<!-- форма -->

<?php if ($USER->IsAdmin()): ?>
    <form method="post">
        <?= bitrix_sessid_post() ?>

        <label for="procedure_name">
            <?= Loc::getMessage('HW3_PROCEDURE_NAME') ?>
        </label>

        <input
            id="procedure_name"
            type="text"
            name="procedure_name"
            maxlength="255"
            required
        >

        <button type="submit">
            <?= Loc::getMessage('HW3_SAVE') ?>
        </button>
    </form>
<?php else: ?>
    <p><?= Loc::getMessage('HW3_ADMIN_ONLY') ?></p>
<?php endif; ?>

<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>