<?php
namespace classes\commands;

use classes\model\Datum;
use classes\model\Feiertage;
use classes\model\Resturlaub;
use classes\model\Urlaub;
use classes\request\Request;
use classes\response\Response;
use classes\template\HtmlTemplateView;
use classes\mapper\MitarbeiterDAO;
use classes\mapper\AbteilungDAO;
use classes\mapper\FunktionDAO;
use classes\mapper\UrlaubDAO;

class BetriebsurlaubVerwaltenCommand implements Command {
    public function execute(Request $request, Response $response) {
        $status = $_SESSION['status'];
        $meldung = '';
        $nav = $_SESSION['nav'];
        $view = 'betriebsurlaubVerwalten';

        $mitarbeiterDAO = new MitarbeiterDAO();
        $abteilungDAO = new AbteilungDAO();
        $funktionDAO = new FunktionDAO();
        $urlaubDAO = new UrlaubDAO();
        $objFeiertage = new Feiertage();
        $currentYear = date('Y');
        $nextYear = date('Y', strtotime('+1 year'));

        // Feiertage per Klasse berechnen und übergeben:
        $arrCurrentFeiertage = $objFeiertage->getArrAlleCurrentFeiertage();
        $arrNextFeiertage = $objFeiertage->getArrAlleNextFeiertage();
        $arrWochentage = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'];

        // Betriebsurlaub aus DB holen und übergeben:
        $arrBetriebsurlaub = $urlaubDAO->readByPnr(0);

        $arrObjMitarbeiter = $mitarbeiterDAO->readAll();
        $arrObjAbteilung = $abteilungDAO->readAll();
        $arrObjFunktion = $funktionDAO->readAll();

        // Eingabefelder Betriebsurlaub planen vorbelegen falls letzte Eingabe fehlerhaft:
        if($request->issetParameter('beantragen_beginn')) {
            $beantragenBeginnVorbelegen = $request->getParameter('beantragen_beginn');
        } else {
            $beantragenBeginnVorbelegen = '';
        }
        if($request->issetParameter('beantragen_ende')) {
            $beantragenEndeVorbelegen = $request->getParameter('beantragen_ende');
        } else {
            $beantragenEndeVorbelegen = '';
        }

        // Formulareingabe Urlaub planen auswerten:
        if($request->issetParameter('beantragen_submit')) {
            $fehler = false;
            // Prüfen ob alle Felder ausgefüllt:
            if(empty($request->getParameter('beantragen_beginn')) || empty($request->getParameter('beantragen_ende')) ||
                (empty($request->getParameter('beantragen_tage'))&&$request->getParameter('beantragen_tage')!=0)) {
                $fehler = true;
                $meldung = 'Bitte alle Felder ausfüllen!<br>';
            } else {
                $beantragenBeginn = $request->getParameter('beantragen_beginn');
                $beantragenEnde = $request->getParameter('beantragen_ende');
                // Prüfen ob Datumswerte gültig:
                if(!(Datum::dateCheckUS($beantragenBeginn) || Datum::dateCheckGerman($beantragenBeginn))) {
                    $fehler = true;
                    $meldung .= 'Ungültige Eingabe für Startdatum!<br>';
                }
                if(!(Datum::dateCheckUS($beantragenEnde) || Datum::dateCheckGerman($beantragenEnde))) {
                    $fehler = true;
                    $meldung .= 'Ungültige Eingabe für Enddatum!<br>';
                }
                // Datumsformat anpassen:
                if($fehler === false) {
                    if(!Datum::dateCheckUS($beantragenBeginn)) {
                        $beantragenBeginn = Datum::convertToUS($beantragenBeginn);
                    }
                    if(!Datum::dateCheckUS($beantragenEnde)) {
                        $beantragenEnde = Datum::convertToUS($beantragenEnde);
                    }
                    // Weitere Überprüfungen der Eingabe (siehe jeweilige '$meldung .= ...'-Zeile!):
                    if ($beantragenBeginn > $beantragenEnde) {
                        $fehler = true;
                        $meldung .= 'Ende liegt vor Beginn!<br>';
                    }
                    if (strtotime($beantragenBeginn) < time()) {
                        $fehler = true;
                        $meldung .= 'Beginn muss in der Zukunft liegen!<br>';
                    }
                    if (date('D', strtotime($beantragenBeginn)) === 'Sat' || date('D', strtotime($beantragenBeginn)) === 'Sun' ||
                        date('D', strtotime($beantragenEnde)) === 'Sat' || date('D', strtotime($beantragenEnde)) === 'Sun' ||
                        in_array(strtotime($beantragenBeginn), $arrCurrentFeiertage, true) || in_array(strtotime($beantragenBeginn), $arrNextFeiertage, true) ||
                        in_array(strtotime($beantragenEnde), $arrCurrentFeiertage, true) || in_array(strtotime($beantragenEnde), $arrNextFeiertage, true)) {
                        $fehler = true;
                        $meldung .= 'Beginn und/oder Ende dürfen nicht auf einen Samstag, Sonntag oder Feiertag fallen!<br>';
                    }
                    if (date('Y', strtotime($beantragenBeginn)) < $currentYear || (date('Y', strtotime($beantragenBeginn)) > $nextYear)) {
                        $fehler = true;
                        $meldung .= 'Beginn muss im laufenden oder nächsten Jahr liegen!<br>';
                    }
                }

                // Antrag in DB eintragen wenn kein Fehler:
                if($fehler === false) {
                    $objUrlaubBeantragen = new Urlaub(0, 0, $beantragenBeginn, $beantragenEnde, 0, 1, 'B');
                    $urlaubDAO->create($objUrlaubBeantragen);
                    if($_SESSION['bstufe'] >= 3) {
                        $_SESSION['pendingCount'] = $urlaubDAO->readPendingCount();
                    }
                    $beantragenBeginnVorbelegen = '';
                    $beantragenEndeVorbelegen = '';
                }
            }
        }

        // Einzelne Betriebsurlaube per Button löschbar:
        if($request->issetParameter('antragLoeschen')) {
            $urlaubDAO->deleteByUnr($request->getParameter('unr'));
        }

        // Urlaubsdaten aktualisieren:-------------------------------------------------------------
        $arrBetriebsurlaub = $urlaubDAO->readByPnr(0);

        $template = new HtmlTemplateView($view);
        $template->assign('status', $status);
        $template->assign('meldung', $meldung);
        $template->assign('arrObjMitarbeiter', $arrObjMitarbeiter);
        $template->assign('arrObjAbteilung', $arrObjAbteilung);
        $template->assign('arrObjFunktion', $arrObjFunktion);
//        $template->assign('arrRestUrlaub', $arrRestUrlaub);
        $template->assign('arrWochentage', $arrWochentage);
        $template->assign('arrCurrentFeiertage', $arrCurrentFeiertage);
        $template->assign('arrNextFeiertage', $arrNextFeiertage);
        $template->assign('arrBetriebsurlaub', $arrBetriebsurlaub);
        $template->assign('beantragenBeginnVorbelegen', $beantragenBeginnVorbelegen);
        $template->assign('beantragenEndeVorbelegen', $beantragenEndeVorbelegen);
        $template->assign('nav', $nav);
        $template->render($request, $response);
    }
}

?>