<?php

namespace Emmetog\View;

use Emmetog\View\ViewInterface;

class PhpView implements ViewInterface
{

    protected $assignedVars = array();
    protected $template = '';

    public function assign($variable, $value) {
	$this->assignedVars[$variable] = $value;
    }

    public function getAssignedVariables() {
	return $this->assignedVars;
    }

    public function removeAssignedVariable($variable) {
	if (array_key_exists($variable, $this->assignedVars)) {
	    unset($this->assignedVars[$variable]);
	}
    }

    public function setTemplate($templatePath) {
	$this->template = $templatePath;
    }

    public function render() {
	extract($this->assignedVars);
	require_once $this->template;
    }

}

?>
