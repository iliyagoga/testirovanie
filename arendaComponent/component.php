<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
global $USER;
$res= $this->getCars('cars','driver');
$this->checkParams($_GET);
$arResult['ITEMS']=$this->init($USER->GetId());
$this->IncludeComponentTemplate();
?>