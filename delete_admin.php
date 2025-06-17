<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['adminId'] = getValueFromPost(value: 'adminId');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['adminId'])
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
        else if(!isExist(table: 'admins', where: 'adminId = ?', valuesArray: array($GLOBALS['adminId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'الادمن غير موجود',
                    'messageEn' => 'Admin not found',
                ],
            ];
        }
        else {

            deleteWithId(table: 'admins', column: 'adminId', id: $GLOBALS['adminId']);

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