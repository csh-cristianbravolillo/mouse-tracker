<?php if (!defined('APP_NAME')) exit("Direct access to this file is not allowed.");

class Element
{
	private $meta = null;
	private $onload = null;
	private $content = null;
	private $script = null;
	private $css = null;
	private $form = null;

	static function genMeta($name,$content)
	{ return "<meta http-equiv=\"$name\" content=\"$content\"/>"; }

	static function genCSSRef($ref)
	{ return "<link rel='stylesheet' type='text/css' href='$ref'/>"; }

	static function genCSSIn($content)
	{ return "<style type='text/css'>$content</style>"; }

	static function genScriptRef($ref)
	{ return "<script type='text/javascript' src='$ref'></script>"; }

	static function genScriptIn($content)
	{ return "<script type='text/javascript'>$content</script>"; }

	static function genFormVar($name,$content)
	{ return "<input type='hidden' name='$name' value='$content'/>"; }

	#>------------------------------------------
	function __construct()
	{
		$this->meta = array();
		$this->onload = array();
		$this->content = array();
		$this->script = array();
		$this->script['ref'] = array();
		$this->script['in'] = array();
		$this->css = array();
		$this->css['ref'] = array();
		$this->css['in'] = array();
		$this->form = array();
 	}

	#>------------------------------------------
	function addContent($line)
	{ $this->content[] = $line; }

	function getContent($empty=true)
	{
		$ret = implode("",$this->content);
		if ($empty)
		{
			unset($this->content);
			$this->content = array();
		}
		return $ret;
	}

	#>------------------------------------------
	function addOnload($code,$priority=false)
	{
		if (!$code) return false;
		if (substr($code,-1)!=';') $code .= ';';
		if ($priority)
			array_unshift($this->onload,$code);
		else
			$this->onload[] = $code;
	}

	function getOnload()
	{ return implode("",$this->onload); }

	#>------------------------------------------
	function addScriptIn($name,$content)
	{
		if (!isset($this->script['in'][$name])) $this->script['in'][$name] = array();
		$this->script['in'][$name][] = $content;
	}

	function addScriptRef($ref)
	{ $this->script['ref'][] = trim($ref); }

	function getScripts()
	{
		$head = '';

		#> Ref scripts
		foreach (array_unique($this->script['ref']) as $ref)
			$head .= Element::genScriptRef($ref);

		#> Inline scripts
		if (count($this->script['in'])>0)
		{
			$tmp = '';
			foreach ($this->script['in'] as $name => $content)
				$tmp .= "function $name { ".implode(" ",$content)." }\n";
			$head .= Element::genScriptIn($tmp);
		}
		return $head;
	}

	#>------------------------------------------
	function addCSSIn($class,$key,$value)
	{
		if (!isset($this->css['in'][$class])) $this->css['in'][$class] = array();
		$this->css['in'][$class][$key] = trim($value);
	}

	function addCSSRef($ref)
	{ $this->css['ref'][] = trim($ref); }

	function addCSSClass($class,$ref)
	{ $this->css['in'][$class] = $ref; }

	function getCSS()
	{
		$head = '';

		#> Ref stylesheets
		foreach (array_unique($this->css['ref']) as $ref)
			$head .= Element::genCSSRef($ref);

		#> Inline stylesheets
		if (count($this->css['in'])>0)
		{
			$tmp = '';
			foreach (array_keys($this->css['in']) as $class)
			{
				$tmp .= "$class { ";
				if (is_array($this->css['in'][$class]))
					foreach ($this->css['in'][$class] as $k => $v)
						$tmp .= "$k:$v; ";
				else
					$tmp .= $this->css['in'][$class]." ";

				$tmp .= "}";
			}
			$head .= Element::genCSSIn($tmp);
		}

		return $head;
	}

	#>------------------------------------------
	function addMeta($name,$content)
	{ $this->meta[$name] = trim($content); }

	function getMeta()
	{
		#> We generate the headers
		$head = '';
		foreach ($this->meta as $name => $content)
			$head .= Element::genMeta($name,$content);
		return $head;
	}

	#>------------------------------------------
	function setFormVar($name,$value)
	{ $this->form[trim($name)] = urlencode(trim($value)); }

	function getFormVar($name)
	{ return $this->form[trim($name)] || false; }

	function getFormVars()
	{
		$tmp = '';
		foreach ($this->form as $k => $v)
			$tmp .= Element::genFormVar($k,$v);
		return $tmp;
	}

	#> This method should be overloaded by classes extending this one.
	function generate($mode='') {}
}
?>