<?php
class FactoryController extends Controller {
	
	//@override
	public function afterRender($view, &$output) {
		$op = new sourceOptimize();
		$op->prefix = 'http://manage'.DOMAIN;
		$op->context = $output;
		// 		$op->dict = 'application.data.resmap';
		$op->run();
		$output = $op->context;
	}
}