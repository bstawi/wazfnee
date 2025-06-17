<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['socialMediaId'] = getValueFromGet(value: 'socialMediaId');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['socialMediaId'])
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
        else if(!isExist(table: 'social_media', where: 'socialMediaId = ?', valuesArray: array($GLOBALS['socialMediaId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'وسيلة التواصل غير موجودة',
                    'messageEn' => 'Social media not found',
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
                'socialMedia' => getOne(table: 'social_media', where: 'socialMediaId = ?', valuesArray: array($GLOBALS['socialMediaId'])),
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