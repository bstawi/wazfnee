<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['userId']                                   = getValueFromPost(value: 'userId');
        $GLOBALS['fullName']                                 = getValueFromPost(value: 'fullName');
        $GLOBALS['personalImage']                            = getValueFromPost(value: 'personalImage');
        $GLOBALS['coverImage']                               = getValueFromPost(value: 'coverImage');
        $GLOBALS['bio']                                      = getValueFromPost(value: 'bio');
        $GLOBALS['emailAddress']                             = getValueFromPost(value: 'emailAddress');
        $GLOBALS['password']                                 = getValueFromPost(value: 'password');
        $GLOBALS['dialingCode']                              = getValueFromPost(value: 'dialingCode');
        $GLOBALS['phoneNumber']                              = getValueFromPost(value: 'phoneNumber');
        $GLOBALS['birthDate']                                = getValueFromPost(value: 'birthDate');
        $GLOBALS['countryId']                                = getValueFromPost(value: 'countryId');
        $GLOBALS['gender']                                   = getValueFromPost(value: 'gender');
        $GLOBALS['latitude']                                 = getValueFromPost(value: 'latitude');
        $GLOBALS['longitude']                                = getValueFromPost(value: 'longitude');
        $GLOBALS['balance']                                  = getValueFromPost(value: 'balance');
        $GLOBALS['isActiveNumbersOfPublishJobsInSameDay']    = getValueFromPost(value: 'isActiveNumbersOfPublishJobsInSameDay', isBool: true);
        $GLOBALS['isActiveNumbersOfPublishSeekersInSameDay'] = getValueFromPost(value: 'isActiveNumbersOfPublishSeekersInSameDay', isBool: true);
        $GLOBALS['numbersOfPublishJobsInSameDay']            = getValueFromPost(value: 'numbersOfPublishJobsInSameDay');
        $GLOBALS['numbersOfPublishSeekersInSameDay']         = getValueFromPost(value: 'numbersOfPublishSeekersInSameDay');
        $GLOBALS['isFeatured']                               = getValueFromPost(value: 'isFeatured', isBool: true);
    }

    function isExistAnyDataEmpty() {
        if(
               checkEmpty(value: $GLOBALS['userId'])
            || checkEmpty(value: $GLOBALS['fullName'])
            || checkEmpty(value: $GLOBALS['emailAddress'])
            || checkEmpty(value: $GLOBALS['password'])
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
                users
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
                latitude = ?,
                longitude = ?,
                balance = ?,
                isActiveNumbersOfPublishJobsInSameDay = ?,
                isActiveNumbersOfPublishSeekersInSameDay = ?,
                numbersOfPublishJobsInSameDay = ?,
                numbersOfPublishSeekersInSameDay = ?,
                isFeatured = ?
            WHERE 
                userId = ?
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
            $GLOBALS['latitude'],
            $GLOBALS['longitude'],
            $GLOBALS['balance'],
            $GLOBALS['isActiveNumbersOfPublishJobsInSameDay'],
            $GLOBALS['isActiveNumbersOfPublishSeekersInSameDay'],
            $GLOBALS['numbersOfPublishJobsInSameDay'],
            $GLOBALS['numbersOfPublishSeekersInSameDay'],
            $GLOBALS['isFeatured'],
            $GLOBALS['userId'],
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
        else if(!isExist(table: 'users', where: 'userId = ?', valuesArray: array($GLOBALS['userId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'المستخدم غير موجود',
                    'messageEn' => 'User not found',
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
        else if(isExist(table: 'users', where: 'phoneNumber = ? AND userId != ?', valuesArray: array($GLOBALS['phoneNumber'], $GLOBALS['userId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'يوجد بالفعل نفس رقم الهاتف مسجل من قبل',
                    'messageEn' => 'There is the same phone number registered before',
                ],
            ];
        }
        else if(isExist(table: 'users', where: 'emailAddress = ? AND userId != ?', valuesArray: array($GLOBALS['emailAddress'], $GLOBALS['userId']))) {    

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'يوجد بالفعل مستخدم بعنوان البريد الإلكترونى المحدد',
                    'messageEn' => 'Already exists an user with the given email address',
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

            editInDatabase();

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'تم تعديل المستخدم بنجاح',
                    'messageEn' => 'the user has been modified successfully',
                ],
                'user' => getOne(table: 'users', where: 'userId = ?', valuesArray: array($GLOBALS['userId'])),
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