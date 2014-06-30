<?php
	require_once('config/config.php');
	require_once(APP_CLASSPATH . 'Canvas.php');
	require_once(APP_CLASSPATH . 'Query.php');
	require('functions.php');

	#> Required objects and preparation
	$c = new Canvas(thisURL());
	$q = new Query($db);
	$data = new Template('player.tpl');

	$id = getvar('id','');
	$q->select('action,content,t')->from('runrow')->where("rid='$id'")->orderby('t,id');
	$inner = $q->run();
	$cols = extractValues($inner);
	$v = $cols['version'];

	#> Some more values that do not depend on the frames (i.e., single values that do not change)
	list($scwidth,$scheight) = explode(',', $cols['scsize']);
	list($vpwidth,$vpheight) = explode(',', $cols['vpsize']);
	$data->assign('scwidth',	intval($scwidth*$scalefactor));
	$data->assign('scheight',	intval($scheight*$scalefactor));
	$data->assign('totaltime',	toTime($cols['tmax']-$cols['tmin']));
	$data->assign('version',	$cols['Version']);
	$data->assign('context',	$cols['Context']);
	$data->assign('intent',		$cols['intent']);
	$data->assign('browser',	$cols['browser']);
	$data->assign('os',			$cols['os']);

	#> We reset some needed values in the template.
	$data->assign('obj1backg',	'');
	$data->assign('obj1top',	'');
	$data->assign('obj1left',	'');
	$data->assign('obj1width',	'');
	$data->assign('obj1height',	'');
	$data->assign('obj1backwidth',	'');
	$data->assign('obj1backheight',	'');

	$data->assign('obj2backg',	'');
	$data->assign('obj2top',	'');
	$data->assign('obj2left',	'');
	$data->assign('obj2width',	'');
	$data->assign('obj2height',	'');
	$data->assign('obj2backwidth',	'');
	$data->assign('obj2backheight',	'');
	$firstwpos = false;

	#> We set the values that depend on the study (currently only RP6)
	switch ($cols['study'])
	{
/*
		case 'rp6':
			$wfile = "v".$v;
			if ($v>0)
				$wfile .= 's'.(($cols['context']=='cmu'? 1:2)+($cols['intent']=='malicious'? 3:0));
			$wwidth = intval(($v==3? 500:366)*$scalefactor);
			$wheight = intval(($v==0? 230:($v==1? 328:($v==2? 354:530)))*$scalefactor);
			$data->assign('wfile', 		$wfile.'.png');
			$data->assign('wwidth',		$wwidth);
			$data->assign('wheight',	$wheight);
			$data->assign('wclipwidth',	$wwidth);
			$data->assign('wclipheight',	$wheight);

			switch ($cols['context'])
			{
				case 'cmu':
					$tempw = '100%';
					$temph = strval(intval(35*$scalefactor)).'px';
					$data->assign('obj1backg',		'cmu-bar.png');
					$data->assign('obj1top',		'0px');
					$data->assign('obj1left',		'0px');
					$data->assign('obj1width',		$tempw);
					$data->assign('obj1height',		$temph);
					$data->assign('obj1backwidth',	$tempw);
					$data->assign('obj1backheight',	$temph);
					$c->common->addOnload("setDisplay('wrapobj1','visible')");

					$tempw = strval(intval($vpwidth*0.6*$scalefactor)).'px';
					$temph = strval(intval(237*$scalefactor)).'px';
					$data->assign('obj2backg',		'cmu-center.png');
 					$data->assign('obj2top',		strval(intval(50*$scalefactor)).'px');
					$data->assign('obj2left',		strval(intval(200*$scalefactor)).'px');
     					$data->assign('obj2width',		$tempw);
     					$data->assign('obj2height',		$temph);
     					$data->assign('obj2backwidth',	$tempw);
     					$data->assign('obj2backheight',	$temph);
					$c->common->addOnload("setDisplay('wrapobj2','visible')");
					break;

				case 'ms':
					$data->assign('obj1backg',	'games-back.png');
					$data->assign('obj1top',	'0px');
					if ($vpwidth<1160)
						$templeft = 0;
					else
						$templeft = intval($vpwidth*0.11*$scalefactor);
					$data->assign('obj1left', "${templeft}px");

					$tempw = intval(972*$scalefactor).'px';
					$data->assign('obj1width',		$tempw);
					$data->assign('obj1height',		intval(min(1095,$vpheight)*$scalefactor).'px');
					$data->assign('obj1backwidth',	$tempw);
					$data->assign('obj1backheight',	intval(1095*$scalefactor).'px');
					$c->common->addOnload("setDisplay('wrapobj1','visible')");

					$data->assign('obj2backg',		'');
					$data->assign('obj2top',		'');
					$data->assign('obj2left',		'');
					$data->assign('obj2width',		'');
					$data->assign('obj2height',		'');
					$data->assign('obj2backwidth',	'');
					$data->assign('obj2backheight',	'');
					break;
			}
			break;
*/
		case 'rp10':
			$wwidth = intval(366*$scalefactor);

			switch ($cols['a'])
			{
				case 'noav': 	$wheight = 320; break;
				case 'cir':		$wheight = 430; break;
				case 'cis':		$wheight = 285; break;
				default:		$wheight = 270; break;
			}
			$wheight = intval($wheight*$scalefactor);

			$data->assign('wfile', 			"cond-".$cols['a'].$cols['s'].'.gif');
			$data->assign('wwidth',			$wwidth);
			$data->assign('wheight',		$wheight);
			$data->assign('wclipwidth',		$wwidth);
			$data->assign('wclipheight',	$wheight);

			$data->assign('obj1backg',		'games-back3.gif');
			$data->assign('obj1top',		'0px');
			if ($vpwidth<1160)
				$templeft = 0;
			else
				$templeft = intval($vpwidth*0.11*$scalefactor);
			$data->assign('obj1left', "${templeft}px");

			$tempw = intval(972*$scalefactor).'px';
			$data->assign('obj1width',		$tempw);
			$data->assign('obj1height',		intval(min(1095,$vpheight)*$scalefactor).'px');
			$data->assign('obj1backwidth',	$tempw);
			$data->assign('obj1backheight',	intval(1095*$scalefactor).'px');
			$c->common->addOnload("setDisplay('wrapobj1','visible')");
			break;

		default:
			$data->assign('wfile', "");
			$data->assign('wwidth', 0);
			$data->assign('wheight', 0);
			break;
	}

	#> -------------------------------------------------------------------------------------------------------
	#> -------------------------------------------------------------------------------------------------------
	#> Generation of frames.
	$frames = array();
	$vpwidth = 0; $vpheight = 0;
	$scx = 0; $scy = 0;
	$mx = 0; $my = 0;
	$wx = 0; $wy = 0;
	$firstmsg = 0;

	foreach ($inner as $row)
	{
		$frame = array();
		$frame['vpwidth'] = '';
		$frame['vpheight'] = '';
		$frame['scx'] = '';
		$frame['scy'] = '';
		$frame['mx'] = '';
		$frame['my'] = '';
		$frame['wx'] = '';
		$frame['wy'] = '';
		$frame['tt'] = $row['t']-$cols['tmin'];
		$frame['display'] = '';
		$frame['click'] = false;
		$frame['blink'] = false;
		$frame['msg'] = '';

		switch ($row['action'])
		{
			case 'vpsize':
				list($vpwidth,$vpheight) = explode(',',$row['content']);
				$frame['backsize'] = intval(970*$scalefactor);
				$frame['backleft'] = ($vpwidth<1160? 0:($vpwidth-1160)*.3)*$scalefactor;
				$frame['vpwidth'] = intval($vpwidth*$scalefactor);
				$frame['vpheight'] = intval($vpheight*$scalefactor);
				if ($firstmsg>1)
				      $frame['msg'] = 'Change viewport size';
                        else
				      $firstmsg++;
				break;

			case 'scpos':
				list($scx,$scy) = explode(',',$row['content']);
				$frame['scx'] = intval($scx*$scalefactor);
				$frame['scy'] = intval($scy*$scalefactor);
				if ($firstmsg>1)
      				$frame['msg'] = 'Change browser position';
                        else
      				$firstmsg++;
				break;

			case 'mpos':
			case 'click':
				list($mx,$my) = explode(',',$row['content']);
				$where = '';
				if (strpos($my,'@'))
					list($my,$where) = explode('@',$my);

				if ($where=='start')
					break;

				$frame['mx'] = intval($mx*$scalefactor);
				$frame['my'] = intval($my*$scalefactor);
				if ($row['action']=='click')
				{
					if (substr($where,0,4)!='wopt')
						$frame['msg'] = "Click @$where";
					$frame['click'] = true;
				}
				$lastmx = $frame['mx'];
				$lastmy = $frame['my'];
				break;

			case 'wpos':
				list($wx,$wy) = explode(',',$row['content']);
				if (!$firstwpos)
				{
					$frame['wx'] = intval($wx*$scalefactor);
					$frame['wy'] = intval($wy*$scalefactor);
					$firstwpos = true;
				}
				else
				{
					$frame['wx'] = intval(($wx-$lastwx+$lastmx)*$scalefactor);
					$frame['wy'] = intval(($wy-$lastwy+$lastmy)*$scalefactor);
				}
				$lastwx = $frame['wx'];
				$lastwy = $frame['wy'];
				break;

			case 'iev':
				switch ($row['content'])
				{
					case 'wappear':
						$frame['wx'] = intval(50*$scalefactor);
						$frame['wy'] = intval(100*$scalefactor);
						// no-break on purpose!

					case 'wdisappear':
						$frame['display'] = $row['content'];
						$frame['msg'] = 'Warning '.($row['content']=='wappear'? 'appeared':'disappeared').'.';
						break;

					case 'beginblink':
					 	$frame['blink'] = true;
					 	$frame['msg'] = 'Warning blinked!';
					 	break;

					case 'opt1':
					case 'opt2':
					case 'opt3':
						$frame['msg'] = 'Option '.substr($row['content'],3).' clicked.';
						break;

					default:
						$frame['msg'] = $row['content'];
						break;
				}
				break;
		}
		if ($frame['msg']) $frame['msg'] = "&#9679; ".$frame['msg'];
		$frames[] = $frame;
	}
	$data->assign('frames',$frames);

	$c->common->addContent($data->generate());
	$c->common->addCSSRef(APP_URL . 'files/wrap.css.php');
	$c->common->addScriptRef(APP_URL . 'files/wrap.js');
	$c->common->addOnload("maxtime=".($cols['tmax']-$cols['tmin'])."; wrapanimate();");
	$c->display();
?>