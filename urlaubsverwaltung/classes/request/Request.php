<?php
namespace classes\request;

interface Request{
    public function getParameterNames();
    public function issetParameter($name);
    public function getParameter($name);
    public function getHeader($name);
}
?>