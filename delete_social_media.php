<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['socialMediaId'] = getValueFromPost(value: 'socialMediaId');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['socialMediaId'])
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
            
            deleteWithId(table: 'social_media', column: 'socialMediaId', id: $GLOBALS['socialMediaId']);

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'تم حذف وسيلة التواصل بنجاح',
                    'messageEn' => 'The social media has been deleted successfully',
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