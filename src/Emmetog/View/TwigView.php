<?php

namespace Emmetog\View;

use Emmetog\View\View;

class TwigView extends View
{

    /**
     * The variables that are assigned to this view.
     * 
     * @var array
     */
    protected $assignedVars = array();

    /**
     * The template (layout) to use to display the view.
     *
     * @var string
     */
    protected $template = '';

    /**
     * Assign a variable to this view.
     * 
     * @param string $variable The name of the variable to assign.
     * @param mixed $value The value of the variable.
     */
    public function assign($variable, $value)
    {
        $this->assignedVars[$variable] = $value;
    }

    /**
     * Gets all the variables that are assigned to this view.
     * 
     * @return array
     */
    public function getAssignedVariables()
    {
        return $this->assignedVars;
    }

    /**
     * Unassigns a variable from this view.
     * 
     * @param string $variable The variable name to unassign.
     */
    public function removeAssignedVariable($variable)
    {
        if (array_key_exists($variable, $this->assignedVars))
        {
            unset($this->assignedVars[$variable]);
        }
    }

    /**
     * Sets the template for this view.
     * 
     * @param string $templatePath The path to the template (inside the Template dir).
     */
    public function setTemplate($templatePath)
    {
        $this->template = $templatePath;
    }

    /**
     * Renders the view.
     * 
     * @throws TwigViewNoTemplateSpecifiedException If the template was not specified.
     */
    public function render()
    {
        // Get the template directory.
        $template_directory = $this->config->getConfiguration('paths', 'template_path');

        if (!is_dir($template_directory))
        {
            throw new TwigViewTemplateDoesNotExistException('The template directory ("'.$template_directory.'") does not exist!');
        }

        $loader = new \Twig_Loader_Filesystem($template_directory);
        $options = array();
        $twig = new \Twig_Environment($loader, $options);

        if (empty($this->template))
        {
            throw new TwigViewNoTemplateSpecifiedException();
        }

        $template = $twig->loadTemplate($this->template);
        $html = $template->render($this->assignedVars);

        echo $html;
    }

}

class TwigViewException extends \Exception
{
    
}

class TwigViewNoTemplateSpecifiedException extends TwigViewException
{
    
}

class TwigViewTemplateDoesNotExistException extends TwigViewException
{
    
}

?>
