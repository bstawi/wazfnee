<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['userId'] = getValueFromPost(value: 'userId');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['userId'])
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
        else if(!isExist(table: 'users', where: 'userId = ?', valuesArray: array($GLOBALS['userId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'المستخدم غير موجود',
                    'messageEn' => 'User not found',
                ],
            ];
        }
        else {

            deleteWithId(table: 'users', column: 'userId', id: $GLOBALS['userId']);

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'نجاح',
                    'messageEn' => 'Success',
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