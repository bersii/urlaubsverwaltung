<?php
namespace classes\filter;

use classes\request\Request;
use classes\response\Response;

class FilterChain{
	private $filters = array();
	
	public function addFilter(Filter $filter){
		$this->filters[] = $filter;
	}
	
	public function processFilters(Request $request, Response $response){
		foreach($this->filters as $filter){
			$filter->execute($request, $response);
		}
	}
}
?>