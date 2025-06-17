<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['jobId'] = getValueFromPost(value: 'jobId');
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
        else {
            
            deleteWithId(table: 'jobs', column: 'jobId', id: $GLOBALS['jobId']);

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'تم حذف الوظيفة بنجاح',
                    'messageEn' => 'The job has been deleted successfully',
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