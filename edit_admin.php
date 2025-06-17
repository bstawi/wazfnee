<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['adminId']       = getValueFromPost(value: 'adminId');
        $GLOBALS['fullName']      = getValueFromPost(value: 'fullName');
        $GLOBALS['personalImage'] = getValueFromPost(value: 'personalImage');
        $GLOBALS['coverImage']    = getValueFromPost(value: 'coverImage');
        $GLOBALS['bio']           = getValueFromPost(value: 'bio');
        $GLOBALS['emailAddress']  = getValueFromPost(value: 'emailAddress');
        $GLOBALS['password']      = getValueFromPost(value: 'password');
        $GLOBALS['dialingCode']   = getValueFromPost(value: 'dialingCode');
        $GLOBALS['phoneNumber']   = getValueFromPost(value: 'phoneNumber');
        $GLOBALS['birthDate']     = getValueFromPost(value: 'birthDate');
        $GLOBALS['countryId']     = getValueFromPost(value: 'countryId');
        $GLOBALS['gender']        = getValueFromPost(value: 'gender');
        $GLOBALS['permissions']   = getValueFromPost(value: 'permissions');
        $GLOBALS['isSuper']       = getValueFromPost(value: 'isSuper', isBool: true);
    }

    function isExistAnyDataEmpty() {
        if(
               checkEmpty(value: $GLOBALS['adminId'])
            || checkEmpty(value: $GLOBALS['fullName'])
            || checkEmpty(value: $GLOBALS['emailAddress'])
            || checkEmpty(value: $GLOBALS['password'])
            || checkEmpty(value: $GLOBALS['isSuper'])
        ) {
            return true;
        }
        else {
            return false;
        }
    }

    function editAdminInDatabase() {
        $sql = "
            UPDATE 
                admins
            SET 
                fullName = ?,
                personalImage = ?,
                coverImage = ?,
                bio = ?,
                emailAddress = ?,
                password = ?,
                dialingCode = ?,
                phoneNumber = ?,
                birthDate = ?,
                countryId = ?,
                gender = ?,
                permissions = ?,
                isSuper = ?
            WHERE 
                adminId = ?
        ";
        $values = array(
            $GLOBALS['fullName'],
            $GLOBALS['personalImage'],
            $GLOBALS['coverImage'],
            $GLOBALS['bio'],
            $GLOBALS['emailAddress'],
            $GLOBALS['password'],
            $GLOBALS['dialingCode'],
            $GLOBALS['phoneNumber'],
            $GLOBALS['birthDate'],
            $GLOBALS['countryId'],
            $GLOBALS['gender'],
            $GLOBALS['permissions'],
            $GLOBALS['isSuper'],
            $GLOBALS['adminId'],
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
        else if(!isExist(table: 'admins', where: 'adminId = ?', valuesArray: array($GLOBALS['adminId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'الادمن غير موجود',
                    'messageEn' => 'Admin not found',
                ],
            ];
        }
        else if(!filter_var($GLOBALS['emailAddress'], FILTER_VALIDATE_EMAIL)) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'صيغة البريد الالكترونى غير صحيحة',
                    'messageEn' => 'The email address is not valid',
                ],
            ];
        }
        else if(isExist(table: 'admins', where: 'phoneNumber = ? AND adminId != ?', valuesArray: array($GLOBALS['phoneNumber'], $GLOBALS['adminId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'يوجد بالفعل نفس رقم الهاتف مسجل من قبل',
                    'messageEn' => 'There is the same phone number registered before',
                ],
            ];
        }
        else if(isExist(table: 'admins', where: 'emailAddress = ? AND adminId != ?', valuesArray: array($GLOBALS['emailAddress'], $GLOBALS['adminId']))) {    

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'يوجد بالفعل ادمن بعنوان البريد الإلكترونى المحدد',
                    'messageEn' => 'Already exists an admin with the given email address',
                ],
            ];
        }
        else if(strlen($GLOBALS['password']) < 6) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'كلمة السر ليست قوية جرب كلمة سر اخرى',
                    'messageEn' => 'The password is not strong enough',
                ],
            ];
        }
        else {

            editAdminInDatabase();

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'تم تعديل الادمن بنجاح',
                    'messageEn' => 'the admin has been modified successfully',
                ],
                'admin' => getOne(table: 'admins', where: 'adminId = ?', valuesArray: array($GLOBALS['adminId'])),
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