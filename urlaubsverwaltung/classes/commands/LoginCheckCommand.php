<?php
namespace classes\commands;

use classes\request\Request;
use classes\response\Response;
use classes\model\Login;
use classes\mapper\LoginDAO;
use classes\mapper\MitarbeiterDAO;
use classes\mapper\UrlaubDAO;
use classes\template\HtmlTemplateView;

    class LoginCheckCommand implements Command {

    public function execute(Request $request, Response $response){

            $username = $request->getParameter('username');
            $password = $request->getParameter('password');
            $fehler = false;
            $meldung = '';
            $view = '';

            // Login-Objekt erstellen und mit Login-Eingabe füllen:
            $login = new Login($username, $password);

            // LogginDAO-Instanz erzeugen:
            $loginCheck = new LoginDAO();

            // Login-Eingabe checken:
            if (empty($username)) {
                $fehler = true;
                $meldung .= 'Bitte geben Sie einen <span class="meldung1">Nutzernamen</span> an!<br>';
            }
            if ($username === 'betriebsurlaub') {
                $fehler = true;
                $meldung .= 'Ungültiger Nutzername!<br>';
            }
            if (empty($password)) {
                $fehler = true;
                $meldung .= 'Bitte geben Sie ein <span class="meldung1">Passwort</span> an!<br>';
            }
            if($fehler === false) {
                if($loginCheck->checkLogin($login) === true) {
                    $mitarbeiterDAO = new MitarbeiterDAO();
                    $objMitarbeiter = $mitarbeiterDAO->readByUsername($username);
                    $_SESSION['username'] = $username;
                    $_SESSION['pnr']      = $objMitarbeiter->getPnr();
                    $_SESSION['vorname']  = $objMitarbeiter->getVorname();
                    $_SESSION['bstufe']   = $objMitarbeiter->getBstufe();
                    $_SESSION['uges']     = $objMitarbeiter->getUges();
                    $_SESSION['letzte_aktivitaet'] = time();
                    $_SESSION['status']   = 'Angemeldet: ' .
                                             $objMitarbeiter->getTitel() . ' ' .
                                             $objMitarbeiter->getVorname() . ' ' .
                                             $objMitarbeiter->getNachname() . '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;' .
                                             'Letzter Login: ' .
                                             (($objMitarbeiter->getLetzterLogin()===NULL)?'--':date("d.m.Y \u\m H:i \U\h\\r", strtotime($objMitarbeiter->getLetzterLogin())));
                    if($_SESSION['bstufe'] >= 3) {
                        $urlaubDAO = new UrlaubDAO();
                        $_SESSION['pendingCount'] = $urlaubDAO->readPendingCount();
                    }
                    if($password === DEFAULT_PWD) {
                        $_SESSION['force_pw_change'] = true;
                        $_SESSION['bstufe'] = 1;
                    }

                    // Letzte Login-Zeit setzen:
                    $mitarbeiterDAO->setLastLoginTime($username);

                    // Dynamische Navigation erzeugen:
                    $arrCommandSets = [ARR_BSTUFE1_COMMANDS, ARR_BSTUFE2_COMMANDS, ARR_BSTUFE3_COMMANDS];
                    $_SESSION['nav'] = $arrCommandSets[$_SESSION['bstufe']-1];

                    // Startseite für Alle:
                    header('Location: index.php?cmd=' . START_LOGGED_IN);
                    exit;
                }
                else{
                    $meldung .= 'Falsches Passwort und/oder Nutzername';
                }
            }

            // Bei Fehlern wieder zur (Login-Seite mit Fehlermeldungen):
            $view = 'login';
            $template = new HtmlTemplateView($view);
            $template->assign('username', $username);
            $template->assign('meldung', $meldung);
            $template->render( $request, $response);
        }
    }

?>