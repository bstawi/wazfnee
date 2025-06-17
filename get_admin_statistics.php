<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['startTimeToday'] = getValueFromGet(value: 'startTimeToday');
        $GLOBALS['endTimeToday']   = getValueFromGet(value: 'endTimeToday');
        $GLOBALS['startThisMonth'] = getValueFromGet(value: 'startThisMonth');
        $GLOBALS['endThisMonth']   = getValueFromGet(value: 'endThisMonth');
        $GLOBALS['startLastMonth'] = getValueFromGet(value: 'startLastMonth');
        $GLOBALS['endLastMonth']   = getValueFromGet(value: 'endLastMonth');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['startTimeToday'])
            || checkEmpty(value: $GLOBALS['endTimeToday'])
            || checkEmpty(value: $GLOBALS['startThisMonth'])
            || checkEmpty(value: $GLOBALS['endThisMonth'])
            || checkEmpty(value: $GLOBALS['startLastMonth'])
            || checkEmpty(value: $GLOBALS['endLastMonth'])
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
        else {

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'نجاح',
                    'messageEn' => 'Success',
                ],
                'adminStatistics' => [
                    [
                        'label' => 'admins',
                        'titleAr' => 'المسئولين',
                        'titleEn' => 'Admins',
                        'count' => countRows(table: 'admins'),
                    ],
                    [
                        'label' => 'users',
                        'titleAr' => 'المستخدمين',
                        'titleEn' => 'Users',
                        'count' => countRows(table: 'users'),
                    ],
                    [
                        'label' => 'categories',
                        'titleAr' => 'الفئات',
                        'titleEn' => 'Categories',
                        'count' => countRows(table: 'categories'),
                    ],
                    [
                        'label' => 'sliders',
                        'titleAr' => 'البانرات',
                        'titleEn' => 'Sliders',
                        'count' => countRows(table: 'sliders'),
                    ],
                    [
                        'label' => 'articles',
                        'titleAr' => 'المقالات',
                        'titleEn' => 'Articles',
                        'count' => countRows(table: 'articles'),
                    ],
                    [
                        'label' => 'complaints',
                        'titleAr' => 'الشكاوى',
                        'titleEn' => 'Complaints',
                        'count' => countRows(table: 'complaints'),
                    ],
                    [
                        'label' => 'activeJobs',
                        'titleAr' => 'وظائف نشطة',
                        'titleEn' => 'Active Jobs',
                        'count' => countRows(table: 'jobs', where: 'adStatus = ?', valuesArray: array('active')),
                    ],
                    [
                        'label' => 'pendingJobs',
                        'titleAr' => 'وظائف قيد المراجعة',
                        'titleEn' => 'Pending Jobs',
                        'count' => countRows(table: 'jobs', where: 'adStatus = ?', valuesArray: array('pending')),
                    ],
                    [
                        'label' => 'rejectedJobs',
                        'titleAr' => 'وظائف مرفوضة',
                        'titleEn' => 'Rejected Jobs',
                        'count' => countRows(table: 'jobs', where: 'adStatus = ?', valuesArray: array('rejected')),
                    ],
                    [
                        'label' => 'activeSeekers',
                        'titleAr' => 'باحثين نشطين',
                        'titleEn' => 'Active Job Seekers',
                        'count' => countRows(table: 'seekers', where: 'adStatus = ?', valuesArray: array('active')),
                    ],
                    [
                        'label' => 'pendingSeekers',
                        'titleAr' => 'باحثين قيد المراجعة',
                        'titleEn' => 'Pending Job Seekers',
                        'count' => countRows(table: 'seekers', where: 'adStatus = ?', valuesArray: array('pending')),
                    ],
                    [
                        'label' => 'rejectedSeekers',
                        'titleAr' => 'باحثين مرفوضين',
                        'titleEn' => 'Rejected Job Seekers',
                        'count' => countRows(table: 'seekers', where: 'adStatus = ?', valuesArray: array('rejected')),
                    ],
                    [
                        'label' => 'jobs',
                        'titleAr' => 'الوظائف',
                        'titleEn' => 'Jobs',
                        'count' => countRows(table: 'jobs'),
                    ],
                    [
                        'label' => 'jobSeekers',
                        'titleAr' => 'الباحثين عن عمل',
                        'titleEn' => 'Job Seekers',
                        'count' => countRows(table: 'seekers'),
                    ],
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