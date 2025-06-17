<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['categoryId']  = getValueFromPost(value: 'categoryId');
        $GLOBALS['nameAr']      = getValueFromPost(value: 'nameAr');
        $GLOBALS['nameEn']      = getValueFromPost(value: 'nameEn');
        $GLOBALS['image']       = getValueFromPost(value: 'image');
        $GLOBALS['customOrder'] = getValueFromPost(value: 'customOrder');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['categoryId'])
            || checkEmpty(value: $GLOBALS['nameAr'])
            || checkEmpty(value: $GLOBALS['nameEn'])
            || checkEmpty(value: $GLOBALS['image'])
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
                categories 
            SET 
                nameAr = ?,
                nameEn = ?,
                image = ?,
                customOrder = ?
            WHERE 
                categoryId = ?
        ";
        $values = array(
            $GLOBALS['nameAr'],
            $GLOBALS['nameEn'],
            $GLOBALS['image'],
            $GLOBALS['customOrder'],
            $GLOBALS['categoryId'],
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
        else if(!isExist(table: 'categories', where: 'categoryId = ?', valuesArray: array($GLOBALS['categoryId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'الفئة غير موجودة',
                    'messageEn' => 'Category not found',
                ],
            ]; 
        }
        else {

            editInDatabase();

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'تم تعديل الفئة بنجاح',
                    'messageEn' => 'The category has been modified successfully',
                ],
                'category' => getOne(table: 'categories', where: 'categoryId = ?', valuesArray: array($GLOBALS['categoryId'])),
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