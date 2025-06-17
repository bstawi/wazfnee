<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['image']         = getValueFromPost(value: 'image');
        $GLOBALS['titleAr']       = getValueFromPost(value: 'titleAr');
        $GLOBALS['titleEn']       = getValueFromPost(value: 'titleEn');
        $GLOBALS['articleAr']     = getValueFromPost(value: 'articleAr');
        $GLOBALS['articleEn']     = getValueFromPost(value: 'articleEn');
        $GLOBALS['linksTitleAr']  = getValueFromPost(value: 'linksTitleAr');
        $GLOBALS['linksTitleEn']  = getValueFromPost(value: 'linksTitleEn');
        $GLOBALS['links']         = getValueFromPost(value: 'links');
        $GLOBALS['linksTitlesAr'] = getValueFromPost(value: 'linksTitlesAr');
        $GLOBALS['linksTitlesEn'] = getValueFromPost(value: 'linksTitlesEn');
        $GLOBALS['customOrder']   = getValueFromPost(value: 'customOrder');
        $GLOBALS['isAvailable']   = getValueFromPost(value: 'isAvailable', isBool: true);
        $GLOBALS['createdAt']     = getValueFromPost(value: 'createdAt');
    }

    function isExistAnyDataEmpty() {
        if(
                checkEmpty(value: $GLOBALS['image'])
            || checkEmpty(value: $GLOBALS['titleAr'])
            || checkEmpty(value: $GLOBALS['titleEn'])
            || checkEmpty(value: $GLOBALS['articleAr'])
            || checkEmpty(value: $GLOBALS['articleEn'])
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
                articles (
                    image,
                    titleAr,
                    titleEn,
                    articleAr,
                    articleEn,
                    linksTitleAr,
                    linksTitleEn,
                    links,
                    linksTitlesAr,
                    linksTitlesEn,
                    customOrder,
                    isAvailable,
                    createdAt
            )
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $values = array(
            $GLOBALS['image'],
            $GLOBALS['titleAr'],
            $GLOBALS['titleEn'],
            $GLOBALS['articleAr'],
            $GLOBALS['articleEn'],
            $GLOBALS['linksTitleAr'],
            $GLOBALS['linksTitleEn'],
            $GLOBALS['links'],
            $GLOBALS['linksTitlesAr'],
            $GLOBALS['linksTitlesEn'],
            $GLOBALS['customOrder'],
            $GLOBALS['isAvailable'],
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
                    'messageAr' => 'تم اضافة المقالة بنجاح',
                    'messageEn' => 'Article successfully added',
                ],
                'article' => getOne(table: 'articles', where: 'articleId = ?', valuesArray: array($GLOBALS['conn']->lastInsertId())),
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