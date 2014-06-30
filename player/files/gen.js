/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Form functions.
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
function set(key,val)
{ eval("document.forms[0]."+key+".value='"+escape(val)+"'"); }

function doThis(action)
{
	set('mode',action);
	document.forms[0].submit();
}

function setDo(key,val)
{
	set(key,val);
	document.forms[0].submit();
}

function show() { doThis('show'); }
function setDoShow(key,val) { setDo(key,val); show(); }

function load(url)
{ window.location.assign(url); }

function doConfirm(q,action)
{ if (confirm(q)) doThis(action); }

function toggle(obj)
{ obj.checked=!obj.checked; }

function packvalues(header,total)
{
	console.info('packvalues('+header+','+total+')');
	lst = new Array();
	for (i=1; i<=total; i++)
	{
		fld = eval('document.forms[0].'+header+'_ch'+i+'.value');
		chk = eval('document.forms[0].'+header+'_ch'+i+'.checked');
		if (chk) lst.push(fld);
	}
	set(header+'_pack',lst.join('##'));
}
