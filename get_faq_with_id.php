<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['faqId'] = getValueFromGet(value: 'faqId');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['faqId'])
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
        else if(!isExist(table: 'faqs', where: 'faqId = ?', valuesArray: array($GLOBALS['faqId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'السؤال غير موجود',
                    'messageEn' => 'Faq not found',
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
                'faq' => getOne(table: 'faqs', where: 'faqId = ?', valuesArray: array($GLOBALS['faqId'])),
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