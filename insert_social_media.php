<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['image']     = getValueFromPost(value: 'image');
        $GLOBALS['nameAr']    = getValueFromPost(value: 'nameAr');
        $GLOBALS['nameEn']    = getValueFromPost(value: 'nameEn');
        $GLOBALS['link']      = getValueFromPost(value: 'link');
        $GLOBALS['isActive']  = getValueFromPost(value: 'isActive', isBool: true);
        $GLOBALS['createdAt'] = getValueFromPost(value: 'createdAt');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['image'])
            || checkEmpty(value: $GLOBALS['nameAr'])
            || checkEmpty(value: $GLOBALS['nameEn'])
            || checkEmpty(value: $GLOBALS['link'])
            || checkEmpty(value: $GLOBALS['isActive'])
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
                social_media (image, nameAr, nameEn, link, isActive, createdAt)
            VALUES
                (?, ?, ?, ?, ?, ?)
        ";
        $values = array(
            $GLOBALS['image'],
            $GLOBALS['nameAr'],
            $GLOBALS['nameEn'],
            $GLOBALS['link'],
            $GLOBALS['isActive'],
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
                    'messageAr' => 'تم اضافة وسيلة التواصل بنجاح',
                    'messageEn' => 'Social media successfully added',
                ],
                'socialMedia' => getOne(table: 'social_media', where: 'socialMediaId = ?', valuesArray: array($GLOBALS['conn']->lastInsertId())),
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