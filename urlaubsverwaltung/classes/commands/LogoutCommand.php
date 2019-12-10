<?php
namespace classes\commands;

use classes\request\Request;
use classes\response\Response;
use classes\template\HtmlTemplateView;

class LogoutCommand implements Command {
    public function execute(Request $request, Response $response) {
        $username = $_SESSION['username'];
        $meldung = $username . ' wurde erfolgreich ausgelogt';
        session_destroy();
        $view = "login";
        $template = new HtmlTemplateView($view);
        $template->assign('meldung', $meldung);
        $template->render($request, $response);
    }
}

?>