<?php

    require "../conn.php";

    if($_SERVER['REQUEST_METHOD'] == 'GET') {

        $serviceDescription = getOne(table: 'service_description');

        if($serviceDescription) {

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'نجاح',
                    'messageEn' => 'Success',
                ],
                'serviceDescription' => $serviceDescription,
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