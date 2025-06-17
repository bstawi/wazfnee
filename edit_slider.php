<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['sliderId']     = getValueFromPost(value: 'sliderId');
        $GLOBALS['image']        = getValueFromPost(value: 'image');
        $GLOBALS['sliderTarget'] = getValueFromPost(value: 'sliderTarget');
        $GLOBALS['value']        = getValueFromPost(value: 'value');
    }

    function isExistAnyDataEmpty() {
        if(
            checkEmpty(value: $GLOBALS['sliderId'])
            || checkEmpty(value: $GLOBALS['image'])
            || checkEmpty(value: $GLOBALS['sliderTarget'])
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
                sliders 
            SET 
                image = ?,
                sliderTarget = ?,
                value = ?
            WHERE 
                sliderId = ?
        ";
        $values = array(
            $GLOBALS['image'],
            $GLOBALS['sliderTarget'],
            $GLOBALS['value'],
            $GLOBALS['sliderId'],
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
        else if(!isExist(table: 'sliders', where: 'sliderId = ?', valuesArray: array($GLOBALS['sliderId']))) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'البانر غير موجود',
                    'messageEn' => 'Slider not found',
                ],
            ]; 
        }
        else {

            editInDatabase();

            $result = [
                'base' => [
                    'status' => true,
                    'messageAr' => 'تم تعديل البانر بنجاح',
                    'messageEn' => 'The slider has been modified successfully',
                ],
                'slider' => getOne(table: 'sliders', where: 'sliderId = ?', valuesArray: array($GLOBALS['sliderId'])),
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