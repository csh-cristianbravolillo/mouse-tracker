<?php if (!defined('APP_NAME')) exit("Direct access to this file is not allowed.");

class Query
{
	#> Connector
	private $db = null;

	#> For the internal query
	private $query = null;
	private $what = null;
	private $tabs = null;
	private $where = null;
	private $order = null;
	private $pairs = null;
	private $mode = null;

	function __construct($db)
	{
		$this->db = $db;
		$this->reset();
	}

	function reset()
	{
		$this->query = "";
		$this->what = "";
		$this->tabs = "";
		$this->where = "";
		$this->order = array();
		$this->pairs = "";
		$this->mode = "";
	}

	#> Creates one condition to be put in the where clause.
	function c($var,$op,$val)
	{
		$var = $this->quoteparticle($var);
		$val = trim($val);
		if ($op=='~')
			return "$var like '%$val%'";
		else
			return $var.$op.$this->quoteparticle($val);
	}

	function y()
	{ return "(".implode(' and ',func_get_args()).")"; }

	function o()
	{ return "(".implode(' or ',func_get_args()).")"; }

	#> The model for a select:
	#> $q = new query($db);
	#> $q->select('col1,col2')->from('table')->where($q->c('col1','~','blabla'))->orderby('col2');
	function select($arg)
	{
		$arg = trim($arg);
		if (preg_match("/^distinct\s+([^\s]+)/", $arg, $tmp))
			$this->what = "distinct ".$this->quote($tmp[1]);

		elseif (preg_match("/^(count|max|min|avg)\(([^\)]+)\)/", $arg, $tmp))
		{
			if (trim($tmp[2])=='*')
				$this->what = $tmp[1]."(*)";
			else
				$this->what = $tmp[1]."(".$this->quoteparticle($tmp[2]).")";
		}
		else
			$this->what = $this->quote($arg);

		$this->mode = 'select';
		return $this;
	}

	function rawSelect($arg) // "Literal" select, no interpolation.
	{
		$this->what = trim($arg);
		$this->mode = 'select';
		return $this;
	}

	function from($arg)
	{
		$this->tabs = $this->quote(trim($arg));
		return $this;
	}

	function where($arg)
	{
		$this->where = trim($arg);
		return $this;
	}

	function orderby($arg)
	{
		if ($arg) $this->order[] = $this->quote($arg);
		return $this;
	}

	#> The model for an insert:
	#> $q->insert('name','hola')->into('table')->where($q->c('id','=','3'));
	function insert()
	{
		$this->setPairs(func_get_args());
		$this->mode = "insert";
		return $this;
	}

	#> This is a delayed type of insert. It accepts a list of things, e.g.:
	#> $narf = array('one' => 'uno', 'two' => 'dos');
	#> $q->insertList($narf)->into('table')->where($q->c('id','=','3'));
	function insertList($ref)
	{
		$this->setPairsFromArray($ref);
		$this->mode ="insert";
		return $this;
	}

	function into($arg)
	{
		$this->from($arg);
		return $this;
	}

	#> The model for an update:
	#> $q->update('name','hola')->into('table')->where($q->c('id','=','3'));
	function update()
	{
		$this->setPairs(func_get_args());
		$this->mode = "update";
		return $this;
	}

	#> The model for this update is analogous to insertList().
	function updateList($ref)
	{
		$this->setPairsFromArray($ref);
		$this->mode = "update";
		return $this;
	}

	#> The model for a delete:
	#> $q->deletefrom('table')->where(...);
	function deletefrom($arg)
	{
		$this->from($arg);
		$this->mode = "delete";
		return $this;
	}

	function run($debug=false)
	{
		switch ($this->mode)
		{
			case 'select':
				$this->query = "select ".$this->what." from ".$this->tabs;
				if ($this->where) $this->query .= " where ".$this->where;
				if ($this->order) $this->query .= " order by ".implode(',',$this->order);
				if ($debug)
					$returnval = $this->query;
				else
					$returnval = $this->db->qSelect($this->query);
				break;

			case 'insert':
				$this->query = "insert into ".$this->tabs;
				$this->query .= "(".implode(',',array_keys($this->pairs)).") values ";
				$this->query .= "(".implode(',',array_values($this->pairs)).")";
				if ($debug)
					$returnval = $this->query;
				else
					$returnval = $this->db->qInsert($this->query);
				break;

			case 'update':
				$this->query = "update ".$this->tabs." set ";
				$tmp = array();
				foreach ($this->pairs as $k => $v)
					$tmp[] = "$k=$v";
				$this->query .= implode(',',$tmp);
				if ($this->where) $this->query .= " where ".$this->where;
				if ($debug)
					$returnval = $this->query;
				else
					$returnval = $this->db->qUpdateOrDelete($this->query);
				break;

			case 'delete':
				$this->query = "delete from ".$this->tabs;
				if ($this->where) $this->query .= " where ".$this->where;
				if ($debug)
					$returnval = $this->query;
				else
					$returnval = $this->db->qUpdateOrDelete($this->query);
				break;

			default:
				return false;
		}
		$this->reset();
		return $returnval;
	}

	function runAndGetValue($idx=null)
	{
		$tmp = $this->run();

		if (is_array($tmp) && count($tmp)>0)
		{
			$tmp = $tmp[0];
			$vals = array_values($tmp);
			if (count($vals)==1) { return $vals[0]; }
			if (count($vals)==0) return false;
			if ($idx && isset($tmp[$idx]))
				return $tmp[$idx];
		}
		return false;
	}

	function runAndGetRow()
	{
		$tmp = $this->run();

		if (is_array($tmp) && count($tmp)>0)
			return $tmp[0];
		return false;
	}

	function runAndGetColumn($idx)
	{
		if (!$idx) return false;
		$tmp = $this->run();
		if (is_array($tmp) and count($tmp)>0)
		{
			$results = array();
			foreach ($tmp as $row)
				$results[] = $row[$idx];
			return $results;
		}
		return false;
	}

	#> -----------------------------------------------------------------------
	private function quote($str)
	{
		if ($str=='*')
			return '*';
		else if (strpos($str,','))
		{
			$lst = explode(',',$str);
			for ($i=0; $i<count($lst); $i++)
				$lst[$i] = $this->quoteparticle($lst[$i]);
			return implode(',',$lst);
		}
		else
			return '`'.trim($str).'`';
	}

	private function quoteparticle($str)
	{
		$str=trim($str);
		if (($j=strpos($str,' '))>-1)
			return '`'.substr($str,0,$j).'`'.substr($str,$j);
		else if (($j=strpos($str,'.'))>-1)
			return substr($str,0,$j).'.`'.substr($str,$j+1).'`';
		else
			return '`'.$str.'`';
	}

	private function setPairs($ref)
	{
		if (!is_array($ref) || count($ref)%2 == 1)
			throw new GenException(GEN_ERR_DB,6);

		for ($i=0; $i<count($ref); $i+=2)
			$this->pairs[$this->quoteparticle($ref[$i])] = "'".$this->db->escape($ref[$i+1])."'";
	}

	private function setPairsFromArray($ref)
	{
		if (!is_array($ref))
			throw new GenException(GEN_ERR_DB,6);

		foreach (array_keys($ref) as $k)
			$this->pairs[$this->quoteparticle($k)] = "'".$this->db->escape($ref[$k])."'";
	}
}
?>