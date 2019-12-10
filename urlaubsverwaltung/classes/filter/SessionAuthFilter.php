<?php
namespace classes\filter;

use classes\request\Request;
use classes\response\Response;

class SessionAuthFilter implements Filter{
    public function execute(Request $request, Response $response){
        // Session grundsätzlich starten:
        session_start();

        // Prüfen und Resetten der Inaktivitat:
        if (isset($_SESSION['letzte_aktivitaet']) && (time() - $_SESSION['letzte_aktivitaet'] > TIMEOUT_IN_SEKUNDEN)) {
            session_unset();
            $request->setParameter('meldung_timeout', 'Automatisch ausgeloggt<br>(länger als ' . TIMEOUT_IN_MINUTEN . ' Minuten inaktiv)');
        }
        if (isset($_SESSION['letzte_aktivitaet'])) {
            $_SESSION['letzte_aktivitaet'] = time();
        }

        // Sicherstellen, dass aktuelle Seite nicht LoginCheck ist:
        if($request->getParameter('cmd') != "LoginCheck"){
            // Prüfen ob ein User angemeldet ist. Wenn nicht, Login aufrufen:
            if(isset($_SESSION['username']) && !empty($_SESSION['username'])){
                // Falls Start- oder Login-Seite aufgerufen werden obwohl eingeloggt (Browser-History, Fehlbedienung, Angriff, etc.):
                if($request->getParameter('cmd') === "Login" || $request->getParameter('cmd') === NULL) {
                    $request->setParameter('cmd', START_LOGGED_IN);
                }
                // Prüfen ob Berechtigungsstufe gültig bzw. ausreichend:
                $arrCommandSets = [ARR_BSTUFE1_COMMANDS, ARR_BSTUFE2_COMMANDS, ARR_BSTUFE3_COMMANDS];
                if($_SESSION['bstufe'] === 1 || $_SESSION['bstufe'] === 2 || $_SESSION['bstufe'] === 3) {
                    if(!in_array($request->getParameter('cmd'), $arrCommandSets[$_SESSION['bstufe']-1], true)) {
                        // Zurück zur Startseite-wenn-eigeloggt falls Berechtigungsstufe zu niedrig:
                        $request->setParameter('cmd', START_LOGGED_IN);
                    }
                } else {
                    // Meldung falls in DB ungültiger Wert für Berechtigungsstufe:
                    $request->setParameter('meldung_bstufe_ungueltig', 'Ungueltige Berechtigungsstufe in Datenbank!<br>(Bitte einen Admin informieren)');
                    $request->setParameter('cmd', 'Login');
                    session_destroy();
                    }
            } else {
                // Login aufruffen wenn nicht eingeloggt:
                $request->setParameter('cmd', 'Login');
                session_destroy();
            }
        } else {
                // Falls LoginCheck-Seite aufgerufen werden obwohl eingeloggt (Browser-History, Fehlbedienung, Angriff, etc.):
                if(isset($_SESSION['username']) && !empty($_SESSION['username'])) {
                    $request->setParameter('cmd', START_LOGGED_IN);
                }
        }

        // Autologout für Ajax-Funktionalität handeln:
        if($request->issetParameter('isAjax')) {
            if($request->getParameter('isAjax') === 'true' && $request->getParameter('cmd') === 'Login') {
                echo "fehler";
                exit;
            }
        }
    }
}
?>