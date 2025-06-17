<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['categoryId'] = getValueFromPost(value: 'categoryId');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['categoryId'])
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
        else if(!isExist(table: 'categories', where: 'categoryId = ?', valuesArray: array($GLOBALS['categoryId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'الفئة غير موجود',
                    'messageEn' => 'Category not found',
                ],
            ]; 
        }
        else {

            deleteWithId(table: 'categories', column: 'categoryId', id: $GLOBALS['categoryId']);

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'تم حذف الفئة بنجاح',
                    'messageEn' => 'The category has been deleted successfully',
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