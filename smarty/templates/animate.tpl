<script type='text/javascript'>
function wrapanimate()
{
	reset();
	var xoff=0, yoff=0;
{foreach $frames as $frame}
{if isset($frame.vpsizex)}
	setTimeout("vp.width='{$frame.vpsizex}px'",{$frame.tt});
{/if}
{if isset($frame.vpsizey)}
	setTimeout("vp.height='{$frame.vpsizey}px'",{$frame.tt});
{/if}
{if isset($frame.scposx)}
	setTimeout("vp.left='{$frame.scposx}px'",{$frame.tt});
{/if}
{if isset($frame.scposy)}
	setTimeout("vp.top='{$frame.scposy}px'",{$frame.tt});
{/if}
{if isset($frame.Mx)}
	setTimeout("mm.left='{$frame.Mx}px'",{$frame.tt});
{/if}
{if isset($frame.My)}
	setTimeout("mm.top='{$frame.My}px'",{$frame.tt});
{/if}
{if isset($frame.Dx)}
	setTimeout("ww.left='{$frame.Dx}px'",{$frame.tt});
{/if}
{if isset($frame.Dy)}
	setTimeout("ww.top='{$frame.Dy}px'",{$frame.tt});
{/if}
{if isset($frame.msg)}
	setTimeout("msg.innerHTML+='["+timeToHuman({$frame.tt})+"]: {$frame.msg}<br/>'",{$frame.tt});
{/if}
{if isset($frame.display)}
	setTimeout("makeWDisplay('{$frame.display}')",{$frame.tt});
{/if}
{if isset($frame.click)}
	setTimeout("makeWClick()",{$frame.tt});
{/if}
{if isset($frame.blink)}
	setTimeout("makeWBlink()",{$frame.tt});
{/if}
{if isset($frame.changeWTo)}
	setTimeout("changeWTo('{$frame.changeWTo}',{$frame.w},{$frame.h})", {$frame.tt});
{/if}
{/foreach}
}
</script>

{*setTimeout("vp.backgroundSize='{$frame.backsize}px'",{$frame.tt});setTimeout("vp.backgroundPosition='{$frame.backleft}px 0px'",{$frame.tt});*}