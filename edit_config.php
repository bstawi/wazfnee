<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['label'] = getValueFromPost(value: 'label');
        $GLOBALS['value'] = getValueFromPost(value: 'value');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['label'])
            || checkEmpty(value: $GLOBALS['value'])
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
                configs
            SET 
                value = ?
            WHERE
                label = ?
        ";
        $values = array(
            $GLOBALS['value'],
            $GLOBALS['label'],
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

            editInDatabase();

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'تم تعديل الاعدادات بنجاح',
                    'messageEn' => 'The settings has been modified successfully',
                ],
                'config' => getOne(table: 'configs', where: 'label = ?', valuesArray: array($GLOBALS['label'])),
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