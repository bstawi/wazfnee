<?php

    require "../conn.php";

    function getDataFromRequest() {
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
        $GLOBALS['startTimeToday']    = getValueFromPost(value: 'startTimeToday');
        $GLOBALS['endTimeToday']      = getValueFromPost(value: 'endTimeToday');
        $GLOBALS['createdAt']         = getValueFromPost(value: 'createdAt');
    }

    function isExistAnyDataEmpty() {
        if(
                checkEmpty(value: $GLOBALS['userId'])
            || checkEmpty(value: $GLOBALS['categoryId'])
            || checkEmpty(value: $GLOBALS['countryId'])
            // || checkEmpty(value: $GLOBALS['image'])
            || checkEmpty(value: $GLOBALS['briefAboutMe'])
            || checkEmpty(value: $GLOBALS['yearsOfExperience'])
            || checkEmpty(value: $GLOBALS['universityDegree'])
            || checkEmpty(value: $GLOBALS['startTimeToday'])
            || checkEmpty(value: $GLOBALS['endTimeToday'])
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
                seekers (
                    userId,
                    categoryId,
                    countryId,
                    dialingCode,
                    whatsAppNumber,
                    emailAddress,
                    image,
                    briefAboutMe,
                    yearsOfExperience,
                    universityDegree,
                    isAvailable,
                    adStatus,
                    createdAt
                )
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $values = array(
            $GLOBALS['userId'],
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
            $GLOBALS['createdAt'],
        );
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->execute($values);
    }

    function insertSeeker() {
        insertInDatabase();

        $result = [
            'base' => [
                'status' => true,
                'messageAr' => 'تم اضافة الاعلان بنجاح',
                'messageEn' => 'Ad successfully added',
            ],
            'seeker' => getOne(table: 'seekers', where: 'seekerId = ?', valuesArray: array($GLOBALS['conn']->lastInsertId())),
        ];

        return $result;
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

            $user = getOne(table: 'users', where: 'userId = ?', valuesArray: array($GLOBALS['userId']));
            if($user['isActiveNumbersOfPublishSeekersInSameDay']) {
                if(is_null($user['numbersOfPublishSeekersInSameDay'])) { // Allowed to publish infinite numbers of seekers
                    $result = insertSeeker();
                }
                else {
                    $numbersOfPublishSeekersInSameDayForUser = countRows(table: 'seekers', where: 'userId = ? AND createdAt >= ? AND createdAt <= ?', valuesArray: array($GLOBALS['userId'], $GLOBALS['startTimeToday'], $GLOBALS['endTimeToday']));
                    if($numbersOfPublishSeekersInSameDayForUser >= $user['numbersOfPublishSeekersInSameDay']) {
                        $result = [
                            'base' => [
                                'status' => false,
                                'messageAr' => 'لقد تم استخدام الحد الأقصى للإعلانات اليوم (الحد الأقصى لليوم هو '.$user['numbersOfPublishSeekersInSameDay'].' اعلان)',
                                'messageEn' => 'The maximum number of ads has been used today (maximum '.$user['numbersOfPublishSeekersInSameDay'].' ads per day)',
                            ],
                        ];
                    }
                    else {
                        $result = insertSeeker();
                    }
                }
            }
            else {
                $config = getOne(table: 'configs', where: 'label = ?', valuesArray: array('maximumNumbersOfPublishSeekersInSameDay'));
                $numbersOfPublishSeekersInSameDayForUser = countRows(table: 'seekers', where: 'userId = ? AND createdAt >= ? AND createdAt <= ?', valuesArray: array($GLOBALS['userId'], $GLOBALS['startTimeToday'], $GLOBALS['endTimeToday']));
                if($numbersOfPublishSeekersInSameDayForUser >= $config['value']) {
                    $result = [
                        'base' => [
                            'status' => false,
                            'messageAr' => 'لقد تم استخدام الحد الأقصى للإعلانات اليوم (الحد الأقصى لليوم هو '.$config['value'].' اعلان)',
                            'messageEn' => 'The maximum number of ads has been used today (maximum '.$config['value'].' ads per day)',
                        ],
                    ];
                }
                else {
                    $result = insertSeeker();
                }
            }
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