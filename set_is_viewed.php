<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['adminNotificationId'] = getValueFromPost(value: 'adminNotificationId');
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

    function editInDatabase() {
        $sql = "
            UPDATE 
                admin_notifications
            SET 
                isViewed = ?
            WHERE 
                adminNotificationId = ?
        ";
        $values = array(
            true,
            $GLOBALS['adminNotificationId'],
        );
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->execute($values);
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

            editInDatabase();

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'نجاح',
                    'messageEn' => 'success',
                ],
                'isViewed' => true,
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