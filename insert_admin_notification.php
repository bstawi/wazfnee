<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['title']      = getValueFromPost(value: 'title');
        $GLOBALS['body']       = getValueFromPost(value: 'body');
        $GLOBALS['isViewed']   = getValueFromPost(value: 'isViewed', isBool: true);
        $GLOBALS['createdAt']  = getValueFromPost(value: 'createdAt');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['title'])
            || checkEmpty(value: $GLOBALS['body'])
            || checkEmpty(value: $GLOBALS['createdAt'])
        ) {
            return true;
        }
        else {
            return false;
        }
    }

    function insertInDatabase() {
        $sql = "
            INSERT INTO 
                admin_notifications (
                    title,
                    body,
                    isViewed,
                    createdAt
                )
            VALUES
                (?, ?, ?, ?)
        ";
        $values = array(
            $GLOBALS['title'],
            $GLOBALS['body'],
            $GLOBALS['isViewed'],
            $GLOBALS['createdAt'],
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
        else {

            insertInDatabase();

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'تم ارسال الاشعار بنجاح',
                    'messageEn' => 'The notification has been sent successfully',
                ],
                'adminNotification' => getOne(table: 'admin_notifications', where: 'adminNotificationId = ?', valuesArray: array($GLOBALS['conn']->lastInsertId())),
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