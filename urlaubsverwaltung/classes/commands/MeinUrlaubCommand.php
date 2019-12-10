<?php
namespace classes\commands;

use classes\request\Request;
use classes\response\Response;
use classes\template\HtmlTemplateView;
use classes\model\Feiertage;
use classes\model\Datum;
use classes\model\Resturlaub;
use classes\model\Urlaub;
use classes\model\Login;
use classes\mapper\UrlaubDAO;
use classes\mapper\LoginDAO;
use DateTime;

class MeinUrlaubCommand implements Command {
    public function execute(Request $request, Response $response) {
        $vorname = $_SESSION['vorname'];
        $username = $_SESSION['username'];
        $pnr = $_SESSION['pnr'];
        $urlaubGesamt = $_SESSION['uges'];
        $status = $_SESSION['status'];
        $meldung = '';
        $nav = $_SESSION['nav'];
        $view = 'meinUrlaub';
        $currentYear = date('Y');
        $nextYear = date('Y', strtotime('+1 year'));
        $urlaubDAO = new UrlaubDAO();

        // Feiertage per Klasse berechnen und übergeben:-------------------------------------------
        $objFeiertage = new Feiertage();
        $arrCurrentFeiertage = $objFeiertage->getArrAlleCurrentFeiertage();
        $arrNextFeiertage = $objFeiertage->getArrAlleNextFeiertage();
        $arrWochentage = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'];

        // Betriebsurlaub aus DB holen und übergeben:----------------------------------------------
        $arrBetriebsurlaub = $urlaubDAO->readByPnr(0);

        // Eingabefelder Urlaub planen vorbelegen falls letzte Eingabe fehlerhaft:-----------------
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
        if($request->issetParameter('beantragen_tage')) {
            $beantragenTageVorbelegen = $request->getParameter('beantragen_tage');
        } else {
            $beantragenTageVorbelegen = '';
        }

        // Formulareingabe Urlaub planen auswerten:
        if($request->issetParameter('beantragen_submit') || $request->issetParameter('isUrlaubPlanenAjax')) {
            $fehler = false;
            // Prüfen ob alle Felder ausgefüllt:---------------------------------------------------
            if(empty($request->getParameter('beantragen_beginn')) || empty($request->getParameter('beantragen_ende')) ||
              (empty($request->getParameter('beantragen_tage'))&&$request->getParameter('beantragen_tage')!=0)) {
                $fehler = true;
                $meldung = 'Bitte alle Felder ausfüllen!<br>';
                // Fehlermeldung per Ajax an Modal-Box:
                if($request->issetParameter('isUrlaubPlanenAjax')) {
                    echo '<br>' . $meldung;
                    exit();
                }
            } else {
                $beantragenBeginn = $request->getParameter('beantragen_beginn');
                $beantragenEnde = $request->getParameter('beantragen_ende');
                $beantragenTage = $request->getParameter('beantragen_tage');

                // Prüfen ob Datumswerte gültig:---------------------------------------------------
                if(!(Datum::dateCheckUS($beantragenBeginn) || Datum::dateCheckGerman($beantragenBeginn))) {
                    $fehler = true;
                    $meldung .= 'Ungültige Eingabe für Startdatum!<br>';
                }
                if(!(Datum::dateCheckUS($beantragenEnde) || Datum::dateCheckGerman($beantragenEnde))) {
                    $fehler = true;
                    $meldung .= 'Ungültige Eingabe für Enddatum!<br>';
                }
                if(!is_numeric($beantragenTage) || $beantragenTage <= 0) {
                    $fehler = true;
                    $meldung .= 'Bitte eine Zahl größer 0 für Tage eingeben!<br>';
                }

                // Datumsformat anpassen:----------------------------------------------------------
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
                        $meldung .= 'Beginn muss vor Ende liegen!<br>';
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
                    if (date('Y', strtotime($beantragenBeginn)) != date('Y', strtotime($beantragenEnde))) {
                        $fehler = true;
                        $meldung .= 'Beginn und Ende müssen im selben Jahr liegen!<br>
                                     (ggf. zwei Urlaube beantragen!)<br>';
                    }

                    // Erzwingen dass zuerst der Betriebsurlaub verplant wird:---------------------
                    $arrObjUrlaub = $urlaubDAO->readByPnr($pnr);
                    $arrBetriebsurlaubIsNotGebuchtCurrentYear = [];
                    $arrBetriebsurlaubIsNotGebuchtNextYear = [];
                    $arrBetriebsurlaubCurrentYear = [];
                    $arrBetriebsurlaubNextYear = [];
                    $arrUrlaubCurrentYear = [];
                    $arrUrlaubNextYear = [];
                    if(date('Y', strtotime($beantragenBeginn)) === $currentYear) {
                        $flagIsPassedBUcurrentYear = false;
                        foreach($arrBetriebsurlaub as $betriebsurlaub) {
                            if(date('Y', strtotime($betriebsurlaub->getBeginn())) === $currentYear) {
                                if(strtotime($betriebsurlaub->getBeginn()) > time()) {
                                    $arrBetriebsurlaubCurrentYear[] = $betriebsurlaub;
                                } else {
                                    $flagIsPassedBUcurrentYear = true;
                                }
                            }
                        }
                        if(count($arrBetriebsurlaubCurrentYear) == 0 && $flagIsPassedBUcurrentYear === false) {
                            $fehler = true;
                            $meldung .= 'Für ' . $currentYear . ' kann noch kein Urlaub beantragt werden, da noch kein Betriebsurlaub festgelegt wurde.<br>';
                        } else {
                            foreach($arrObjUrlaub as $objUrlaub) {
                                if(date('Y', strtotime($objUrlaub->getBeginn())) === $currentYear && $objUrlaub->getStatus() != 'A') {
                                    $arrUrlaubCurrentYear[] = $objUrlaub;
                                }
                            }
                            foreach($arrBetriebsurlaubCurrentYear as $betriebsurlaub) {
                                $flag = false;
                                foreach($arrUrlaubCurrentYear as $urlaub) {
                                    if($betriebsurlaub->getBeginn() >= $urlaub->getBeginn() && $betriebsurlaub->getEnde() <= $urlaub->getEnde()) {
                                        $flag = true;
                                    }
                                }
                                if($flag === false) {
                                    $arrBetriebsurlaubIsNotGebuchtCurrentYear[] = $betriebsurlaub;
                                }
                            }
                            if(count($arrBetriebsurlaubIsNotGebuchtCurrentYear) >= 1) {
                                $flag = false;
                                foreach($arrBetriebsurlaubIsNotGebuchtCurrentYear as $buNotGebucht) {
                                    if($buNotGebucht->getBeginn() >= $beantragenBeginn && $buNotGebucht->getEnde() <= $beantragenEnde) {
                                        $flag = true;
                                    }
                                }
                                if($flag === false) {
                                    $fehler = true;
                                    if(count($arrBetriebsurlaubIsNotGebuchtCurrentYear) > 1) {
                                        $meldung .= 'Bitte zuerst folgende Betriebsurlaube verplanen, bevor weiterer Urlaub in ' . $currentYear . ' beantragt wird:<br>';
                                    } else {
                                        $meldung .= 'Bitte zuerst folgenden Betriebsurlaub verplanen, bevor weiterer Urlaub in ' . $currentYear . ' beantragt wird:<br>';
                                    }
                                    foreach($arrBetriebsurlaubIsNotGebuchtCurrentYear as $buNotGebucht) {
                                        $meldung .= date('d.m.Y', strtotime($buNotGebucht->getBeginn())) . '&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;' . date('d.m.Y', strtotime($buNotGebucht->getEnde())) . '<br>';
                                    }
                                }
                            }
                        }
                    }
                    if(date('Y', strtotime($beantragenBeginn)) === $nextYear) {
                        foreach($arrBetriebsurlaub as $betriebsurlaub) {
                            if(date('Y', strtotime($betriebsurlaub->getBeginn())) === $nextYear) {
                                $arrBetriebsurlaubNextYear[] = $betriebsurlaub;
                            }
                        }
                        if(count($arrBetriebsurlaubNextYear) == 0) {
                            $fehler = true;
                            $meldung .= 'Für ' . $nextYear . ' kann noch kein Urlaub beantragt werden, da noch kein Betriebsurlaub festgelegt wurde.<br>';
                        } else {
                            foreach($arrObjUrlaub as $objUrlaub) {
                                if(date('Y', strtotime($objUrlaub->getBeginn())) === $nextYear && $objUrlaub->getStatus() != 'A') {
                                    $arrUrlaubNextYear[] = $objUrlaub;
                                }
                            }
                            foreach($arrBetriebsurlaubNextYear as $betriebsurlaub) {
                                $flag = false;
                                foreach($arrUrlaubNextYear as $urlaub) {
                                    if($betriebsurlaub->getBeginn() >= $urlaub->getBeginn() && $betriebsurlaub->getEnde() <= $urlaub->getEnde()) {
                                        $flag = true;
                                    }
                                }
                                if($flag === false) {
                                    $arrBetriebsurlaubIsNotGebuchtNextYear[] = $betriebsurlaub;
                                }
                            }
                            if(count($arrBetriebsurlaubIsNotGebuchtNextYear) >= 1) {
                                $flag = false;
                                foreach($arrBetriebsurlaubIsNotGebuchtNextYear as $buNotGebucht) {
                                    if($buNotGebucht->getBeginn() >= $beantragenBeginn && $buNotGebucht->getEnde() <= $beantragenEnde) {
                                        $flag = true;
                                    }
                                }
                                if($flag === false) {
                                    $fehler = true;
                                    if(count($arrBetriebsurlaubIsNotGebuchtNextYear) > 1) {
                                        $meldung .= 'Bitte zuerst folgende Betriebsurlaube verplanen, bevor weiterer Urlaub in ' . $nextYear . ' beantragt wird:<br>';
                                    } else {
                                        $meldung .= 'Bitte zuerst folgenden Betriebsurlaub verplanen, bevor weiterer Urlaub in ' . $nextYear . ' beantragt wird:<br>';
                                    }
                                    foreach($arrBetriebsurlaubIsNotGebuchtNextYear as $buNotGebucht) {
                                        $meldung .= date('d.m.Y', strtotime($buNotGebucht->getBeginn())) . '&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;' . date('d.m.Y', strtotime($buNotGebucht->getEnde())) . '<br>';
                                    }
                                }
                            }
                        }
                    }

                    // Überschneidungen mit anderen Urlauben prüfen:-------------------------------
                    $flag = false;
                    foreach($arrObjUrlaub as $urlaub) {
                        if($urlaub->getStatus() === 'G' || $urlaub->getStatus() === 'P') {
                            if(($beantragenBeginn >= $urlaub->getBeginn() && $beantragenBeginn <= $urlaub->getEnde()) ||
                              (($beantragenEnde >= $urlaub->getBeginn() && $beantragenEnde <= $urlaub->getEnde())) ||
                              (($urlaub->getBeginn() >= $beantragenBeginn && $urlaub->getBeginn() <= $beantragenEnde  )) ||
                              (($urlaub->getEnde() >= $beantragenBeginn && $urlaub->getEnde() <= $beantragenEnde ))) {
                                $fehler = true;
                                $flag = true;
                            }
                        }
                    }
                    if($flag === true) {
                        $meldung .= 'Zeitraum überschneidet sich mit genehmigtem oder anderem beantragten Urlaub!<br>';
                    }

                    // Anzahl Tage auf Plausibilität prüfen:---------------------------------------
                    $objBeantragenBeginn = new DateTime($beantragenBeginn);
                    $objBeantragenEnde = new DateTime($beantragenEnde);
                    $objmaxTageBerechnet = $objBeantragenBeginn->diff($objBeantragenEnde);
                    $maxTageBerechnet = $objmaxTageBerechnet->days + 1;
                    for($i=strtotime($beantragenBeginn);$i<=strtotime($beantragenEnde);$i=strtotime('+1 day',$i)) {
                        if (date('D', $i) === 'Sat' || date('D', $i) === 'Sun') {
                            $maxTageBerechnet -= 1;
                        }
                        if(in_array($i, $arrCurrentFeiertage, true) || in_array($i, $arrNextFeiertage, true)) {
                            $maxTageBerechnet -= 1;
                            if (date('D', $i) === 'Sat' || date('D', $i) === 'Sun') {
                                $maxTageBerechnet += 1;
                            }
                        }
                    }
                    if($beantragenTage > $maxTageBerechnet) {
                        $fehler = true;
                        if($maxTageBerechnet > 1) {
                            $meldung .= 'Abzüglich Wochenend- und Feiertagen liegen maximal ' . $maxTageBerechnet . ' Arbeitstage im Zeitraum!';
                        } else {
                            $meldung .= 'Abzüglich Wochenend- und Feiertagen liegt nur 1 Arbeitstag im Zeitraum!';
                        }
                    } else {
                        // Resturlaub berechnen:---------------------------------------------------
                        $restUrlaubCurrentYear = Resturlaub::berechneResturlaub($arrObjUrlaub, $urlaubGesamt)['restUrlaubCurrentYear'];
                        $restUrlaubNextYear = Resturlaub::berechneResturlaub($arrObjUrlaub, $urlaubGesamt)['restUrlaubNextYear'];
                        if(date('Y', strtotime($beantragenBeginn)) === $currentYear) {
                            if($beantragenTage > $restUrlaubCurrentYear) {
                                $fehler = true;
                                if($restUrlaubCurrentYear > 1) {
                                    $meldung .= 'Nur noch ' . $restUrlaubCurrentYear . ' Tage Resturlaub für ' . $currentYear;
                                } else {
                                    $meldung .= 'Nur noch 1 Tag Resturlaub für ' . $currentYear;
                                }
                            }
                        }
                        if(date('Y', strtotime($beantragenBeginn)) === $nextYear) {
                            if($beantragenTage > $restUrlaubNextYear) {
                                $fehler = true;
                                if($restUrlaubNextYear > 1) {
                                    $meldung .= 'Nur noch ' . $restUrlaubNextYear . ' Tage Resturlaub für ' . $nextYear;
                                } else {
                                    $meldung .= 'Nur noch 1 Tag Resturlaub für ' . $nextYear;
                                }
                            }
                        }
                    }
                }

                // Fehlermeldungen per Ajax an Modal-Box:------------------------------------------
                if($request->issetParameter('isUrlaubPlanenAjax')) {
                    if($fehler === false) {
                        echo 'ok';
                    } else {
                        echo '<br>' . $meldung;
                        exit;
                    }
                }

                // Antrag in DB eintragen wenn kein Fehler:----------------------------------------
                if($fehler === false) {
                    $objUrlaubBeantragen = new Urlaub(0, $pnr, $beantragenBeginn, $beantragenEnde, $beantragenTage, 3, 'P');
                    $urlaubDAO->create($objUrlaubBeantragen);
                    if($_SESSION['bstufe'] >= 3) {
                        $_SESSION['pendingCount'] = $urlaubDAO->readPendingCount();
                    }
                    $beantragenBeginnVorbelegen = '';
                    $beantragenEndeVorbelegen = '';
                    $beantragenTageVorbelegen = '';
                }

                if($request->issetParameter('isUrlaubPlanenAjax')) {
                    exit;
                }
            }
        }

        // Einzelne noch nicht genehmigte Anträge per Button löschbar:-----------------------------
        if($request->issetParameter('antragLoeschen')) {
            $urlaubDAO->deleteByUnr($request->getParameter('unr'));
            if($_SESSION['bstufe'] >= 3) {
                $_SESSION['pendingCount'] = $urlaubDAO->readPendingCount();
            }
        }

        // Urlaubsdaten aktualisieren:-------------------------------------------------------------
        $arrObjUrlaub = $urlaubDAO->readByPnr($pnr);
        $auCurrentYear = $urlaubDAO->readAuByPnr($pnr);
        if(empty($auCurrentYear)) {
            $auCurrentYear = 0;
        }
        $arrUrlaubsStatusSprache = ['P' => 'beantragt', 'G' => 'genehmigt', 'A' => 'abgelehnt'];
        $arrUrlaubsStatusCssClass = ['P' => 'status-pending', 'G' => 'status-approved', 'A' => 'status-denied'];
        $restUrlaubCurrentYear = $urlaubGesamt;
        $restUrlaubNextYear = $urlaubGesamt;
        // Resturlaub aktualisieren:
        $restUrlaubCurrentYear = Resturlaub::berechneResturlaub($arrObjUrlaub, $urlaubGesamt)['restUrlaubCurrentYear'];
        $restUrlaubNextYear = Resturlaub::berechneResturlaub($arrObjUrlaub, $urlaubGesamt)['restUrlaubNextYear'];

        // Formulareingabe Passwort ändern auswerten:
        if($request->issetParameter('isPasswortAendernAjax')) {
            $fehler = false;
            // Prüfen ob alle Felder ausgefüllt:---------------------------------------------------
            if(empty($request->getParameter('pw_aendern_alt')) || empty($request->getParameter('pw_aendern_neu')) ||
                (empty($request->getParameter('pw_aendern_neu_wh')))) {
                $fehler = true;
                $meldung .= 'Bitte alle Felder ausfüllen!<br>';
            }
            if(!empty($request->getParameter('pw_aendern_alt'))) {
                // Prüfen ob altes Passwort richtig:
                $pwAendernAlt = $request->getParameter('pw_aendern_alt');
                $objAltesPW = new Login($username, $pwAendernAlt);
                $loginDAO = new LoginDAO();
                if($loginDAO->checkLogin($objAltesPW) === false) {
                    $fehler = true;
                    $meldung .= 'Altes Passwort ist falsch!<br>';
                }
            }
            if(!empty($request->getParameter('pw_aendern_neu'))) {
                // Prüfen ob neues Passwort stark genug:
                $pwAendernNeu = $request->getParameter('pw_aendern_neu');
                $sicherheitsIndex = strlen($pwAendernNeu);
                if (preg_match("/[a-z]/", $pwAendernNeu)) {
                    $sicherheitsIndex = $sicherheitsIndex + 3;
                }
                if (preg_match("/[A-Z]/", $pwAendernNeu)) {
                    $sicherheitsIndex = $sicherheitsIndex + 3;
                }
                if (preg_match("/[0-9]/", $pwAendernNeu)) {
                    $sicherheitsIndex = $sicherheitsIndex + 4;
                }
                if (preg_match("/[-,.;:_]/", $pwAendernNeu)) {
                    $sicherheitsIndex = $sicherheitsIndex + 5;
                }
                if($sicherheitsIndex < 10) {
                    $fehler = true;
                    $meldung .= 'Das neue Passwort ist nicht sicher genug!<br>';
                } else {
                    // Sicherstellen, dass neues Passwort nicht identisch mit Standard-Passwort:
                    if($pwAendernNeu === DEFAULT_PWD) {
                        $fehler = true;
                        $meldung .= 'Das neue Passwort darf nicht identisch mit dem Standard-Passwort sein!<br>';
                    }
                }
                // Prüfen auf gültige Zeichen:
                $arrPwAendernNeu = str_split($pwAendernNeu);
                foreach($arrPwAendernNeu as $c) {
                    if(!(preg_match("/[a-z]/", $c) || preg_match("/[A-Z]/", $c) ||
                        preg_match("/[0-9]/", $c) || preg_match("/[-,.;:_]/", $c))) {
                        $fehler = true;
                        $meldung .= 'Das neue Passwort darf nur aus Groß- und Kleinbuchstaben,
                                        Ziffern sowie den Zeichen&nbsp;&nbsp;&nbsp;-  ,  .  ;  :  _&nbsp;&nbsp;&nbsp;bestehen!';
                        break;
                    }
                }
                // Prüfen auf maximale Länge:
                if(strlen($pwAendernNeu) > 50) {
                    $fehler = true;
                    $meldung .= 'Das Neue Passwort darf maximal 50 Zeichen lang sein!<br>';
                }
            }
            if(!empty($request->getParameter('pw_aendern_alt')) && !empty($request->getParameter('pw_aendern_neu'))) {
                // Prüfen ob altes und neues Passwort identisch:
                if($pwAendernAlt === $pwAendernNeu) {
                    $fehler = true;
                    $meldung .= 'Das neue und alte Passwort dürfen nicht identisch sein!<br>';
                }
            }
            if(!empty($request->getParameter('pw_aendern_neu')) && !empty($request->getParameter('pw_aendern_neu_wh'))) {
                // Prüfen ob neues Passwort identisch mit Wiederholung:
                $pwAendernNeuWh = $request->getParameter('pw_aendern_neu_wh');
                if($pwAendernNeu !== $pwAendernNeuWh) {
                    $fehler = true;
                    $meldung .= 'Neues Passwort und Wiederholung sind nicht identisch!<br>';
                }
            }
            // Wenn Fehler, Meldungen per Ajax an Modal-Box:
            if($fehler === true) {
                echo '<br>' . $meldung;
                exit();
            }
            // Wenn kein Fehler, neues Passwort (gehashed, geslzen und gepfeffert) in DB eintragen:
            if($fehler === false) {
                $objNeuesPW = new Login($username, $pwAendernNeu);
                $loginDAO->setNewPassword($objNeuesPW);
                echo 'ok';
                session_destroy();
                exit;
            }
        }

        // Passwort ändern erzwingen, falls noch Standart Passwort:
        if(isset($_SESSION['force_pw_change'])) {
            if($_SESSION['force_pw_change'] === true) {
                $meldungPw = 'Dies ist Ihr erster Login oder Ihr Passwort wurde zurück gesetzt.<br>
                               Bitte Vergeben Sie jetzt ein neues Passwort.';
                $template = new HtmlTemplateView('meinUrlaubMandatoryPwChange');
                $template->assign('vorname', $vorname);
                $template->assign('status', $status);
                $template->assign('currentYear', $currentYear);
                $template->assign('nextYear', $nextYear);
                $template->assign('arrCurrentFeiertage', $arrCurrentFeiertage);
                $template->assign('arrNextFeiertage', $arrNextFeiertage);
                $template->assign('arrBetriebsurlaub', $arrBetriebsurlaub);
                $template->assign('arrWochentage', $arrWochentage);
                $template->assign('meldung', $meldung);
                $template->assign('meldungPw', $meldungPw);
                $template->assign('nav', $nav);
                $template->render($request, $response);
            }
        }

        // Variablen übergeben und Seite rendern:--------------------------------------------------
        if(!isset($_SESSION['force_pw_change'])) {
            $template = new HtmlTemplateView($view);
            $template->assign('vorname', $vorname);
            $template->assign('status', $status);
            $template->assign('currentYear', $currentYear);
            $template->assign('nextYear', $nextYear);
            $template->assign('arrObjUrlaub', $arrObjUrlaub);
            $template->assign('auCurrentYear', $auCurrentYear);
            $template->assign('restUrlaubCurrentYear', $restUrlaubCurrentYear);
            $template->assign('restUrlaubNextYear', $restUrlaubNextYear);
            $template->assign('arrUrlaubsStatusSprache', $arrUrlaubsStatusSprache);
            $template->assign('arrUrlaubsStatusCssClass', $arrUrlaubsStatusCssClass);
            $template->assign('arrCurrentFeiertage', $arrCurrentFeiertage);
            $template->assign('arrNextFeiertage', $arrNextFeiertage);
            $template->assign('arrBetriebsurlaub', $arrBetriebsurlaub);
            $template->assign('arrWochentage', $arrWochentage);
            $template->assign('beantragenBeginnVorbelegen', $beantragenBeginnVorbelegen);
            $template->assign('beantragenEndeVorbelegen', $beantragenEndeVorbelegen);
            $template->assign('beantragenTageVorbelegen', $beantragenTageVorbelegen);
            $template->assign('meldung', $meldung);
            $template->assign('nav', $nav);
            $template->render($request, $response);
        }
    }
}

?>