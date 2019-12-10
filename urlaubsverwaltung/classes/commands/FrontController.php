<?php
namespace classes\commands;

use classes\request\Request;
use classes\response\Response;
use classes\filter\Filter;
use classes\filter\FilterChain;


class FrontController {
	private $path;
	private $defaultCommand;

	public function __construct($path, $defaultCommand){
		$this->path = $path;
		$this->defaultCommand = $defaultCommand;
		$this->preFilters = new FilterChain();
	}


	public function handleRequest(Request $request, Response $response){
		$this->preFilters->processFilters($request, $response);
		$command = $this->getCommand($request);
		$command->execute($request, $response);
		$response->flush();
	}


	public function getCommand(Request $request){
		if ($request->issetParameter("cmd")) {
			$cmdName = $request->getParameter("cmd");
			$command = $this->loadCommand($cmdName);
			if ($command instanceof Command) {
				return $command;
			}
		}
		$command = $this->loadCommand($this->defaultCommand);

		return $command;
	}


	public function loadCommand($cmdName){
		$class = $this->path . "\\" . $cmdName . "Command";
		$file = $this->path . "/" . $cmdName . "Command.php";
		$file = str_replace('\\', DIRECTORY_SEPARATOR, $file);

		if (!file_exists($file)) {
			return false;
		}
		$command = new $class();
		return $command;
	}


	public function addPreFilter(Filter $filter){
		$this->preFilters->addFilter($filter);
	}
}