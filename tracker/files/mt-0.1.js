/**
 * User variables.
 */
var mt_freq = 50;				// Threshold to log mouse tracking movements, in milliseconds. In other words, 1000/freq is the frequency in hertz.
var mt_detect_resize = false;	// Whether resizes of the main window should be detected or not.

/**
 * System variables.
 */
var mt_pid = null;				// Participant ID.
var mt_posturl = '';			// URL to which the tracking will be posted.
var mt_rprt = new Array();		// Main array wherein all events will be kept.
var mt_rprt_offset = 0;			// Offset of the page loading.
var mt_current_state = null;	// The state of the automaton.
var mt_freq_sem = true;			// Semaphor to control the frequency to poll mouse movements.
var mt_dxc = 0;					// Delayed X Coordinate; used to limit the sensibility of the logging of mouse movements.
var mt_dyc = 0;					// Delayed Y Coordinate.
var mt_mms = 5;					// Mouse movements sensitivity. If the mouse does not move more than mt_mms pixels, it won't get logged.
var mt_silent = false;			// Whether the submitData function should stay silent or not.
var mt_name = '';				// Name of the current 'project'.

function init()
{
	// We obtain the timing of the first load of the page and report it, along with data about the status of the viewport.
	mt_rprt_offset = getInstant();
	report('begin', 'offset=' + mt_rprt_offset + ';vpw=' + $(window).width() + ';vph=' + $(window).height() + ';');
	mt_current_state = 's_start';

	// We set the posturl. It may be put manually, but it's better automatically.
	mt_posturl = $(location).attr('protocol') + '//' + $(location).attr('hostname') + $(location).attr('pathname');
	mt_posturl = mt_posturl.replace('index.html','store.php')

	// Now we get the pid from the URL, or we fail.
	var tmp = $(location).attr('search');
	var tmp2 = tmp.match(/pid=[^&]+/g);
	if (tmp2)
		mt_pid = tmp2[0].substr(4);
	else
		mt_pid = 'anonym';

	// We get the name project from the URL.
	tmp2 = tmp.match(/name=[^&]+/g);
	if (tmp2)
		mt_name = decodeURI(tmp2[0].substr(5));

	// We initialize movement within the viewport.
	$(document).mousemove(function(e) {
		if (mt_freq_sem && (Math.abs(mt_dxc-e.pageX)>mt_mms || Math.abs(mt_dyc-e.pageY)>mt_mms))
		{
			mt_freq_sem = false;
			mt_dxc = e.pageX;
			mt_dyc = e.pageY;
			report('mv', 'x=' + e.pageX + ';y=' + e.pageY + ';');
			setTimeout('mt_freq_sem=true;', mt_freq);
		}
	});

	// If the window is closed, the last thing we do is to attempt to send the data so it's not lost. This does not always
	// work because of browsers having inconsistently implemented this.
	$(window).unload(function() {
		submitData();
	});

	// If we're asked to track the resizing of the main window, we do.
	if (mt_detect_resize)
	{
		$(window).resize(function() {
			report('resize', 'vpw=' + $(window).width() + ';vph=' + $(window).height() + ';');
		});
	}

	// All objects with class 'trackable' will be tracked for clicks, mouseenters and mouseleaves (no mouse movements,
	// that's too much data)
	$('.trackable')
		.click(function(e) {
			report('m-clk', 'id=' + $(this).attr('id') + ';x=' + e.pageX + ';y=' + e.pageY + ';which=' + e.which + ';');
			checkStates($(this).attr('id'));
		})
		.mouseenter(function() {
			report('m-ent', 'id=' + $(this).attr('id') + ';');
		})
		.mouseleave(function() {
			report('m-lev', 'id=' + $(this).attr('id') + ';');
		});
}

function getInstant()
{
	var thistime = new Date();
	thistime -= 0;
	return(thistime);
}

function report(code,content)
{
	mt_rprt.push(''+(getInstant()-mt_rprt_offset)+':'+code+':'+content);
}

function submitData(silent)
{
	mt_silent = silent? true:false;
	if (!mt_silent) console.log('mt.submitData(): Sending...');

	var request = $.ajax({
		url: mt_posturl,
		type: 'POST',
		data: { pid: mt_pid, content: mt_rprt.join('#'), agent: navigator.userAgent, name: mt_name },
		dataType: 'xml',
	});

	request.done(function(xml) {
		if (!mt_silent)
		{
			var errornum = $(xml).find('error').first().text();
			var errormsg = $(xml).find('errormsg').first().text();
			var addition = $(xml).find('additional').first().text();

			if (parseInt(errornum)>0)
				console.log('mt.submitData(): Error ' + errornum + ': ' + errormsg + ' (' + addition + ')');
			else
				console.log('mt.submitData(): Tracking data succesfully stored.');
		}
	});

	request.error(function(xml) {
		if (!mt_silent)
			console.log('mt.submitData(): Network or submission error.');
	});
}

/* The finite-states machine */
function checkStates(signal)
{
	// ---------------------------------------------------------------------------------------------
	if (mt_current_state=='s_start')
	{
		switch (signal)
		{
			case 'one':
				// Some things should occur here.
				mt_current_state = 's_one';
				break;
		}
	}

	// ---------------------------------------------------------------------------------------------
	else if (mt_current_state=='s_one')
	{
		switch (signal)
		{
			case 'two':
				// Some things should occur here.
				mt_current_state = 's_two';
				break;

			default:
				// Some things should occur here.
				mt_current_state = 's_start';
				break;
		}
	}

	// ---------------------------------------------------------------------------------------------
	else if (mt_current_state=='s_two')
	{
		switch (signal)
		{
			case 'three':
				// Some things should occur here. This is only an example.
				submitData();
				mt_current_state = 's_end';
				break;

			default:
				// Some things should occur here.
				mt_current_state = 's_start';
				break;
		}
	}
}