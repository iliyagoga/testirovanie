<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Loader;
use Bitrix\Iblock;


class Arenda extends CBitrixComponent{
    protected $timeStart=0;
    protected $timeEnd=0;
    public function checkParams($get){
  
            if(!empty($get['time_start']) and !empty($get['time_end'])){
                $this->timeStart=$get['time_start'];
                $this->timeEnd=$get['time_end'];
            }
        

    }
    protected function getCars()
    {
        if (!Loader::includeModule('iblock')) {
            throw new Exception('Модуль инфоблоков не загружен');
        }
        $elements = [];
        $dbElements = \CIBlockElement::GetList(Array(),
             ['IBLOCK_CODE'=>'cars']
          
        );

        while ($element = $dbElements->fetch()) {
            $elem=CIBlockElement::GetProperty($element['IBLOCK_ID'],$element['ID']);
            $element['VALUES']=[];
            while ($ob = $elem->GetNext())
            { if($ob['CODE']=='driver'){
                $driver=CIBlockElement::GetProperty($ob['LINK_IBLOCK_ID'],$ob['VALUE']);
                $ob=$driver->Fetch();
            }
                array_push($element['VALUES'],$ob);
            }
            array_push($elements,$element);
            
        }

        return $elements;
    }

    protected function getBrone(){
        $broneCars=\CIBlockElement::GetList(Array(),['IBLOCK_CODE'=>'brone']);
        $arrBroneCars=[];

        while ($element = $broneCars->fetch()) {
            $elem=CIBlockElement::GetProperty($element['IBLOCK_ID'],$element['ID']);
            $element['PROPERTIES']=[];
            while ($ob = $elem->GetNext()){
                array_push($element['PROPERTIES'],$ob);
            }
            array_push($arrBroneCars,$element);

        }
        return $arrBroneCars;
    }

    protected function getBlackListCars($timeStart,$timeEnd){
        $blackCars=$this->getBrone();
        $arr=[];
        foreach($blackCars as $blackCar){
            if(strtotime($blackCar['PROPERTIES'][2]['VALUE'])<=strtotime($timeStart) and strtotime($blackCar['PROPERTIES'][3]['VALUE'])>=strtotime($timeStart)){
                array_push($arr,$blackCar['PROPERTIES'][1]['VALUE']);
            }
            else{
                
                if(strtotime($blackCar['PROPERTIES'][2]['VALUE'])<=strtotime($timeEnd) and strtotime($blackCar['PROPERTIES'][3]['VALUE'])>=strtotime($timeEnd)){
                    array_push($arr,$blackCar['PROPERTIES'][1]['VALUE']);
                }
                else{
                    if(strtotime($blackCar['PROPERTIES'][2]['VALUE'])>=strtotime($timeStart) and strtotime($blackCar['PROPERTIES'][3]['VALUE'])<=strtotime($timeEnd)){
                        array_push($arr,$blackCar['PROPERTIES'][1]['VALUE']);
                    }
                    else{
                        continue;
                    }
                }
            }
  
        }


        return $arr;
    }

    protected function getClassRole(){
        $allowCars=\CIBlockElement::GetList(Array(),['IBLOCK_CODE'=>'role_class_car']);
        $arrAllowCars=[];
        while($allowCar = $allowCars->Fetch()){
            $elem=CIBlockElement::GetProperty($allowCar['IBLOCK_ID'],$allowCar['ID']);
            $allowCar['PROPERTIES']=[];
            while ($ob = $elem->GetNext()){
                array_push($allowCar['PROPERTIES'],$ob);
            }
            array_push($arrAllowCars,$allowCar);

        }
        return $arrAllowCars;
    }
   protected function getUserRole($userId){
        $userRoles=\CIBlockElement::GetList(Array(),['IBLOCK_CODE'=>'role']);
        $roles=[];
        $user=null;
        while ($role=$userRoles->Fetch()){
            $elem=CIBlockElement::GetProperty($role['IBLOCK_ID'],$role['ID']);
            $role['PROPERTIES']=[];
            while ($ob = $elem->GetNext()){
                array_push($role['PROPERTIES'],$ob);
            }
            array_push($roles,$role);
        }
        foreach($roles as $role){
            if($role['PROPERTIES'][1]['VALUE']==$userId){
                $user=$role;
                break;
            }
        }
        return $user;
    }
    protected function getAllowComfort($userId){
        $roleClasses=$this->getClassRole();
        $user=$this->getUserRole($userId);
        $allowComforts=[];
        foreach($roleClasses as $roleClass){
            if($roleClass['PROPERTIES']['0']['VALUE']==$user['ID'])
            array_push($allowComforts,$roleClass['PROPERTIES']['1']['VALUE_XML_ID']);
          
        }
        return $allowComforts;
    }

    public function init($userId){
        if($this->timeStart!=0 and $this->timeEnd!=0){
            $blacklistCars=$this->getBlackListCars($this->timeStart,$this->timeEnd);
            $comfortList=$this->getAllowComfort($userId);
            $cars=$this->getCars();
            $result=[];
            foreach($cars as $car){
                if(!in_array($car['ID'],$blacklistCars)){
              
                    if(in_array($car['VALUES'][1]['VALUE_XML_ID'],$comfortList)){
                        array_push($result,$car);
                    }
                   
                }
            }
            return $result;
        }
        else{
            return [];
        }
       

    }
    


}
?>