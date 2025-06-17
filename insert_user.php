<?php

    require "../conn.php";

    function getDataFromRequest() {
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
        $GLOBALS['signInMethod']                             = getValueFromPost(value: 'signInMethod');
        $GLOBALS['createdAt']                                = getValueFromPost(value: 'createdAt');
    }

    function isExistAnyDataEmpty() {
        if(
               checkEmpty(value: $GLOBALS['fullName'])
            || checkEmpty(value: $GLOBALS['emailAddress'])
            || checkEmpty(value: $GLOBALS['password'])
            || checkEmpty(value: $GLOBALS['signInMethod'])
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
                users (
                    fullName,
                    personalImage,
                    coverImage,
                    bio,
                    emailAddress,
                    password,
                    dialingCode,
                    phoneNumber,
                    birthDate,
                    countryId,
                    gender,
                    latitude,
                    longitude,
                    balance,
                    isActiveNumbersOfPublishJobsInSameDay,
                    isActiveNumbersOfPublishSeekersInSameDay,
                    numbersOfPublishJobsInSameDay,
                    numbersOfPublishSeekersInSameDay,
                    isFeatured,
                    signInMethod,
                    createdAt
                )
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
            $GLOBALS['signInMethod'],
            $GLOBALS['createdAt'],
        );
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->execute($values);

        $GLOBALS['userId'] = $GLOBALS['conn']->lastInsertId();
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
        else if(!filter_var($GLOBALS['emailAddress'], FILTER_VALIDATE_EMAIL)) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'صيغة البريد الالكترونى غير صحيحة',
                    'messageEn' => 'The email address is not valid',
                ],
            ];
        }
        else if(isExist(table: 'users', where: 'phoneNumber = ?', valuesArray: array($GLOBALS['phoneNumber']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'يوجد بالفعل نفس رقم الهاتف مسجل من قبل',
                    'messageEn' => 'There is the same phone number registered before',
                ],
            ];
        }
        else if(isExist('users', 'emailAddress = ?', array($GLOBALS['emailAddress']))) {

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
            
            insertInDatabase();

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'تم انشاء المستخدم بنجاح',
                    'messageEn' => 'User successfully created',
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