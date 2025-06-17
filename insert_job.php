<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['userId']         = getValueFromPost(value: 'userId');
        $GLOBALS['categoryId']     = getValueFromPost(value: 'categoryId');
        $GLOBALS['countryId']      = getValueFromPost(value: 'countryId');
        $GLOBALS['dialingCode']    = getValueFromPost(value: 'dialingCode');
        $GLOBALS['whatsAppNumber'] = getValueFromPost(value: 'whatsAppNumber');
        $GLOBALS['emailAddress']   = getValueFromPost(value: 'emailAddress');
        // $GLOBALS['image']          = getValueFromPost(value: 'image');
        $GLOBALS['title']          = getValueFromPost(value: 'title');
        $GLOBALS['details']        = getValueFromPost(value: 'details');
        $GLOBALS['monthlySalary']  = getValueFromPost(value: 'monthlySalary');
        $GLOBALS['currencyId']     = getValueFromPost(value: 'currencyId');
        $GLOBALS['isHideSalary']   = getValueFromPost(value: 'isHideSalary', isBool: true);
        $GLOBALS['isAvailable']    = getValueFromPost(value: 'isAvailable', isBool: true);
        $GLOBALS['startTimeToday'] = getValueFromPost(value: 'startTimeToday');
        $GLOBALS['endTimeToday']   = getValueFromPost(value: 'endTimeToday');
        $GLOBALS['createdAt']      = getValueFromPost(value: 'createdAt');
    }

    function isExistAnyDataEmpty() {
        if(
                checkEmpty(value: $GLOBALS['userId'])
            || checkEmpty(value: $GLOBALS['categoryId'])
            || checkEmpty(value: $GLOBALS['countryId'])
            // || checkEmpty(value: $GLOBALS['image'])
            || checkEmpty(value: $GLOBALS['title'])
            || checkEmpty(value: $GLOBALS['details'])
            || checkEmpty(value: $GLOBALS['monthlySalary'])
            || checkEmpty(value: $GLOBALS['currencyId'])
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
                jobs (
                    userId,
                    categoryId,
                    countryId,
                    dialingCode,
                    whatsAppNumber,
                    emailAddress,
                    image,
                    title,
                    details,
                    monthlySalary,
                    currencyId,
                    isHideSalary,
                    isAvailable,
                    adStatus,
                    createdAt
                )
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $values = array(
            $GLOBALS['userId'],
            $GLOBALS['categoryId'],
            $GLOBALS['countryId'],
            $GLOBALS['dialingCode'],
            $GLOBALS['whatsAppNumber'],
            $GLOBALS['emailAddress'],
            '',
            $GLOBALS['title'],
            $GLOBALS['details'],
            $GLOBALS['monthlySalary'],
            $GLOBALS['currencyId'],
            $GLOBALS['isHideSalary'],
            $GLOBALS['isAvailable'],
            'pending',
            $GLOBALS['createdAt'],
        );
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->execute($values);
    }

    function insertJob() {
        insertInDatabase();
    
        $result = [
            'base' => [
                'status' => true,
                'messageAr' => 'تم اضافة الوظيفة بنجاح',
                'messageEn' => 'Job successfully added',
            ],
            'job' => getOne(table: 'jobs', where: 'jobId = ?', valuesArray: array($GLOBALS['conn']->lastInsertId())),
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
            if($user['isActiveNumbersOfPublishJobsInSameDay']) {
                if(is_null($user['numbersOfPublishJobsInSameDay'])) { // Allowed to publish infinite numbers of jobs
                    $result = insertJob();
                }
                else {
                    $numbersOfPublishJobsInSameDayForUser = countRows(table: 'jobs', where: 'userId = ? AND createdAt >= ? AND createdAt <= ?', valuesArray: array($GLOBALS['userId'], $GLOBALS['startTimeToday'], $GLOBALS['endTimeToday']));
                    if($numbersOfPublishJobsInSameDayForUser >= $user['numbersOfPublishJobsInSameDay']) {
                        $result = [
                            'base' => [
                                'status' => false,
                                'messageAr' => 'لقد تم استخدام الحد الأقصى للإعلانات اليوم (الحد الأقصى لليوم هو '.$user['numbersOfPublishJobsInSameDay'].' اعلان)',
                                'messageEn' => 'The maximum number of ads has been used today (maximum '.$user['numbersOfPublishJobsInSameDay'].' ads per day)',
                            ],
                        ];
                    }
                    else {
                        $result = insertJob();
                    }
                }
            }
            else {
                $config = getOne(table: 'configs', where: 'label = ?', valuesArray: array('maximumNumbersOfPublishJobsInSameDay'));
                $numbersOfPublishJobsInSameDayForUser = countRows(table: 'jobs', where: 'userId = ? AND createdAt >= ? AND createdAt <= ?', valuesArray: array($GLOBALS['userId'], $GLOBALS['startTimeToday'], $GLOBALS['endTimeToday']));
                if($numbersOfPublishJobsInSameDayForUser >= $config['value']) {
                    $result = [
                        'base' => [
                            'status' => false,
                            'messageAr' => 'لقد تم استخدام الحد الأقصى للإعلانات اليوم (الحد الأقصى لليوم هو '.$config['value'].' اعلان)',
                            'messageEn' => 'The maximum number of ads has been used today (maximum '.$config['value'].' ads per day)',
                        ],
                    ];
                }
                else {
                    $result = insertJob();
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