<?php if (!defined('APP_NAME')) exit('Direct access to this file is not allowed.');

class Template extends Smarty
{
	private $template = null;

	function __construct($template)
	{
		parent::__construct();
		$this->template = $template;
		$this->setTemplateDir(APP_ROOT . 'smarty/templates/');
		$this->setCompileDir(APP_ROOT . 'smarty/cache/');
		$this->setConfigDir(APP_ROOT . 'smarty/cache/');
		$this->setCacheDir(APP_ROOT . 'smarty/cache/');
	}

	function display()
	{ parent::display($this->template); }

	function generate()
	{ return parent::fetch($this->template); }
}
?>