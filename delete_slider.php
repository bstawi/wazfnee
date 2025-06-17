<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['sliderId'] = getValueFromPost(value: 'sliderId');
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

    if($_SERVER['REQUEST_METHOD'] == 'POST') {

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
            
            deleteWithId(table: 'sliders', column: 'sliderId', id: $GLOBALS['sliderId']);

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'تم حذف البانر بنجاح',
                    'messageEn' => 'The slider has been deleted successfully',
                ],
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