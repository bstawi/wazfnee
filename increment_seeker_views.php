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

    function editInDatabase() {
        $sql = "
            UPDATE 
                seekers
            SET 
                views = views + 1
            WHERE 
                seekerId = ?
        ";
        $values = array(
            $GLOBALS['seekerId'],
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
        else if(!isExist(table: 'seekers', where: 'seekerId = ?', valuesArray: array($GLOBALS['seekerId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'الاعلان غير موجود',
                    'messageEn' => 'Seeker not found',
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
                'seeker' => getOne(table: 'seekers', where: 'seekerId = ?', valuesArray: array($GLOBALS['seekerId'])),
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