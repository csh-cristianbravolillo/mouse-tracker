<?php if (!defined('APP_NAME')) exit("Direct access to this file is not allowed.");

class Filter
{
	private $patt = null;
	private $reps = null;
	private $filters = null;
	private static $php_filters = array(
		'ltrim','rtrim','trim','strtolower','strtoupper','ucfirst','ucwords'
	);

	function __construct()
	{
		$this->patt = array();
		$this->reps = array();
		$this->filters = array();
		if (func_num_args()>0)
			foreach (func_get_args() as $flt)
				 $this->add($flt);
	}

	function add($filter)
	{
		if (in_array($filter,Filter::$php_filters))
			$this->filters[] = $filter;
		else switch ($filter)
		{
			case 'makeurls': $this->addRegExp("(https?:\/\/[^\s<]+)","<a href='$1'>$1</a>"); break;
			case 'collapse': $this->addRegExp("\s+"," "); break;
		}
	}

	function addRegExp($pattern,$replacement)
	{
		$this->patt[] = "/$pattern/";
		$this->reps[] = $replacement;
	}

	function filter($txt)
	{
		foreach ($this->filters as $flt)
			eval("\$txt = $flt(\$txt);");
		return preg_replace($this->patt,$this->reps,$txt);
	}
}

?>