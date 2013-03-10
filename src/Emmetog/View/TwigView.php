<?php

namespace Emmetog\View;

use Emmetog\View\View;

class TwigView extends View
{

    protected $assignedVars = array();
    protected $template = '';

    public function assign($variable, $value)
    {
        $this->assignedVars[$variable] = $value;
    }

    public function getAssignedVariables()
    {
        return $this->assignedVars;
    }

    public function removeAssignedVariable($variable)
    {
        if (array_key_exists($variable, $this->assignedVars))
        {
            unset($this->assignedVars[$variable]);
        }
    }

    public function setTemplate($templatePath)
    {
        $this->template = $templatePath;
    }

    public function render()
    {
        // Not needed now that we are using composer.
//        require_once 'Twig/Autoloader.php';
//        \Twig_Autoloader::register();
        
        // Get the template directory.
        $template_directory = APP_ROOT_DIRECTORY . 'Template' . DIRECTORY_SEPARATOR;
        $loader = new \Twig_Loader_Filesystem($template_directory);
        $options = array();
        $twig = new \Twig_Environment($loader, $options);
        
        $template = $twig->loadTemplate($this->template);
        $html = $template->render($this->assignedVars);
        
        echo $html;
    }

}

?>
