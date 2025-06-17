<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['questionAr'] = getValueFromPost(value: 'questionAr');
        $GLOBALS['questionEn'] = getValueFromPost(value: 'questionEn');
        $GLOBALS['answerAr']   = getValueFromPost(value: 'answerAr');
        $GLOBALS['answerEn']   = getValueFromPost(value: 'answerEn');
        $GLOBALS['createdAt']  = getValueFromPost(value: 'createdAt');
    }

    function isExistAnyDataEmpty() {
        if(
               checkEmpty(value: $GLOBALS['questionAr'])
            || checkEmpty(value: $GLOBALS['questionEn'])
            || checkEmpty(value: $GLOBALS['answerAr'])
            || checkEmpty(value: $GLOBALS['answerEn'])
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
                faqs (questionAr, questionEn, answerAr, answerEn, createdAt) 
            VALUES 
                (?, ?, ?, ?, ?)
        ";
        $values = array(
            $GLOBALS['questionAr'],
            $GLOBALS['questionEn'],
            $GLOBALS['answerAr'],
            $GLOBALS['answerEn'],
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
                    'messageAr' => 'تم اضافة السؤال بنجاح',
                    'messageEn' => 'The faq has been added successfully',
                ],
                'faq' => getOne(table: 'faqs', where: 'faqId = ?', valuesArray: array($GLOBALS['conn']->lastInsertId())),
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