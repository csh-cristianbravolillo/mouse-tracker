<?php if (!defined('APP_NAME')) exit("Direct access to this file is not allowed.");

class Table extends Element
{
	private static $column_properties = array(
		'label',		# Sets a label for the column
		'visible',		# Makes the column visible/invisible
		'order',		# Makes the header of the column a command to order the table by this column
		'filter',		# Links the column with a filter.
		'clickable',	# Makes each cell of this column a link to select the row.
		'userfilter',	# Creates an overlay to allow the user to filter the results.
	);
	private static $table_properties = array(
		'paging', 		# Presentation of rows in sets of 'paging' elements.
		'highlight',	# Whether to highlight the row being hovered or not.
		'cell_padding',	# Usual properties in tables.
		'cell_spacing',
		'style',		# The css class that will be assigned to the table.
		'addstyle',		# Additional style for the table.
		'numbering',	# Whether the rows should be numbered or not.
		'id',			# Column that should be considered a unique index (a.k.a. "primary key")
		'caption',		# Yes, you got it right. A caption for the whole table.
	);
	private $elems = null;
	private $elems_dup = null;
	private $labels = null;
	private $gral = null;
	private $id = null;
	private static $idc = 100;

	/* ------------------- *
	 * --- Constructor --- *
	 * ------------------- */
	function __construct($elems=null,$id=null)
	{
		parent::__construct();
		if ($elems) $this->setElems($elems,$id);
		$this->setProperty('cell_padding','2');
		$this->setProperty('cell_spacing','0');
		$this->addCSSRef(APP_URL . 'files/table.nav.css.php');
		$this->id = "tb".Table::$idc;
		Table::$idc++;
	}

	function setElems($elems,$id=null)
	{
		if (!is_array($elems))
		{
			throw new GenException(GEN_ERR_TABLE,4);
			return false;
		}
		if (count($elems)==0) return false;

		$this->elems = $elems;
		foreach (array_keys($elems[0]) as $name)
			$this->resetColumn($name);

		if ($id)
		{
			$this->checkColumnExists($id);
			$this->setProperty('id',$id);
		}
		return true;
	}

	function isEmpty()
	{ return (count($this->elems)==0); }

	/* ------------------------------------------ *
	 * --- Methods that add or delete columns --- *
	 * ------------------------------------------ */
	#> Adds a column that is a link to the row. It assumes that
	#> within the table, there is a column by the name 'id' that
	#> identifies uniquely each row.
	function addClickableIconColumn($name,$idfld,$icon,$action,$confirm='')
	{
		if ($this->isEmpty()) return false;
		$this->checkColumnExists($name,false);
		if (!($idcol = $this->getProperty('id')))
		{
			throw new GenException(GEN_ERR_TABLE,5);
			return false;
		}
		foreach (array_keys($this->elems) as $i)
			$this->elems[$i][$name] =
				"<img class='tablink' src='".APP_URL."images/actions/$icon.png' onclick=\"set('$idfld','".$this->elems[$i][$idcol]."');".($confirm?"doConfirm('$confirm',":"doThis(")."'$action')\"/>";
		$this->resetColumn($name);
		$this->setFormVar($idfld,'');
		return true;
	}

	#> Deletes a column. Use with wisdom.
	function deleteColumn($name)
	{
		if ($this->isEmpty()) return false;
		$this->checkColumnExists($name);
		foreach ($this->elems as $i)
			unset($this->elems[$i][$name]);
		return true;
	}

	private function resetColumn($name)
	{
		if ($this->isEmpty()) return false;
		$this->checkColumnExists($name);
		$this->setColumnProperty($name,'label',$name);
		$this->setColumnProperty($name,'visible',true);
		$this->setColumnProperty($name,'order',false);
		$this->setColumnProperty($name,'userfilter',false);
		return true;
	}

	/* ---------------------------------------------- *
	 * --- Methods that set properties of columns --- *
	 * ---------------------------------------------- */
	function setColumnProperty($name,$property,$val)
	{
		if ($this->isEmpty()) return false;
		$this->checkColumnExists($name);

		if (!in_array($property,Table::$column_properties))
		{
			throw new GenException(GEN_ERR_TABLE,3,$property,implode(',',Table::$column_properties));
			return false;
		}

		if (($property=='visible' || $property=='order' || $property=='userfilter') && !is_bool($val))
		{
			throw new GenException(GEN_ERR_TABLE,6,$property,$name);
			return false;
		}

		if ($property=='filter' && !is_a($val,"Filter"))
		{
			throw new GenException(GEN_ERR_TABLE,7,$name,$val);
			return false;
		}

		$this->labels[$name][$property] = $val;
		return true;
	}

	// A convenience function.
	function setColumnProperties($properties)
	{
		if (!is_array($properties))
		{
			throw new GenException(GEN_ERR_TABLE,8);
			return false;
		}

		foreach (array_keys($properties) as $col)
		{
			if (!is_array($properties[$col]))
			{
				throw new GenException(GEN_ERR_TABLE,8);
				return false;
			}

			foreach (array_keys($properties[$col]) as $prop)
				$this->setColumnProperty($col, $prop, $properties[$col][$prop]);
		}
		return true;
	}

	function orderByColumn($name,$asc=true)
	{
		if ($this->isEmpty()) return false;
		$this->checkColumnExists($name);
		foreach ($this->elems as $row)
			foreach (array_keys($row) as $key)
				${$key}[] = $row[$key];
		array_multisort($$name,($asc? SORT_ASC:SORT_DESC),$this->elems);
		return true;
	}

	function filterByColumn($name, $vals)
	{
		if ($this->isEmpty()) return false;
		$this->checkColumnExists($name);

		$i=0;
		while ($i<count($this->elems))
			if (in_array($this->elems[$i][$name], $vals))
				array_splice($this->elems, $i, 1);
			else
				$i++;
	}

	/* ------------------------------------------------ *
	 * --- Methods that set properties of the table --- *
	 * ------------------------------------------------ */
	function setProperty($name,$val)
	{
		if ($this->isEmpty()) return false;
		if (!in_array($name,Table::$table_properties))
		{
			throw new GenException(GEN_ERR_TABLE,3,$name,implode(',',Table::$table_properties));
			return false;
		}
		$this->gral[$name] = $val;

		#> Post-processing
		switch ($name)
		{
			case 'paging':
				$this->setFormVar('from',0);
				break;
		}
		return true;
	}

	function getProperty($name)
	{ return (isset($this->gral[$name])? $this->gral[$name]:false); }

	/* --------------------- *
	 * --- Other methods --- *
	 * --------------------- */
	function doesColumnExist($name)
	{
		if ($this->isEmpty()) return false;
		return in_array($name,array_keys($this->elems[0]));
	}

	private function checkColumnExists($name,$bit=true)
	{
		if ($this->isEmpty()) return false;
		if ($this->doesColumnExist($name)!=$bit)
		{
			throw new GenException(GEN_ERR_TABLE,$bit? 1:2,$name);
			return false;
		}
		return true;
	}

	function getPager()
	{
		$paging = getvar('paging',0);
		$pag_opts = array(
			'0' => 'No paging',
			'20' => '20',
			'40' => '40',
			'60' => '60',
			'80' => '80',
			'100' => '100',
			'200' => '200',
			'300' => '300',
			'400' => '400',
			'500' => '500',
		);
		$cont = "";
		foreach ($pag_opts as $k=>$v) $cont .= "<option value='$k'".($paging==$k? " selected='1'":'').">$v</option>";
		if ($paging>0) $this->setProperty('paging',$paging);
		return "<strong>Paging</strong>:<br/><select name='paging' onchange='frm.submit()'>$cont</select>";
	}

	function getSmartHeader($name)
	{
		$this->checkColumnExists($name);
		$vals = array();

		foreach ($this->elems as $row)
			$vals[$row[$name]] = 1;
		$vals = array_keys($vals);

		if (count($vals)>0)
		{
			sort($vals);
			$cont = "<div id='${name}_tip' class='gentip'>Filter out:<br/>";
			$i=1;
			foreach ($vals as $val)
			{
				$chname = $name.'_ch'.$i;
				$packvl = "packvalues('$name',".count($vals).");frm.submit()";
				$cont .= "<input type='checkbox' name='$chname'".(getvar($chname)? " checked='true'":'')." value='$val' onchange=\"$packvl\">";
				$cont .= "<div class='opt' onclick=\"toggle(frm.$chname);$packvl\">$val</div></input><br/>";
				$i++;
			}
			$cont .= "<div class='button' onclick=\"popUp('${name}_tip',false,event)\">Cancel</div>";
			$this->addCSSRef(APP_URL."files/dialogs.css.php");
			$this->addOnload("setDisplay('${name}_tip','unobservable')");
			$this->setFormVar("${name}_tot",count($vals));
			$this->setFormVar("${name}_pack",getvar($name.'_pack',''));
			return "$cont</div>\n";
		}
	}

	function generate($mode='')
	{
		#> If empty, nothing to say
		if ($this->isEmpty())
		{
			$this->addContent('<p>This view has no content.</a>');
			return;
		}

		#> Generation of smart headers tooltips
		foreach (array_keys($this->labels) as $name)
		{
			if ($this->labels[$name]['userfilter'])
				$this->addContent($this->getSmartHeader($name));
			if ($flt = getvar($name.'_pack'))
				$this->filterByColumn($name,explode('##',$flt));
		}

		#> Column ordering
		if ($order = getvar('order'))
			foreach (explode(',',$order) as $pair)
			{
				list($name,$dir) = explode('/',$pair);
				$this->orderByColumn($name,$dir=='asc');
			}

		#> Paging for rows
		if (($range=$this->getProperty('paging'))!==false)
		{
			$from = max(intval(getvar('from')),1);
			$last = count($this->elems);
			$to = min($from+$range-1,$last);
			$finalrow = "";

			if ($from>$range+1) $finalrow .= "<div class='nav' style='float:left;' onclick=\"setDo('from',1)\">&#10096;&#10096; First</div> ";
			if ($from>1) $finalrow .= "<div class='nav' style='float:left;' onclick=\"setDo('from',".max(1,$from-$range).")\">&#10096; Previous</div> ";
			if ($to+$range<$last) $finalrow .= "<div class='nav' style='float:right;' onclick=\"setDo('from',".(intval($last/$range)*$range+1).")\">Last &#10097;&#10097;</div>";
			if ($from<$last-$range) $finalrow .= "<div class='nav' style='float:right;' onclick=\"setDo('from',".($from+$range).")\">Next &#10097;</div>";
		}
		else
		{
			$from = 1;
			$to = count($this->elems);
			$finalrow = "";
		}

		#> Content of this drawable object
		$style = $this->getProperty('style')? $this->getProperty('style'):'simple';
		$addstyle = $this->getProperty('addstyle');
		if ($addstyle) $addstyle=" style='$addstyle'";
		$this->addContent(
			"<table cellpadding='".$this->getProperty('cell_padding')."' cellspacing='".$this->getProperty('cell_spacing').
			"' border='0' class='$style'$addstyle>\n");

		#> If we need paging, we display it before the headers.
		if ($finalrow)
			$this->addContent("<tr><td colspan='".(count($this->elems[0])+($this->getProperty('numbering')? 1:0))."'>$finalrow</td></tr>");

		#> If we need a caption, we display before the headers as well.
		if ($this->getProperty('caption'))
			$this->addContent("<tr><td class='caption' colspan='".(count($this->elems[0])+($this->getProperty('numbering')? 1:0))."'>".$this->getProperty('caption')."</td></tr>");

		#> Headers
		$this->addContent("<tr>\n");
		if ($this->getProperty('numbering'))
			$this->addContent("<th>#</th>");
		foreach (array_keys($this->labels) as $name)
			if ($this->labels[$name]['visible'])
			{
				$this->addContent("<th>");
				if ($this->labels[$name]['userfilter'])
					$this->addContent("<div class='microheader' onclick=\"popUp('${name}_tip',false,event)\">");
				$this->addContent($this->labels[$name]['label']);
				if ($this->labels[$name]['userfilter'])
					$this->addContent("</div>");

				#> If the header is to be ordered, we generate the corresponding link
				if ($this->labels[$name]['order'])
				{
					$this->addContent("<div class='order_asc' onclick=\"setDoShow('order','$name/asc')\"></div>");
					$this->addContent("<div class='order_desc' onclick=\"setDoShow('order','$name/desc')\"></div>");
				}

				$this->addContent("</th>");
			}
		$this->addContent("</tr>\n");

		#> Rows display
		for ($i=$from; $i<=$to; $i++)
		{
			#> We display the <tr> element.
			if ($this->getProperty('id'))
				$rowid = $this->elems[$i-1][$this->getProperty('id')];
			else
				$rowid = '';
			$this->addContent("<tr class='hl'".($rowid? " id='r$rowid'":'').">\n");

			#>  We add the numbering, if set.
			if ($this->getProperty('numbering'))
				$this->addContent("<td class='n'>$i</td>\n");

			#> And we finally display the elements of the row.
			foreach ($this->elems[$i-1] as $k=>$v)
				if ($this->labels[$k]['visible'])
				{
					if (isset($this->labels[$k]['filter']))
						$v = $this->labels[$k]['filter']->filter($v);
					$this->addContent("<td class='$k'>$v</td>\n");
				}
			$this->addContent("</tr>\n");
		}

		#> If we need paging, we also display it as last row.
		if ($finalrow) $this->addContent("<tr><td colspan='".(count($this->elems[0])+($this->getProperty('numbering')? 1:0))."'>$finalrow</td></tr>");
		$this->addContent("</table>");

		#> The styles we need
		if (!$this->getProperty('style')) $this->addCSSRef(APP_URL . 'files/table.css.php');
		$this->addScriptRef(APP_URL . 'files/table.js');
	}
}
?>