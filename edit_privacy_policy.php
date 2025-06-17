<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['textAr'] = getValueFromPost(value: 'textAr');
        $GLOBALS['textEn'] = getValueFromPost(value: 'textEn');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['textAr'])
            || checkEmpty(value: $GLOBALS['textEn'])
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
                privacy_policy 
            SET 
                textAr = ?,
                textEn = ?
        ";
        $values = array(
            $GLOBALS['textAr'],
            $GLOBALS['textEn'],
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
                    'messageAr' => 'تم التعديل بنجاح',
                    'messageEn' => 'Modified successfully',
                ],
                'privacyPolicy' => getOne(table: 'privacy_policy'),
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