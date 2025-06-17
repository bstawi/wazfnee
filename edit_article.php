<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['articleId']     = getValueFromPost(value: 'articleId');
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
    }

    function isExistAnyDataEmpty() {
        if(
               checkEmpty(value: $GLOBALS['articleId'])
            || checkEmpty(value: $GLOBALS['image'])
            || checkEmpty(value: $GLOBALS['titleAr'])
            || checkEmpty(value: $GLOBALS['titleEn'])
            || checkEmpty(value: $GLOBALS['articleAr'])
            || checkEmpty(value: $GLOBALS['articleEn'])
            || checkEmpty(value: $GLOBALS['customOrder'])
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
                articles
            SET 
                image = ?,
                titleAr = ?,
                titleEn = ?,
                articleAr = ?,
                articleEn = ?,
                linksTitleAr = ?,
                linksTitleEn = ?,
                links = ?,
                linksTitlesAr = ?,
                linksTitlesEn = ?,
                customOrder = ?,
                isAvailable = ?
            WHERE 
                articleId = ?
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
            $GLOBALS['articleId'],
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

            editInDatabase();

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'تم تعديل المقالة بنجاح',
                    'messageEn' => 'the article has been modified successfully',
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