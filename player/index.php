<?php
	require_once('config.php');
	require_once('functions.php');
	require_once('../common/db.php');

	try
	{
		// Database connection first.
		$db = new CompactDB($mt_dbhost, $mt_dbname, $mt_dbuser, $mt_dbpass);
		$q = new Query($db);
		$chunks = $q->select('id,content')->from('track')->where("processed=0")->run();

		// Required objects and set up
		$c = new Canvas(thisURL());
		$c->common->addScriptRef(APP_URL . 'files/jquery.js');
		$c->common->addScriptRef(APP_URL . 'files/jquery-effects.js');

		switch ($c->getMode())
		{
			/* --------------------------------------------------------------------------------------------------------- */
			case 'show':
			default:
				// We create the toolbar
				$t = new Toolbar();
				$c->register($t);

				// If there are chunks to process, we display a button.
				if (count($chunks)>0)
					$t->addAction('Process '.count($chunks).' tracks',"doConfirm('This action may take a long time and make the browser\\nto timeout. Are you sure you want to do this?','process')",'go-jump');

				// We retrieve all the tracks we have stored in a pretty table.
				$mtrx = $db->qSelect(
					"select t.id, t.name, t.pid, from_unixtime(t.offset/1000) as t_start, round(max(e.t)/1000,1) as t_duration ".
					"from track as t, event as e where t.id=e.track_id and t.processed=1 group by e.track_id;"
				);
				$tab = new Table($mtrx);
				$tab->setProperty('highlight',	true);
				$tab->setProperty('numbering',	true);
				$tab->setProperty('id',			'id');
				$tab->setColumnProperty('id',			'visible',	false);
				$tab->setColumnProperty('name',			'label',	'Project');
				$tab->setColumnProperty('pid',			'label',	'Ppt. ID');
				$tab->setColumnProperty('t_start',		'label',	'Tracking start');
				$tab->setColumnProperty('t_duration',	'label',	'Duration [s]');
				$tab->addClickableIconColumn('Forget',		'trackid',	'edit-clear',			'forget');
				$tab->addClickableIconColumn('Download',	'trackid',	'document-save',		'down');
				$tab->addClickableIconColumn('Replay',		'trackid',	'media-playback-start',	'play');

				// We register the table.
				$c->register($tab);
				break;

			/* --------------------------------------------------------------------------------------------------------- */
			case 'process':
				if (count($chunks)>0)
					foreach ($chunks as $chunk)
					{
						// We split the chunk into triplets.
						$triplets = explode('#',$chunk['content']);

						// We parse the first triplet (it's special)
						$fields = parseTriplet(array_shift($triplets));
						$q->insertList(Array(
							'track_id'=> $chunk['id'],
							't' => 0,
							'action' => 'resize',
							'xc' => $fields['vpw'],
							'yc' => $fields['vph']
						))->into('event')->run();

						// We put the offset into the track table.
						$q->update('offset', $fields['offset'])->into('track')->where("id=".$chunk['id'])->run();

						// We process each triplet.
						foreach ($triplets as $triplet)
						{
							$fields = parseTriplet($triplet);
							$fields['track_id'] = $chunk['id'];

							switch ($fields['action'])
							{
								case 'mv':
									// No further processing is necessary in this case.
									break;

								case 'm-clk':
									replaceKey($fields, 'id', 'xtra1');
									replaceKey($fields, 'which', 'xtra2');
									break;

								case 'm-ent':
								case 'm-lev':
									replaceKey($fields, 'id', 'xtra1');
									break;

								case 'resize':
									replaceKey($fields, 'vpw', 'xc');
									replaceKey($fields, 'vph', 'yc');
									break;
							}

							// We finally insert into the track table.
							$q->insertList($fields)->into('event')->run();
						}

						// We update the track record as processed.
						$q->update('processed', 1)->into('track')->where("id=".$chunk['id'])->run();
					}
				$c->redirect();
				break;

			/* --------------------------------------------------------------------------------------------------------- */
			case 'forget':
				$t = new Toolbar();
				$t->addAction('Back', 'show()', 'go-back');
				$c->register($t);

				if ($trackid = getvar('trackid',''))
				{
					$c->displayMsg("OK, forgetting id=$trackid.");
				}
				else
					$c->displayMsg("I was supposed to receive an id to forget.");
				break;

			/* --------------------------------------------------------------------------------------------------------- */
			case 'play':
				// We create the toolbar
				$t = new Toolbar();
				$t->addAction('Back', 'show()', 'go-back');
				$t->addAction('Play', 'togglePlay()', 'media-playback-start');
				$t->addRawBlock("<div id='clock' class='clock'></div>");
				$t->addAction('Reset', 'trackReset()', 'media-playback-stop');
				$c->register($t);

				// We should receive a scalefactor.
				$scl = floatval(getvar('scl','0.7'));

				// We must receive an id.
				if ($trackid = getvar('trackid',''))
				{
					// ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
					// We obtain some data from the participant first.
					$q->rawSelect('name as project,pid,agent,from_unixtime(offset/1000) as start')->from('track')->where("id=$trackid");
					$ppt = $q->runAndGetRow();
					if ($ppt['project']=='') $ppt['project'] = '<em>None</em>';
					$t->addInfoPairs($ppt);

					// We get all the data.
					$events = $db->qSelect("select id,t,action,xtra1,xc,yc from event where track_id=$trackid order by t,id");
					$firstsize = array_shift($events);
					if ($firstsize['action']!='resize')
					{
						$c->displayMsg("Malformed tracking record (action=".$firstsize['action']."). Cannot replay.");
						break;
					}
					$totaltime = $db->qGetValue("select max(t) from event where track_id=$trackid");
					$c->common->addOnload("maxtime=$totaltime;");

					// We create the screen.
					$screen = new Screen(scale($firstsize['xc']), scale($firstsize['yc']));
					$c->register($screen);

					// Translation cycle
					$transevents = array();
					$tprev = 0;
					foreach ($events as $event)
					{
						$tdiff = $event['t'] - $tprev;
						$tprev = $event['t'];

						switch ($event['action'])
						{
							case 'mv':
								$transevents[] = "program.push(['$(\'#wrapmouse\').css({left:".scale($event['xc']).", top:".scale($event['yc'])."});', $tdiff]);";
								break;

							case 'm-ent':
							case 'm-lev':
								$transevents[] = "program.push(['$(\'#inmsg\').html(\'".($event['action']=='m-ent'? $event['xtra1']:'')."\');', $tdiff]);";
								break;

							case 'm-clk':
								$transevents[] = "program.push(['$(\'#wrapmouse\').css({left:".scale($event['xc']).", top:".scale($event['yc'])."});', $tdiff])";
								$transevents[] = "program.push(['$(\'#inmsg\').css(\'color\', \'red\');', 0])";
								$transevents[] = "program.push(['$(\'#inmsg\').html(\'&lt;".$event['xtra1']."&gt;<br/>\');', 0])";
								$transevents[] = "program.push(['$(\'#wrapcursor\').effect(\'pulsate\', {times:3}, 300);', 0])";
								$transevents[] = "program.push(['$(\'#inmsg\').css(\'color\', \'black\');', 301])";
								break;
						}
					}

					// Final preparations.
					$c->common->addOnload(implode("\n", $transevents));

					// ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
				}
				else
					$c->displayMsg("I did not receive an id for the record I'm supposed to play.");

/*

				// We put there multiple values
				$screen->assign('total_time', toTime($totaltime));
				$screen->assign('condition_name', getConditionName($condition));
				$screen->assign('scenario_name', getScenarioName($scenario));
				$screen->assign('scsizex', scale($values['scsizex']));
				$screen->assign('scsizey', scale($values['scsizey']));

				// We determine the characteristics of the image we'll use for the warning.
				$opt2x = 28;
				$opt2y = 143;
				$opt2w = 307;
				$opt2h = 53;
				$showingmouseover = 0;

				// We determine the features of different warnings
				$ww = array();
				$ww['width'] = 366;
				$ww['height'] = 270;
				if (in_array($condition, array('c','e','noav','cir','cis')))
					$ww['init'] = "$condition$scenario";
				else switch ($condition)
				{
					case 's':
					case 'acs':
						$ww['init']		= "k$scenario";
						$ww['onover']	= "$condition${scenario}b";
						$ww['ons']		= "$condition${scenario}c";
						break;

					case 't':
					case 'r':
						$ww['init']		= "b$scenario";
						$ww['onover']	= "$condition$scenario";
						$ww["on$condition"] = "c$scenario";
						break;

					case 'ri':
						$ww['init']		= "b$scenario";
						$ww['onover']	= "ri$scenario";
						$ww['on4']		= "c$scenario";
						break;

					case 'ac':
						$ww['init']		= "c$scenario";
						$ww['onover']	= "h$scenario";
						break;

					case 'acd':
					case 'acri':
						$ww['init']		= "b$scenario";
						$ww['onover']	= "$condition$scenario";
						$ww['on4']		= "h$scenario";
						break;
				}

				switch ($condition)
				{
					case 'cir':		$ww['height'] = 430; break;
					case 'cis':		$ww['height'] = 285; break;
					case 'noav':	$ww['height'] = 320; break;
					case 't':		$ww['height'] = 300; break;
				}

				// Generation of the "frames". This the coolest part.
				$q->select("t,event,signal as content,xc,yc")->from("runrow")->where("rid='$playid'")->orderby("t,id");
				$events = $q->run();
				$frames = array('');
				$xoff = 0;
				$yoff = 0;
				$lastwx = 50;
				$lastwy = 100;

				for ($i=0; $i<count($events); $i++)
				{
					if ($events[$i]['event']=='C' && $events[$i]['xc']==0 && $events[$i]['yc']==0)
						continue;

					$frame = array();
					$frame['tt'] = $events[$i]['t'];

					// All commands that require coordinates are set here.
					if (in_array($events[$i]['event'], array('vpsize','scpos','M','C','D')))
					{
						$prefix = $events[$i]['event'];
						if ($prefix=='C' || $prefix=='D') $prefix='M';

						$frame[$prefix.'x'] = scale($events[$i]['xc']);
						$frame[$prefix.'y'] = scale($events[$i]['yc']);
					}

					if ($events[$i]['event']=='E' && $events[$i]['content']=='wappear')
					{
						$frame['Dx'] = scale($events[$i]['xc']);
						$frame['Dy'] = scale($events[$i]['yc']);
					}


					// Important! This is the activation of the mouse over option 2.
					if ($events[$i]['event']=='M' && isWithin($events[$i]['xc'], $events[$i]['yc'], $opt2x+$lastwx, $opt2y+$lastwy, $opt2x+$opt2w+$lastwx, $opt2y+$opt2h+$lastwy) && $showingmouseover==1 && isset($ww['onover']))
					{
						$frame['changeWTo'] = 'images/wrap/cond-'.$ww['onover'].'.png';
						$frame['w'] = scale($ww['width']);
						$frame['h'] = scale($ww['height']);

						if (isset($ww['on4']))
							$frames[] = array('tt' => $events[$i]['t']+4000, 'changeWTo' => 'images/wrap/cond-'.$ww['on4'].'.png', 'w' => scale($ww['width']), 'h' => scale($ww['height']));

						$frame['msg'] = 'Mouse entered option 2.';
						$showingmouseover = 2;
					}

					// Dragging has a lof of complexity.
					if ($events[$i]['event']=='D')
					{
						$lastwx = $events[$i]['xc'] + $xoff;
						$lastwy = $events[$i]['yc'] + $yoff;
						$frame['Dx'] = scale($lastwx);
						$frame['Dy'] = scale($lastwy);
					}

					// Click event
					if ($events[$i]['event']=='C')
					{
						switch ($events[$i]['content'])
						{
							case 'wtkfilter':
								// The mechanism of detection of the mouse over option 2 is not so good. So, if somebody clicks on option 2, obviously hovered the option first.
								if ($showingmouseover==1 && isset($ww['onover']))
								{
									$frame['changeWTo'] = 'images/wrap/cond-'.$ww['onover'].'.png';
									$frame['w'] = scale($ww['width']);
									$frame['h'] = scale($ww['height']);

									if (isset($ww['on4']))
										$frames[] = array('tt' => $events[$i]['t']+4000, 'changeWTo' => 'images/wrap/cond-'.$ww['on4'].'.png', 'w' => scale($ww['width']), 'h' => scale($ww['height']));

									$frame['msg'] = 'Mouse entered option 2.';
									$showingmouseover = 2;
								}
								if (!isset($frame['msg']))
									$frame['msg'] = '';

								$frame['msg'] .= ' Option 2 clicked (disabled)';
								$frame['click'] = 1;
								break;

							case 'wopt2_ok':
								$frame['msg'] = 'OK button within request pop-up clicked.';
								$frame['click'] = 1;
								break;

							default:
								$frame['msg'] = 'Unspecified click ('.$events[$i]['content'].')';
								$frame['click'] = 1;
								break;

							case 'wopt1t':
							case 'wopt1e':
							case 'wopt2t':
							case 'wopt2e':
								$frame['click'] = 1;
								break;

							case 'ignore':
							case 'wmaintext':
								break;

							case 'wtop':
								$xoff = $lastwx - $events[$i]['xc'];
								$yoff = $lastwy - $events[$i]['yc'];
								$frame['msg'] = 'Dragging.';
								break;
						}
					}

					// Messages and events
					if ($events[$i]['event']=='E')
						switch ($events[$i]['content'])
						{
							case 'wappear':
								$frame['msg'] = 'Dialog appears.';
								$frame['display'] = 'wappear';
								//$frame['wposx'] = scale($lastwx);
								//$frame['wposy'] = scale($lastwy);
								$showingmouseover = 1;
								if ($events[$i]['t']>10000)
									$frames[] = array('tt' => $events[$i]['t']-8000, 'msg' => 'Tab browser activated.');
								break;

							case 'wdisappear':
								$frame['msg'] = 'Dialog disappears.';
								$frame['display'] = 'wdisappear';
								$showingmouseover = 0;
								break;

							case 'beginblink':
								$frame['msg'] = 'Blinking starts.';
								$frame['blink'] = 1;
								break;

							case 'endblink':
								$frame['msg'] = 'Blinking stops.';
								break;

							case 'opt1':
								$frame['msg'] = 'Option 1 clicked.';
								break;

							case 'opt2':
								$frame['msg'] = 'Option 2 clicked.';
								break;

							case 'close':
								$frame['msg'] = 'Close button clicked.';
								break;

							default:
								$frame['msg'] = "Unknown event: '".$events[$i]['content']."'";
								break;
						}

					// Finally, we add the frame to the set of frames and go to the next.
					$frames[] = $frame;
				}

				#> Finally, we create the last objects that'll go into screen, and generate the content into the screen template.
				$ww['height'] = scale($ww['height']);
				$ww['width'] = scale($ww['width']);
				$screen->assign('ww', $ww);
				#> We create the selector for scale
				$scloptions = array();
				for ($i=0.5; $i<=1.0; $i+=0.1)
				{
					$i=round($i,1);
					$scloptions["$i"] = "".($i*100)."%";
				}
				$screen->assign('selector', createComboBox('scl', $scloptions, $scl));

				$animate = new Template('animate.tpl');
				$animate->assign('frames', $frames);

				$c->common->addContent($screen->generate());
				$c->common->addContent($animate->generate());
*/
				// We set the trackid for persistence.
				$c->common->setFormVar('trackid', $trackid);
				break;
		}
		$c->display();
	}
	catch (GenException $e) { $e->display(); exit($e->getCode()); }
?>
