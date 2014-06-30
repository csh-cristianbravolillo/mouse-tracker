<?php if (!defined('APP_NAME')) exit("Direct access to this file is not allowed.");

class Toolbar extends Element
{
	private $blocks = null;

	function __construct()
	{
		parent::__construct();
		$this->blocks = array();
	}

	function addInfo($content)
	{ $this->blocks[] = "<div class='info'>$content</div>"; }

	function addInfoPairs($pairs)
	{
		$nice = array();
		foreach ($pairs as $k => $v)
			$nice[] = "<strong>".ucfirst($k)."</strong>: <small>$v</small>";
		$this->addInfo(implode('&nbsp;&bull;&nbsp;', $nice));
	}

	function addAction($name,$action,$img)
	{ $this->blocks[] = "<div class='action' onclick=\"$action\"><span>$name</span><br/><img src='".APP_URL."images/actions/$img.png' alt='toolbar command'/></div>"; }

	function addRawBlock($content)
	{ $this->blocks[] = $content; }

	function generate($mode='')
	{
		$tmp = "<div class='toolbar'>";
		foreach ($this->blocks as $block) $tmp .= $block;
		$tmp .= "<div class='appid'>".APP_NAME."<br/>&copy;2010-2014 <a href='http://cups.cs.cmu.edu' target='_new'>CUPS Lab</a></div></div>\n";
		$this->addContent($tmp);
		$this->addCSSRef(APP_URL . 'files/toolbar.css.php');
	}
}
?>