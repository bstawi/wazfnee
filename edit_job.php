<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['jobId']          = getValueFromPost(value: 'jobId');
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
    }

    function isExistAnyDataEmpty() {
        if(
               checkEmpty(value: $GLOBALS['jobId'])
            || checkEmpty(value: $GLOBALS['userId'])
            || checkEmpty(value: $GLOBALS['categoryId'])
            || checkEmpty(value: $GLOBALS['countryId'])
            // || checkEmpty(value: $GLOBALS['image'])
            || checkEmpty(value: $GLOBALS['title'])
            || checkEmpty(value: $GLOBALS['details'])
            || checkEmpty(value: $GLOBALS['monthlySalary'])
            || checkEmpty(value: $GLOBALS['currencyId'])
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
                jobs
            SET 
                categoryId = ?,
                countryId = ?,
                dialingCode = ?,
                whatsAppNumber = ?,
                emailAddress = ?,
                image = ?,
                title = ?,
                details = ?,
                monthlySalary = ?,
                currencyId = ?,
                isHideSalary = ?,
                isAvailable = ?,
                adStatus = ?
            WHERE 
                jobId = ?
        ";
        $values = array(
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
            $GLOBALS['jobId'],
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
        else if(!isExist(table: 'jobs', where: 'jobId = ?', valuesArray: array($GLOBALS['jobId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'الوظيفة غير موجودة',
                    'messageEn' => 'Job not found',
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
                    'messageAr' => 'تم تعديل الوظيفة بنجاح',
                    'messageEn' => 'the job has been modified successfully',
                ],
                'job' => getOne(table: 'jobs', where: 'jobId = ?', valuesArray: array($GLOBALS['jobId'])),
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