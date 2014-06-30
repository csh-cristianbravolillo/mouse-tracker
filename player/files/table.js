var hlid = 0;

function highlightRow(id,color)
{
	if (hlid>0)
		$('#r'+hlid).css({'backgroundColor':'transparent'});
	$('#r'+id).css({'backgroundColor':color});
	hlid=id;
}
