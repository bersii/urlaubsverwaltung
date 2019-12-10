<?php
namespace classes\template;

use classes\request\Request;
use classes\response\Response;

class HtmlTemplateView implements TemplateView{
    private $template;
    private $vars = array();

    public function __construct($template){
        $this->template = $template;
    }

    public function __get($property){
        if(isset($this->vars[$property])){
            return $this->vars[$property];
        }
        return null;
    }

    public function assign($name, $value){
        $this->vars[$name] = $value;
    }

    public function render(Request $request, Response $response){
        ob_start();
        $filename = "views/". $this->template .".php";
        require_once $filename;
        $data = ob_get_clean();
        $response->write($data);
    }
}
?>