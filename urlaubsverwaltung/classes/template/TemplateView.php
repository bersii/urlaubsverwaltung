<?php
namespace classes\template;

use classes\request\Request;
use classes\response\Response;

interface TemplateView{
    public function assign($name, $value);
    public function render(Request $request, Response $response);
}
?>