<?php
namespace classes\commands;

use classes\request\Request;
use classes\response\Response;

interface Command{
	public function execute(Request $request, Response $response);
}