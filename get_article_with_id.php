<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['articleId'] = getValueFromGet(value: 'articleId');
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
            
            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'نجاح',
                    'messageEn' => 'Success',
                ],
                'article' => getOne(table: 'articles', where: 'articleId = ?', valuesArray: array($GLOBALS['articleId'])),
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