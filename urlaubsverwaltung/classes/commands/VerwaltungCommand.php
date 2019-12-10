<?php
namespace classes\commands;

use classes\request\Request;
use classes\response\Response;
use classes\template\HtmlTemplateView;
use classes\model\Resturlaub;
use classes\model\Login;
use classes\mapper\MitarbeiterDAO;
use classes\mapper\AbteilungDAO;
use classes\mapper\FunktionDAO;
use classes\mapper\LoginDAO;
use classes\mapper\UrlaubDAO;
use classes\model\Mitarbeiter;

class VerwaltungCommand implements Command {
    public function execute(Request $request, Response $response) {
        $status = $_SESSION['status'];
        $meldung = '';
        $nav = $_SESSION['nav'];
        $view = 'verwaltung';

        $mitarbeiterDAO = new MitarbeiterDAO();
        $abteilungDAO = new AbteilungDAO();
        $funktionDAO = new FunktionDAO();
        $urlaubDAO = new UrlaubDAO();

        // Modalbox Mitarbeiter-ändern speichern:
        if($request->issetParameter('isMAAendernAjax')) {
            $fehler = false;
            if(empty($request->getParameter('anrede'))) {
                $fehler = true;
                $meldung .= 'Bitte eine Anrede wählen!<br>';
            }
            if(empty($request->getParameter('titel_maa'))) {
                $request->setParameter('titel_maa', '');
            }
            if(empty($request->getParameter('vorname'))) {
                $fehler = true;
                $meldung .= 'Bitte einen Vornamen eingeben!<br>';
            }
            if(empty($request->getParameter('nachname'))) {
                $fehler = true;
                $meldung .= 'Bitte einen Nachnamen eingeben!<br>';
            }
            if(empty($request->getParameter('abteilung'))) {
                $fehler = true;
                $meldung .= 'Bitte eine Abteilung wählen!<br>';
            }
            if(empty($request->getParameter('funktion'))) {
                $fehler = true;
                $meldung .= 'Bitte eine Funktion wählen!<br>';
            }
            if(empty($request->getParameter('bstufe'))) {
                $bstufeMAA = 1;
            } else {
                if(($request->getParameter('bstufe')<1) || ($request->getParameter('bstufe')>3)) {
                    $fehler = true;
                    $meldung .= 'Ungültige Berechtigungsstufe!<br>';
                } else {
                    if(($_SESSION['bstufe']<3) && ($request->getParameter('bstufe')>1)) {
                        $fehler = true;
                        $meldung .= 'Höhere Berechtigungsstufen dürfen nur von Personen mit Stufe 3 vergeben werden!<br>';
                    }
                }
            }
            if(empty($request->getParameter('durchwahl'))) {
                $request->setParameter('durchwahl', '');
            }
            if(empty($request->getParameter('email'))) {
                $request->setParameter('email', '');
            }
            if(empty($request->getParameter('aucurrent'))) {
                $request->setParameter('aucurrent', 0);
            } else {
                // Prüfen ob Urlaubstage Numeric und >= 0
                if(is_numeric($request->getParameter('aucurrent'))) {
                    if($request->getParameter('aucurrent') < 0) {
                        $fehler = true;
                        $meldung .= 'Die Anzahl der AU-Tage darf nicht negativ sein!<br>';
                    }
                } else {
                    $fehler = true;
                    $meldung .= 'Bitte eine Zahl für die AU-Tage eingeben!<br>';
                }
            }
            if(empty($request->getParameter('uges'))) {
                $fehler = true;
                $meldung .= 'Bitte Anzahl Tage Gesamturlaub eingeben!<br>';
            } else {
                // Prüfen ob Urlaubstage Numeric und >= 0
                if(is_numeric($request->getParameter('uges'))) {
                    if($request->getParameter('uges') < 0) {
                        $fehler = true;
                        $meldung .= 'Die Anzahl der Urlaubstage darf nicht negativ sein!<br>';
                    }
                } else {
                    $fehler = true;
                    $meldung .= 'Bitte eine Zahl für die Urlaubstage eingeben!<br>';
                }
            }

            // Wenn Fehler, Meldungen per Ajax an Modal-Box:
            if($fehler === true) {
                echo '<br>' . $meldung;
                exit();
            }

            // Wenn kein Fehler, Änderungen in DB eintrage:
            if($fehler === false) {
                $objCurrentMitarbeiter = $mitarbeiterDAO->readBypnr($request->getParameter('verw_ma_aendern_pnr'));
                $login = new Login($objCurrentMitarbeiter->getUsername(), DEFAULT_PWD);
                $loginDAO = new LoginDAO();
                $objCurrentMitarbeiter->setAnrede($request->getParameter('anrede'));
                $objCurrentMitarbeiter->setTitel($request->getParameter('titel'));
                $objCurrentMitarbeiter->setVorname($request->getParameter('vorname'));
                $objCurrentMitarbeiter->setNachname($request->getParameter('nachname'));
                $objCurrentMitarbeiter->setAbtnr($request->getParameter('abteilung'));
                $objCurrentMitarbeiter->setFunktion($request->getParameter('funktion'));
                $objCurrentMitarbeiter->setDurchwahl($request->getParameter('durchwahl'));
                $objCurrentMitarbeiter->setEmail($request->getParameter('email'));
                $objCurrentMitarbeiter->setUges($request->getParameter('uges'));
                if($_SESSION['bstufe'] >= 3) {
                    $objCurrentMitarbeiter->setBstufe($request->getParameter('bstufe'));
                }
                $mitarbeiterDAO->update($objCurrentMitarbeiter);
                if($request->getParameter(('aucurrent')) >= 0) {
                    $urlaubDAO->writeOrUpdateAuByPnr($request->getParameter('verw_ma_aendern_pnr'), $request->getParameter(('aucurrent')));
                }
                if($request->issetParameter('st_pwd') && !$loginDAO->checkLogin($login)) {
                    if($request->getParameter('st_pwd') === 'on') {
                        $loginDAO->setNewPassword($login);
                    }
                }
                echo 'ok';
                exit;
            }
        }

        //Modalbox Mitarbeiter anlegen auswerten:
        if($request->issetParameter('isMAAAjax')) {
            $fehler = false;
            if(empty($request->getParameter('anrede_maa'))) {
                $fehler = true;
                $meldung .= 'Bitte eine Anrede wählen!<br>';
            } else {
                $anredeMAA = $request->getParameter('anrede_maa');
            }
            if(!empty($request->getParameter('titel_maa'))) {
                $titelMAA = $request->getParameter('titel_maa');
            } else {
                $titelMAA = '';
            }
            if(empty($request->getParameter('vorname_maa'))) {
                $fehler = true;
                $meldung .= 'Bitte einen Vornamen eingeben!<br>';
            } else {
                $vornameMAA = $request->getParameter('vorname_maa');
            }
            if(empty($request->getParameter('nachname_maa'))) {
                $fehler = true;
                $meldung .= 'Bitte einen Nachnamen eingeben!<br>';
            } else {
                $nachnameMAA = $request->getParameter('nachname_maa');
            }
            if(empty($request->getParameter('abteilung_maa'))) {
                $fehler = true;
                $meldung .= 'Bitte eine Abteilung wählen!<br>';
            } else {
                $abteilungMAA = $request->getParameter('abteilung_maa');
            }
            if(empty($request->getParameter('funktion_maa'))) {
                $fehler = true;
                $meldung .= 'Bitte eine Funktion wählen!<br>';
            } else {
                $funktionMAA = $request->getParameter('funktion_maa');
            }
            if(empty($request->getParameter('bstufe_maa'))) {
                $bstufeMAA = 1;
            } else {
                if(($request->getParameter('bstufe_maa')<1) || ($request->getParameter('bstufe_maa')>3)) {
                    $bstufeMAA = 1;
                } else {
                    if(($_SESSION['bstufe']<3) && ($request->getParameter('bstufe_maa')>1)) {
                        $fehler = true;
                        $meldung .= 'Höhere Berechtigungsstufen dürfen nur von Personen mit Stufe 3 vergeben werden!<br>';
                    } else {
                        $bstufeMAA = $request->getParameter('bstufe_maa');
                    }
                }
            }
            if(!empty($request->getParameter('durchwahl_maa'))) {
                $durchwahlMAA = $request->getParameter('durchwahl_maa');
            } else {
                $durchwahlMAA = '';
            }
            if(!empty($request->getParameter('email_maa'))) {
                $emailMAA = $request->getParameter('email_maa');
            } else {
                $emailMAA = '';
            }
            if(empty($request->getParameter('uges_maa'))) {
                $fehler = true;
                $meldung .= 'Bitte Anzahl Tage Gesamturlaub eingeben!<br>';
            } else {
                $ugesMAA = $request->getParameter('uges_maa');
                // Prüfen ob Urlaubstage Numeric und >= 0
                if(is_numeric($ugesMAA)) {
                    if($ugesMAA < 0) {
                        $fehler = true;
                        $meldung .= 'Die Anzahl der Urlaubstage darf nicht negativ sein!<br>';
                    }
                } else {
                    $fehler = true;
                    $meldung .= 'Bitte eine Zahl für die Urlaubstage eingeben!<br>';
                }
            }
            if(empty($request->getParameter('nutzername_maa'))) {
                $fehler = true;
                $meldung .= 'Bitte einen Nutzernamen eingeben!<br>';
            } else {
                $nutzernameMAA = $request->getParameter('nutzername_maa');
                // Prüfen ob Nutzername noch nicht vorhanden:
                    if($mitarbeiterDAO->readByUsername($nutzernameMAA)) {
                        $fehler = true;
                        $meldung .= 'Nutzername existiert bereits. Bitte einen anderen wählen!<br>';
                    }
            }

            // Wenn Fehler, Meldungen per Ajax an Modal-Box:
            if($fehler === true) {
                echo '<br>' . $meldung;
                exit();
            }

            // Wenn kein Fehler, neuen Mitarbeiter in DB eintragen:
            if($fehler === false) {
                $objMitarbeiterAnlegen = new Mitarbeiter($vornameMAA, $nachnameMAA, 0, $abteilungMAA, "dummy", $funktionMAA, $bstufeMAA, $ugesMAA, $durchwahlMAA, $emailMAA, $anredeMAA, $titelMAA, NULL, $nutzernameMAA);
                $mitarbeiterDAO->create($objMitarbeiterAnlegen);
                echo 'ok';
                exit;
            }
        }

        $arrObjMitarbeiter = $mitarbeiterDAO->readAll();
        $arrObjAbteilung = $abteilungDAO->readAll();
        $arrObjFunktion = $funktionDAO->readAll();

        // Resturlaube:
        $arrRestUrlaubCurrentYear =[];
        $arrRestUrlaubNextYear =[];
        foreach($arrObjMitarbeiter as $objMitarbeiter) {
            $arrObjUrlaub = $urlaubDAO->readByPnr($objMitarbeiter->getPnr());
            $arrRestUrlaubCurrentYear[$objMitarbeiter->getPnr()] = Resturlaub::berechneResturlaub($arrObjUrlaub, $objMitarbeiter->getUges())['restUrlaubCurrentYear'];
            $arrRestUrlaubNextYear[$objMitarbeiter->getPnr()] = Resturlaub::berechneResturlaub($arrObjUrlaub, $objMitarbeiter->getUges())['restUrlaubNextYear'];
        }

        // Modalbox Mitarbeiter-ändern vorbelegen:
        if($request->issetParameter('ajaxPnr')) {
            $objCurrentMitarbeiter = $mitarbeiterDAO->readBypnr($request->getParameter('ajaxPnr'));
            $login = new Login($objCurrentMitarbeiter->getUsername(), DEFAULT_PWD);
            $loginDAO = new LoginDAO();
            $arrWochentage = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'];
            $arrUrlaubsStatusSprache = ['P' => 'beantragt', 'G' => 'genehmigt', 'A' => 'abgelehnt'];
            $arrUrlaubsStatusCssClass = ['P' => 'status-pending', 'G' => 'status-approved', 'A' => 'status-denied'];

            $arrObjUrlaub = $urlaubDAO->readByPnr($request->getParameter('ajaxPnr'));
            $urlaubString = '{';
            foreach($arrObjUrlaub as $key => $urlaub) {
                $urlaubString .= '"' . $key . '": "<tr><td>' . $arrWochentage[date('w', strtotime($urlaub->getBeginn()))] .
                                    ', </td><td>' . date('d.m.Y', strtotime($urlaub->getBeginn())) .
                                    '</td><td>&nbsp;&nbsp;-&nbsp;</td><td>' .
                                    $arrWochentage[date('w', strtotime($urlaub->getEnde()))] .
                                    ', </td><td>' . date('d.m.Y', strtotime($urlaub->getEnde())) .
                                    ':&nbsp;&nbsp;&nbsp;</td><td class=\"' . $arrUrlaubsStatusCssClass[$urlaub->getStatus()] . '\">' .
                                    $arrUrlaubsStatusSprache[$urlaub->getStatus()] .
                                    '&nbsp;&nbsp;&nbsp;</td><td>(' . $urlaub->getTage();
                if($urlaub->getTage() <= 1) {
                    $urlaubString .= ' Tag)</td></tr>"';
                } else {
                    $urlaubString .= ' Tage)</td></tr>"';
                }
                if($key < (count($arrObjUrlaub)-1)) {
                    $urlaubString .= ', ';
                }
            }
                $urlaubString .= '}';
            $ajaxJson = '{
                           "anrede": "' . $objCurrentMitarbeiter->getAnrede() . '",
                           "titel": "' . $objCurrentMitarbeiter->getTitel() . '",
                           "vorname": "' . $objCurrentMitarbeiter->getVorname() . '",
                           "nachname": "' . $objCurrentMitarbeiter->getNachname() . '",
                           "abteilung": "' . $objCurrentMitarbeiter->getAbtnr() . '",
                           "durchwahl": "' . $objCurrentMitarbeiter->getDurchwahl() . '",
                           "email": "' . $objCurrentMitarbeiter->getEmail() . '",
                           "nutzername": "' . $objCurrentMitarbeiter->getUsername() . '",
                           "pwdIsStandard": "' . $loginDAO->checkLogin($login) . '",
                           "funktion": "' . $objCurrentMitarbeiter->getFunktion() . '",
                           "uges": "' . $objCurrentMitarbeiter->getUges() . '",
                           "bstufe": "' . $objCurrentMitarbeiter->getBstufe() . '",
                           "current_bstufe": "' . $_SESSION['bstufe'] . '",
                           "urlaube": ' . $urlaubString . ',
                           "urestcurrent": "' . $arrRestUrlaubCurrentYear[$request->getParameter('ajaxPnr')] . '",
                           "urestnext": "' . $arrRestUrlaubNextYear[$request->getParameter('ajaxPnr')] . '",
                           "aucurrent": "' . $urlaubDAO->readAuByPnr($request->getParameter('ajaxPnr')) . '"
                         }';
            echo $ajaxJson;
            exit;
        }

        $template = new HtmlTemplateView($view);
        $template->assign('status', $status);
        // $template->assign('meldung', $meldung);
        $template->assign('arrObjMitarbeiter', $arrObjMitarbeiter);
        $template->assign('arrObjAbteilung', $arrObjAbteilung);
        $template->assign('arrObjFunktion', $arrObjFunktion);
        $template->assign('arrRestUrlaubCurrentYear', $arrRestUrlaubCurrentYear);
        $template->assign('arrRestUrlaubNextYear', $arrRestUrlaubNextYear);
        $template->assign('nav', $nav);
        $template->render($request, $response);
    }
}

?>