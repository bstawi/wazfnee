<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['adminNotificationId'] = getValueFromGet(value: 'adminNotificationId');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['adminNotificationId'])
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
        else if(!isExist(table: 'admin_notifications', where: 'adminNotificationId = ?', valuesArray: array($GLOBALS['adminNotificationId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'الاشعار غير موجود',
                    'messageEn' => 'Notification not found',
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
                'adminNotification' => getOne(table: 'admin_notifications', where: 'adminNotificationId = ?', valuesArray: array($GLOBALS['adminNotificationId'])),
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