<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['nameAr']      = getValueFromPost(value: 'nameAr');
        $GLOBALS['nameEn']      = getValueFromPost(value: 'nameEn');
        $GLOBALS['image']       = getValueFromPost(value: 'image');
        $GLOBALS['customOrder'] = getValueFromPost(value: 'customOrder');
        $GLOBALS['createdAt']   = getValueFromPost(value: 'createdAt');
    }

    function isExistAnyDataEmpty() {
        if(
               checkEmpty(value: $GLOBALS['nameAr'])
            || checkEmpty(value: $GLOBALS['nameEn'])
            || checkEmpty(value: $GLOBALS['image'])
            || checkEmpty(value: $GLOBALS['customOrder'])
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
                categories (nameAr, nameEn, image, customOrder, createdAt) 
            VALUES 
                (?, ?, ?, ?, ?)
        ";
        $values = array(
            $GLOBALS['nameAr'],
            $GLOBALS['nameEn'],
            $GLOBALS['image'],
            $GLOBALS['customOrder'],
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
                    'messageAr' => 'تم اضافة الفئة بنجاح',
                    'messageEn' => 'The category has been added successfully',
                ],
                'category' => getOne(table: 'categories', where: 'categoryId = ?', valuesArray: array($GLOBALS['conn']->lastInsertId())),
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