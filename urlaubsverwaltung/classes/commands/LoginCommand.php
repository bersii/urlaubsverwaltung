<?php
namespace classes\commands;

use classes\request\Request;
use classes\response\Response;
use classes\template\HtmlTemplateView;

class LoginCommand implements Command {
    public function execute(Request $request, Response $response) {
        $view = "login";
        $meldung  = ($request->getParameter('meldung_timeout') === NULL) ? '' : $request->getParameter('meldung_timeout');
        $meldung .= ($request->getParameter('meldung_bstufe_ungueltig') === NULL) ? '' : $request->getParameter('meldung_bstufe_ungueltig');
        $template = new HtmlTemplateView($view);
        $template->assign('meldung', $meldung);
        $template->render($request, $response);
    }
}

?>