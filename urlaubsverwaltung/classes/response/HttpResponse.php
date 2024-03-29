<?php
namespace classes\response;

class HttpResponse implements Response{
    private $status;
    private $headers;
    private $body;

    public function __construct(){
        $this->status = "200 OK";
        $this->headers = array();
        $this->body = null;
    }

    public function setStatus($status){
        $this->status = $status;
    }
    public function addHeaders($name, $value){
        $this->headers[$name] = $value;
    }
    public function write($data){
        $this->body .= $data;
    }
    public function flush(){
        header("HTTP/1.0 " . $this->status);
        foreach ($this->headers as $name => $value){
            header($name . ":" . $value);
        }
        print $this->body;
        $this->headers = array();
        $this->data = null;
    }
}
?>