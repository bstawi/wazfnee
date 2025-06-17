<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['image']        = getValueFromPost(value: 'image');
        $GLOBALS['sliderTarget'] = getValueFromPost(value: 'sliderTarget');
        $GLOBALS['value']        = getValueFromPost(value: 'value');
        $GLOBALS['createdAt']    = getValueFromPost(value: 'createdAt');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['image'])
            || checkEmpty(value: $GLOBALS['sliderTarget'])
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
                sliders (image, sliderTarget, value, createdAt) 
            VALUES 
                (?, ?, ?, ?)
        ";
        $values = array(
            $GLOBALS['image'],
            $GLOBALS['sliderTarget'],
            $GLOBALS['value'],
            $GLOBALS['createdAt'],
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
        else {

            insertInDatabase();

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'تم اضافة البانر بنجاح',
                    'messageEn' => 'The slider has been added successfully',
                ],
                'slider' => getOne(table: 'sliders', where: 'sliderId = ?', valuesArray: array($GLOBALS['conn']->lastInsertId())),
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