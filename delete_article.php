<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['articleId'] = getValueFromPost(value: 'articleId');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['articleId'])
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
        else if(!isExist(table: 'articles', where: 'articleId = ?', valuesArray: array($GLOBALS['articleId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'المقالة غير موجودة',
                    'messageEn' => 'Article not found',
                ],
            ]; 
        }
        else {
            
            deleteWithId(table: 'articles', column: 'articleId', id: $GLOBALS['articleId']);

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'تم حذف المقالة بنجاح',
                    'messageEn' => 'The article has been deleted successfully',
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