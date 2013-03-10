<?php

namespace Emmetog\View;

interface ViewInterface {
    
    public function assign($variable, $value);
    
    public function removeAssignedVariable($variable);
    
    public function getAssignedVariables();
    
    public function setTemplate($templatePath);

    public function render();
    
}
?>
