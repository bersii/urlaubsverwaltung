<?php
namespace classes\commands;

use classes\mapper\AbteilungDAO;
use classes\mapper\UrlaubsartDAO;
use classes\model\Mitarbeiter;
use classes\request\Request;
use classes\response\Response;
use classes\template\HtmlTemplateView;
use classes\model\Feiertage;
use classes\mapper\UrlaubDAO;
use classes\mapper\MitarbeiterDAO;

class UrlaubGenehmigenCommand implements Command {
    public function execute(Request $request, Response $response) {
        // $vorname = $_SESSION['vorname'];
        // // $username = $_SESSION['username'];
        // $pnr = $_SESSION['pnr'];
        // $urlaubGesamt = $_SESSION['uges'];
         $status = $_SESSION['status'];
        $meldung = '';
        $nav = $_SESSION['nav'];
        $view = 'urlaubGenehmigen';
        $urlaubDAO = new UrlaubDAO();
        $abteilungDAO = new AbteilungDAO();

//        Feiertage per Klasse berechnen und übergeben:
        $objFeiertage = new Feiertage();
        $arrCurrentFeiertage = $objFeiertage->getArrAlleCurrentFeiertage();
        $arrNextFeiertage = $objFeiertage->getArrAlleNextFeiertage();
        $arrWochentage = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'];

        // Betriebsurlaub aus DB holen und übergeben:
        $arrBetriebsurlaub = $urlaubDAO->readByPnr(0);

        // Urlaubsdaten aus DB holen und übergeben:
        // $urlaubDAO = new UrlaubDAO();
        // $arrObjUrlaub = $urlaubDAO->readByPnr($pnr);
        // $arrUrlaubsStatusSprache = ['P' => 'beantragt', 'G' => 'genehmigt', 'A' => 'abgelehnt'];
        // $arrUrlaubsStatusCssClass = ['P' => 'status-pending', 'G' => 'status-approved', 'A' => 'status-denied'];
        // $restUrlaub = $urlaubGesamt;
        // // Resturlaub berechnen:
        // foreach($arrObjUrlaub as $urlaub) {
        //     if($urlaub->getStatus() === 'G' || $urlaub->getStatus() === 'P') {
        //         $restUrlaub -= $urlaub->getTage();
        //     }
        // }

        // TODO: SOMETHING SOMETHING BUTTON MAGIC
        if($request->issetParameter('btnDeny')) {
            $urlaubDAO->ablehnen($request->getParameter('unr'));
        }

        if($request->issetParameter('btnApprove')) {
            $urlaubDAO->genehmigen($request->getParameter('unr'));
        }

        if($_SESSION['bstufe'] >= 3) {
            $_SESSION['pendingCount'] = $urlaubDAO->readPendingCount();
        }

        // Modalbox Urlaubstage-ändern speichern:
        if($request->issetParameter('tage_aendern_submit')) {
            $objCurrentUrlaub = $urlaubDAO->readByUnr($request->getParameter('urlaub_genehm_unr'));
            $objCurrentUrlaub->setTage($request->getParameter('tage'));
            $urlaubDAO->update($objCurrentUrlaub);
        }

        // Alle noch nicht genehmigten Urlaube aus der Datenbank auslesen
        $arrVorname = [];
        $arrNachname = [];
        $arrAbteilungen = [];
        $arrObjUrlaub = $urlaubDAO->readPending();
        foreach($arrObjUrlaub as $objUrlaub) {
            $pnr = $objUrlaub->getPnr();
            $mitarbeiterDAO = new MitarbeiterDAO();
            $mitarbeiter = $mitarbeiterDAO->readBypnr($pnr);
            $arrVorname[] = $mitarbeiter->getVorname();
            $arrNachname[] = $mitarbeiter->getNachname();
            $abteilung = $abteilungDAO->readByAbtnr($mitarbeiter->getAbtnr());
            $arrAbteilungen[] = $abteilung->getName();
        }



        // Modalbox Urlaub editieren vorbelegen:
        if($request->issetParameter('ajaxUnr')) {
            $objCurrentUrlaub = $urlaubDAO->readByUnr($request->getParameter('ajaxUnr'));
            $objCurrentMitarbeiter = $mitarbeiterDAO->readBypnr($objCurrentUrlaub->getPnr());
            $ajaxJson = '{
                           "name": "' . $objCurrentMitarbeiter->getVorname() . " " . $objCurrentMitarbeiter->getNachname() .'",
                           "beginn": "' . $objCurrentUrlaub->getBeginn() . '",
                           "ende": "' . $objCurrentUrlaub->getEnde() . '",
                           "tage": "' . $objCurrentUrlaub->getTage() . '"
                         }';
            echo $ajaxJson;
            exit;
        }

        $template = new HtmlTemplateView($view);
        $template->assign('arrVorname', $arrVorname);
        $template->assign('arrNachname', $arrNachname);
        $template->assign('status', $status);
        $template->assign('arrObjUrlaub', $arrObjUrlaub);
        $template->assign('arrAbteilungen', $arrAbteilungen);
        // $template->assign('restUrlaub', $restUrlaub);
        // $template->assign('arrUrlaubsStatusSprache', $arrUrlaubsStatusSprache);
        // $template->assign('arrUrlaubsStatusCssClass', $arrUrlaubsStatusCssClass);
        $template->assign('arrCurrentFeiertage', $arrCurrentFeiertage);
        $template->assign('arrNextFeiertage', $arrNextFeiertage);
        $template->assign('arrBetriebsurlaub', $arrBetriebsurlaub);
        $template->assign('arrWochentage', $arrWochentage);
        // $template->assign('meldung', $meldung);
        $template->assign('nav', $nav);
        $template->render($request, $response);
    }
}

?>