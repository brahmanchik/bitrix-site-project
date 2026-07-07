<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
use Bitrix\Main\Type\DateTime;
$APPLICATION->SetTitle("Добавление в лог");
?>
    <ul class="list-group">
        <li class="list-group-item">
            <a href="/local/logs/log_custom.log">Файл лога</a>,
            в лог добавленно 'Открыта страница writelog.php'
        </li>
    </ul>
<?
$currentDateTime = new DateTime();
echo $currentDateTime->toString();

// ТУТ ДОБАВЛЯЕМ ФУНКЦИЮ ЗАПИСИ В ЛОГ В ПАПКУ local/logs
\Bitrix\Main\Diag\Debug::writeToFile(
    "OTUS Открыта страница writelog.php в " . $currentDateTime->toString(), 
    "HTTP_LOG", 
    "local/logs/log_custom.log" // Путь относительно корня сайта к вашей папке
);
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>