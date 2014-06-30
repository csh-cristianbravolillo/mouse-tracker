<?php if (!defined('APP_NAME')) exit("Direct access to this file is not allowed.");

class Screen extends Element
{
	private $template = null;

	function __construct($width=0, $height=0)
	{
		parent::__construct();
		$this->template = new Template('screen.tpl');
		if ($width)
			$this->setWidth($width);
		if ($height)
			$this->setHeight($height);
	}

	function setWidth($w) { $this->template->assign('scsizex', $w); }
	function setHeight($h) { $this->template->assign('scsizey', $h); }

	function generate()
	{
		$this->addScriptRef(APP_URL . 'files/wrap.js');
		$this->addCSSRef(APP_URL . 'files/wrap.css.php');
		$this->addContent($this->template->generate());
	}
}
?>