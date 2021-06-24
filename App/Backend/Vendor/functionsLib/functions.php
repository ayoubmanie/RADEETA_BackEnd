<?php

function array_keys_exists(array $keys, array $arr)
{
    return !array_diff_key(array_flip($keys), $arr);
}

function formErrorMsg($entity)
{
    $entityName = get_class($entity);
    $msg = $entityName . " must be valid ";
    if (!empty($entity->erreurs())) {
        $msg .= ", error code : ";
        foreach ($entity->erreurs() as $key => $value) {
            $msg .= " '" . $value . "', ";
        }
    }
    return $msg;
}


function validDate($attribute)
{
    $test_arr  = explode('-', $attribute);
    if (count($test_arr) == 3) {
        //checkdate ( $month, $day, $year )
        if (!checkdate($test_arr[1], $test_arr[2], $test_arr[0])) {
            return false;
        }
    } else {
        return false;
    }
    return true;
}

function validDateTime($attribute)
{
    $array = explode(" ", $attribute);

    validDate($array[0]);
    validTime($array[1]);

    return true;
}

function validTime($attribute)
{
    $matches = preg_match('/^(([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?)$/', $attribute);

    if ($matches == 0) return false;
    else return true;
}