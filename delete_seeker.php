<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['seekerId'] = getValueFromPost(value: 'seekerId');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['seekerId'])
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
        else if(!isExist(table: 'seekers', where: 'seekerId = ?', valuesArray: array($GLOBALS['seekerId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'الاعلان غير موجود',
                    'messageEn' => 'Ad not found',
                ],
            ]; 
        }
        else {
            
            deleteWithId(table: 'seekers', column: 'seekerId', id: $GLOBALS['seekerId']);

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'تم حذف الاعلان بنجاح',
                    'messageEn' => 'The ad has been deleted successfully',
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