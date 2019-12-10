<?php
namespace classes\filter;

use classes\request\Request;
use classes\response\Response;

interface Filter{
	public function execute(Request $request, Response $response);
}
?>