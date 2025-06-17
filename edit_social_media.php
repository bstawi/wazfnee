<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['socialMediaId'] = getValueFromPost(value: 'socialMediaId');
        $GLOBALS['image']         = getValueFromPost(value: 'image');
        $GLOBALS['nameAr']        = getValueFromPost(value: 'nameAr');
        $GLOBALS['nameEn']        = getValueFromPost(value: 'nameEn');
        $GLOBALS['link']          = getValueFromPost(value: 'link');
        $GLOBALS['isActive']      = getValueFromPost(value: 'isActive', isBool: true);
    }

    function isExistAnyDataEmpty() {
        if(
               checkEmpty(value: $GLOBALS['socialMediaId'])
            || checkEmpty(value: $GLOBALS['image'])
            || checkEmpty(value: $GLOBALS['nameAr'])
            || checkEmpty(value: $GLOBALS['nameEn'])
            || checkEmpty(value: $GLOBALS['link'])
            || checkEmpty(value: $GLOBALS['isActive'])
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
                social_media
            SET 
                image = ?,
                nameAr = ?,
                nameEn = ?,
                link = ?,
                isActive = ?
            WHERE 
                socialMediaId = ?
        ";
        $values = array(
            $GLOBALS['image'],
            $GLOBALS['nameAr'],
            $GLOBALS['nameEn'],
            $GLOBALS['link'],
            $GLOBALS['isActive'],
            $GLOBALS['socialMediaId'],
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
        else if(!isExist(table: 'social_media', where: 'socialMediaId = ?', valuesArray: array($GLOBALS['socialMediaId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'وسيلة التواصل غير موجودة',
                    'messageEn' => 'Social media not found',
                ],
            ];
        }
        else {

            editInDatabase();

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'تم تعديل وسيلة التواصل بنجاح',
                    'messageEn' => 'the social media has been modified successfully',
                ],
                'socialMedia' => getOne(table: 'social_media', where: 'socialMediaId = ?', valuesArray: array($GLOBALS['socialMediaId'])),
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