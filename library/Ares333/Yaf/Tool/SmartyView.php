<?php
namespace Ares333\Yaf\Tool;

use Smarty;
use Yaf\View_Interface;
use Yaf\Application;

class SmartyView implements View_Interface
{

    /**
     * Smarty object
     *
     * @var Smarty
     */
    protected $smarty;

    /**
     * Constructor
     *
     * @param string $tmplPath
     * @param array $extraParams
     * @return void
     */
    function __construct($tmplPath = null, $extraParams = array())
    {
        $name = 'Smarty';
        $this->smarty = new $name();
        $this->smarty->muteExpectedErrors();
        $dir = Application::app()->getAppDirectory();
        $params = array();
        $params['setCompileDir'] = $dir . '/../cache/smarty/compile';
        $params['setCacheDir'] = $dir . '/../cache/smarty/cache';
        $params['setConfigDir'] = $dir . '/../conf/smarty';
        $templateDir = $dir . '/views';
        $this->setScriptPath($templateDir);
        $params = array_merge($params, $extraParams);
        foreach ($params as $key => $value) {
            $this->smarty->$key($value);
        }
    }

    /**
     *
     * @return Smarty
     */
    public function getAdapter()
    {
        return $this->smarty;
    }

    /**
     * Set the path to the templates
     *
     * @param string $path
     *            The directory to set as the path.
     * @return void
     */
    public function setScriptPath($path)
    {
        $this->smarty->setTemplateDir($path);
    }

    /**
     * (non-PHPdoc)
     *
     * @see View_Interface::getScriptPath()
     */
    public function getScriptPath()
    {
        return rtrim(current($this->smarty->getTemplateDir()), '/');
    }

    /**
     * Assign variables to the template
     *
     * Allows setting a specific key to the specified value, OR passing
     * an array of key => value pairs to set en masse.
     *
     * @see __set()
     * @param string|array $spec
     *            The assignment strategy to use (key or
     *            array of key => value pairs)
     * @param mixed $value
     *            (Optional) If assigning a named variable,
     *            use this as the value.
     * @return void
     */
    public function assign($spec, $value = null)
    {
        if (is_array($spec)) {
            $this->smarty->assign($spec);
            return;
        }
        
        $this->smarty->assign($spec, $value);
    }

    /**
     * Processes a template and returns the output.
     *
     * @param string $name
     *            The template to process.
     * @param array $value
     * @return string The output.
     */
    public function render($name, $value = NULL)
    {
        if (isset($value)) {
            $this->smarty->assign($value);
        }
        return $this->smarty->fetch($name);
    }

    /**
     * output
     *
     * @param string $name
     *            The template to process.
     * @param array $value
     * @see View_Interface::display()
     */
    public function display($name, $value = NULL)
    {
        if (isset($value)) {
            $this->assign($value);
        }
        $this->smarty->display($name);
    }
}