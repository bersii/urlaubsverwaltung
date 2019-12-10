<?php
namespace classes\model;

class Datum {
    public static function dateCheckGerman($date) {
        $arrDate = explode('.', $date);
        if(count($arrDate)===3 && is_numeric($arrDate[0])===true && is_numeric($arrDate[1])===true && is_numeric($arrDate[2])===true) {
            if(checkdate($arrDate[1], $arrDate[0], (int)$arrDate[2]) === true) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function dateCheckUS($date) {
        $arrDate = explode('-', $date);
        if(count($arrDate)===3 && is_numeric($arrDate[0])===true && is_numeric($arrDate[1])===true && is_numeric($arrDate[2])===true) {
            if(checkdate($arrDate[1], $arrDate[2], (int)$arrDate[0]) === true) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function convertToGerman($date) {
        return date('d.m.Y', strtotime($date));
    }

    public static function convertToUS($date) {
        return date('Y-m-d', strtotime($date));
    }
}
?>