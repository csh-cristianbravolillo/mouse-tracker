
var time = new Number(0);
var maxtime = new Number(0);
var program = new Array();
var pointer = 0;
var lastTimeout = 0;
var lastClock = 0;
var scxy = 0;
var playmode = true;

$(window).load(function () {
	trackReset();
	ng_init();
});

function togglePlay()
{
	if (playmode)
	{
		$("div.action:contains('Play') img").attr('src', 'images/actions/media-playback-pause.png');
		$("div.action:contains('Play') span").first().text('Pause');
		updateClock(50);
		trackRun();
	}
	else
	{
		$("div.action:contains('Pause') img").attr('src', 'images/actions/media-playback-start.png');
		$("div.action:contains('Pause') span").first().text('Play');
		clearTimeout(lastTimeout);
		clearTimeout(lastClock);
	}
	playmode = !playmode;
}

function updateClock(tempo)
{
	// Calculate the time to display
	var mil = time;
	var min = parseInt(mil/60000);
	var sec = parseInt((mil - 60000*min)/1000);
	mil -= min*60000 + sec*1000;

	// We set the clock.
	$('#clock').html(prepend(min,2)+':'+prepend(sec,2)+" "+prepend(mil,3));
	time = time.valueOf() + parseInt(tempo);
	if (time<maxtime)
		lastClock = setTimeout("updateClock('"+tempo+"')",tempo);
}

function prepend(num, dig)
{
	if (dig>3) dig=3;
	if (dig<2) dig=2;
	if (num<10 && dig==3)
		return '00'+num;
	else if ((num<100 && dig==3) || (num<10 && dig==2))
		return '0'+num;
	else
		return num;
}

function trackRun()
{
	if (pointer>=program.length) return;

	var com = program[pointer][0];
	if (pointer<program.length-1)
		com += 'trackRun();';

	lastTimeout = setTimeout(com, program[pointer][1]);
	if (pointer<program.length)
		pointer++;
}

function trackReset()
{
	time=0;
	pointer=0;
	$('#wrapmouse').css({'top':0, 'left':0});
	$('#clock').html('00:00 000');
	$('#inmsg').html('');
	$("div.action:contains('Pause') img").attr('src', 'images/actions/media-playback-start.png');
	$("div.action:contains('Pause') span").first().text('Play');
}
