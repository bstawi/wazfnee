<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['faqId']      = getValueFromPost(value: 'faqId');
        $GLOBALS['questionAr'] = getValueFromPost(value: 'questionAr');
        $GLOBALS['questionEn'] = getValueFromPost(value: 'questionEn');
        $GLOBALS['answerAr']   = getValueFromPost(value: 'answerAr');
        $GLOBALS['answerEn']   = getValueFromPost(value: 'answerEn');
    }

    function isExistAnyDataEmpty() {
        if(
               checkEmpty(value: $GLOBALS['faqId'])
            || checkEmpty(value: $GLOBALS['questionAr'])
            || checkEmpty(value: $GLOBALS['questionEn'])
            || checkEmpty(value: $GLOBALS['answerAr'])
            || checkEmpty(value: $GLOBALS['answerEn'])
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
                faqs 
            SET 
                questionAr = ?,
                questionEn = ?,
                answerAr = ?,
                answerEn = ?
            WHERE 
                faqId = ?
        ";
        $values = array(
            $GLOBALS['questionAr'],
            $GLOBALS['questionEn'],
            $GLOBALS['answerAr'],
            $GLOBALS['answerEn'],
            $GLOBALS['faqId'],
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

            editInDatabase();

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'تم تعديل السؤال بنجاح',
                    'messageEn' => 'The faq has been modified successfully',
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