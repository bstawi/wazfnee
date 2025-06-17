<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['seekerId']          = getValueFromPost(value: 'seekerId');
        $GLOBALS['userId']            = getValueFromPost(value: 'userId');
        $GLOBALS['categoryId']        = getValueFromPost(value: 'categoryId');
        $GLOBALS['countryId']         = getValueFromPost(value: 'countryId');
        $GLOBALS['dialingCode']       = getValueFromPost(value: 'dialingCode');
        $GLOBALS['whatsAppNumber']    = getValueFromPost(value: 'whatsAppNumber');
        $GLOBALS['emailAddress']      = getValueFromPost(value: 'emailAddress');
        // $GLOBALS['image']             = getValueFromPost(value: 'image');
        $GLOBALS['briefAboutMe']      = getValueFromPost(value: 'briefAboutMe');
        $GLOBALS['yearsOfExperience'] = getValueFromPost(value: 'yearsOfExperience');
        $GLOBALS['universityDegree']  = getValueFromPost(value: 'universityDegree');
        $GLOBALS['isAvailable']       = getValueFromPost(value: 'isAvailable', isBool: true);
    }

    function isExistAnyDataEmpty() {
        if(
               checkEmpty(value: $GLOBALS['seekerId'])
            || checkEmpty(value: $GLOBALS['userId'])
            || checkEmpty(value: $GLOBALS['categoryId'])
            || checkEmpty(value: $GLOBALS['countryId'])
            // || checkEmpty(value: $GLOBALS['image'])
            || checkEmpty(value: $GLOBALS['briefAboutMe'])
            || checkEmpty(value: $GLOBALS['yearsOfExperience'])
            || checkEmpty(value: $GLOBALS['universityDegree'])
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
                seekers
            SET 
                categoryId = ?,
                countryId = ?,
                dialingCode = ?,
                whatsAppNumber = ?,
                emailAddress = ?,
                image = ?,
                briefAboutMe = ?,
                yearsOfExperience = ?,
                universityDegree = ?,
                isAvailable = ?,
                adStatus = ?
            WHERE 
                seekerId = ?
        ";
        $values = array(
            $GLOBALS['categoryId'],
            $GLOBALS['countryId'],
            $GLOBALS['dialingCode'],
            $GLOBALS['whatsAppNumber'],
            $GLOBALS['emailAddress'],
            '',
            $GLOBALS['briefAboutMe'],
            $GLOBALS['yearsOfExperience'],
            $GLOBALS['universityDegree'],
            $GLOBALS['isAvailable'],
            'pending',
            $GLOBALS['seekerId'],
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
        else if(!isExist(table: 'seekers', where: 'seekerId = ?', valuesArray: array($GLOBALS['seekerId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'الاعلان غير موجود',
                    'messageEn' => 'Ad not found',
                ],
            ];
        }
        else if(!isExist(table: 'users', where: 'userId = ?', valuesArray: array($GLOBALS['userId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'المستخدم غير موجود',
                    'messageEn' => 'User not found',
                ],
            ];
        }
        else if(!isExist(table: 'categories', where: 'categoryId = ?', valuesArray: array($GLOBALS['categoryId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'القسم غير موجود',
                    'messageEn' => 'Category not found',
                ],
            ];
        }
        else {

            editInDatabase();

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'تم تعديل الاعلان بنجاح',
                    'messageEn' => 'the ad has been modified successfully',
                ],
                'seeker' => getOne(table: 'seekers', where: 'seekerId = ?', valuesArray: array($GLOBALS['seekerId'])),
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