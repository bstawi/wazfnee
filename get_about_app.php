<?php

    require "../conn.php";

    if($_SERVER['REQUEST_METHOD'] == 'GET') {

        $aboutApp = getOne(table: 'about_app');

        if($aboutApp) {

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'نجاح',
                    'messageEn' => 'Success',
                ],
                'aboutApp' => $aboutApp,
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