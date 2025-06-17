<?php

    require "../conn.php";


    if($_SERVER['REQUEST_METHOD'] == 'GET') {

        $configs = getAll(table: 'configs', orderBy: 'customOrder ASC');
                
        if($configs || count($configs) == 0) {

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'نجاح',
                    'messageEn' => 'Success',
                ],
                'configs' => $configs,
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