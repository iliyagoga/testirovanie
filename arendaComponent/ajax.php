<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require(__DIR__.'/class.php');
use \Arenda as Arenda;
global $USER;
\CModule::IncludeModule("iblock");
$component = new Arenda();
print_r($USER->GetId());
header("Location: /path/to/component.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['timeStart']) && isset($_POST['timeEnd'])) {
    $action = $_POST['action'];
   
    $res=$component->init($USER->GetId(),$_POST['timeStart'],$_POST['timeEnd']);

}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");

?>