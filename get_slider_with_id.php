<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['sliderId'] = getValueFromGet(value: 'sliderId');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['sliderId'])
        ) {
            return true;
        }
        else {
            return false;
        }
    }

    if($_SERVER['REQUEST_METHOD'] == 'GET') {

        getDataFromRequest();

        if(isExistAnyDataEmpty()) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'املأ جميع البيانات المطلوبة',
                    'messageEn' => 'Fill all required data',
                ],
            ];
        }
        else if(!isExist(table: 'sliders', where: 'sliderId = ?', valuesArray: array($GLOBALS['sliderId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'البانر غير موجود',
                    'messageEn' => 'Slider not found',
                ],
            ]; 
        }
        else {
            
            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'نجاح',
                    'messageEn' => 'Success',
                ],
                'slider' => getOne(table: 'sliders', where: 'sliderId = ?', valuesArray: array($GLOBALS['sliderId'])),
            ];
        }
    }
    else {
        
        $result = [
            'base' => [
                'status' => false,
                'messageAr' => 'حدث خطأ',
                'messageEn' => 'An error occurred',
            ],
        ];
    }

    header('Content-Type: application/json');

    echo json_encode($result);