<?php if (!defined('APP_NAME')) exit("Direct access to this file is not allowed.");

class Canvas extends Template
{
	private $object = null;
	private $mode = null;
	private $order = null;
	public $common = null;

	// Constructor
	function __construct($thisurl,$template='base.tpl')
	{
		parent::__construct($template);
		$this->object = array();
		$this->assign('title', APP_NAME);
		$this->assign('formaction',$thisurl);
		$this->assign('doctype',"<!DOCTYPE html>");

		// We obtain the mode
		$this->mode = getvar('mode','show');
		$this->order = getvar('order','');

		// We use a dummy object to generate the needed elements for the canvas.
		$this->common = new Element();
		$this->common->addMeta('content-type','text/html;charset=utf-8');
		$this->common->addCSSRef(APP_URL . 'files/gen.css.php');
		$this->common->addScriptRef(APP_URL . 'files/gen.js');
		$this->common->setFormVar('mode', $this->mode);
		$this->common->setFormVar('order',$this->order);
	}

	function getMode()
	{ return $this->mode; }

	function getOrder()
	{
		if ($this->order=='') return false;
		return explode(',',$this->order);
	}

	// We register an object to be displayed later.
	function register($obj)
	{
		if (!is_a($obj,"Element"))
		{
			throw new GenException(GEN_ERR_CANVAS,1);
			return false;
		}
		$this->object[] = $obj;
		return true;
	}

	function display()
	{
		array_unshift($this->object, $this->common);
		foreach ($this->object as $obj) $obj->generate();

		#> Then we compose the whole thing from each object.
		$meta = array();
		$scripts = array();
		$css = array();
		$content = '';
		$onload = '';
		$formvars = array();

		foreach ($this->object as $obj)
		{
			$meta[] = $obj->getMeta();
			$scripts[] = $obj->getScripts();
			$css[] = $obj->getCSS();
			$content .= $obj->getContent();
			$onload .= $obj->getOnload();
			$formvars[] = $obj->getFormVars();
		}

		$meta = implode("\n",array_unique($meta));
		$scripts = implode("\n",array_unique($scripts));
		$css = implode("\n",array_unique($css));
		$formvars = implode("\n",array_unique($formvars));

		#> We assign the onload in case there is one
		if ($onload!='')
			$scripts .= Element::genScriptIn("\nfunction ng_init() { $onload }");

		#> We wrap the form vars
		if ($formvars) $formvars = "<div style='display:none;'>\n$formvars</div>";

		#> All remaining assignments and we finish.
		$this->assign('meta',		$meta);
		$this->assign('scripts',	$scripts);
		$this->assign('css',		$css);
		$this->assign('content',	$content);
		$this->assign('formvars',	$formvars);
		parent::display();
	}

	function displayMsg($msg)
	{
		$this->common->addContent("<div class='message'>$msg</div>");
		$this->register($this->common);
		$this->display();
	}

	function redirect($url)
	{
		if (!$url) $url = APP_URL;
		$this->common->addOnload("load('$url');");
		$this->display();
	}
}
?>