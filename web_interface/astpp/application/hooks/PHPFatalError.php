<?php
class PHPFatalError{
	protected $CI;
	public function setHandler() {
		set_exception_handler('exception_handler');
	}
}
function exception_handler($exception) {
	if(!empty($exception->getMessage()))
	show_error($exception->getMessage(),500);
}

