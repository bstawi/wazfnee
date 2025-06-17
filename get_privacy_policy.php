<?php

    require "../conn.php";

    if($_SERVER['REQUEST_METHOD'] == 'GET') {

        $privacyPolicy = getOne(table: 'privacy_policy');

        if($privacyPolicy) {

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'نجاح',
                    'messageEn' => 'Success',
                ],
                'privacyPolicy' => $privacyPolicy,
            ];
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