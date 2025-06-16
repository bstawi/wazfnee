<?php

function convertToBool($value) {
    if(is_null($value) || $value == 'null') return null;
    else if($value == 1 || $value == true) return true;
    else return false;
}

function getValueFromPost($value, $isBool = false) {
    if(isset($_POST[$value]) && $_POST[$value] != 'null') {
        if($isBool) {
            if($_POST[$value] == 'true' || $_POST[$value] == 1) return 1;
            elseif($_POST[$value] == 'false' || $_POST[$value] == 0) return 0;
            else return null;
        }
        else {
            return $_POST[$value];
        }
    }
    else {
        return null;
    }
}

function getValueFromGet($value, $isBool = false) {
    if(isset($_GET[$value]) && $_GET[$value] != 'null') {
        if($isBool) {
            if($_GET[$value] == 'true' || $_GET[$value] == 1) return 1;
            elseif($_GET[$value] == 'false' || $_GET[$value] == 0) return 0;
            else return null;
        }
        else {
            return $_GET[$value];
        }
    }
    else {
        return null;
    }
}

function checkEmpty($value) {
    return (is_null($value) || empty($value) && $value != 0);
}

function isNumberLargeThanZero($num) : bool {
    return is_numeric($num) && (int) $num > 0;
}

function isPageRangeValid($page, $pagesNumber) : bool {
    return $page >= 1 && $page <= $pagesNumber;
}

$userTypes = array('user');