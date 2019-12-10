<?php
namespace classes\response;

interface Response{
    public function setStatus($status);
    public function addHeaders($name, $value);
    public function write($data);
    public function flush();
}
?>