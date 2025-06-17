<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['jobId'] = getValueFromGet(value: 'jobId');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['jobId'])
        ) {
            return true;
        }
        else {
            return false;
        }
    }

    if($_SERVER['REQUEST_METHOD'] == 'GET') {

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
        else {
            
            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'نجاح',
                    'messageEn' => 'Success',
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