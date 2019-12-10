<?php
namespace classes\model;

use classes\mapper\UrlaubDAO;

class Resturlaub {
    public static function berechneResturlaub($arrObjUrlaub, $urlaubGesamt) {
        $urlaubDAO = new UrlaubDAO();
        $currentYear = date('Y');
        $nextYear = date('Y', strtotime('+1 year'));
        $restUrlaubCurrentYear = $urlaubGesamt;
        $restUrlaubNextYear = $urlaubGesamt;
        $arrResturlaubCurrentAndNextYear = [];
        foreach($arrObjUrlaub as $urlaub) {
            if($urlaub->getStatus() === 'G' || $urlaub->getStatus() === 'P') {
                if(date('Y', strtotime($urlaub->getBeginn())) === $currentYear) {
                    $restUrlaubCurrentYear -= $urlaub->getTage();
                }
                if(date('Y', strtotime($urlaub->getBeginn())) === $nextYear) {
                    $restUrlaubNextYear -= $urlaub->getTage();
                }
            }
        }
        if(count($arrObjUrlaub) > 0) {
            $restUrlaubCurrentYear += $urlaubDAO->readAuByPnr($arrObjUrlaub[0]->getPnr());
        }
        $arrResturlaubCurrentAndNextYear['restUrlaubCurrentYear'] = $restUrlaubCurrentYear;
        $arrResturlaubCurrentAndNextYear['restUrlaubNextYear'] = $restUrlaubNextYear;
        return $arrResturlaubCurrentAndNextYear;
    }
}
?>