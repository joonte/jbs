//------------------------------------------------------------------------------
/** @author Бреславский А.В. (Joonte Ltd.) */
//------------------------------------------------------------------------------
// Интервал отображения подсказки
var $PromptIntervalID = null;
// Находиться ли мышка над объектом подсказки
var $PromptIsMouseOver = false;
//------------------------------------------------------------------------------
function PromptApperance(){
	//------------------------------------------------------------------------------
	if($PromptIsMouseOver){
		//------------------------------------------------------------------------------
		var $Prompt = document.getElementById('Prompt');
		//------------------------------------------------------------------------------
		if($Prompt){
			//------------------------------------------------------------------------------
			$Prompt.style.display = 'block';
			//------------------------------------------------------------------------------
			FadeIn($Prompt,100);
			//------------------------------------------------------------------------------
		}
		//------------------------------------------------------------------------------
	}
	//------------------------------------------------------------------------------
	//------------------------------------------------------------------------------
	window.clearInterval($PromptIntervalID);
	//------------------------------------------------------------------------------
}

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function PromptShow($event,$Text,$Object){
	//------------------------------------------------------------------------------
	$Object.onmouseout = PromptHide;
	//------------------------------------------------------------------------------
	var $Prompt = document.getElementById('Prompt');
	//------------------------------------------------------------------------------
	if(!$Prompt){
		//------------------------------------------------------------------------------
		var $Prompt = document.createElement('DIV');
		//------------------------------------------------------------------------------
		$Prompt.id = 'Prompt';
		//------------------------------------------------------------------------------
		$Prompt.style.position = 'absolute';
		//------------------------------------------------------------------------------
		$Prompt.style.display = 'none';
		//------------------------------------------------------------------------------
		document.body.appendChild($Prompt);
		//------------------------------------------------------------------------------
		var $Prompt = document.getElementById('Prompt');
		//------------------------------------------------------------------------------
	}
	//------------------------------------------------------------------------------
	//------------------------------------------------------------------------------
	with($Prompt.style){
		//------------------------------------------------------------------------------
		zIndex =  GetMaxZIndex() + 1;
		//------------------------------------------------------------------------------
		var $Body = document.body;
		//------------------------------------------------------------------------------
		var $OffsetX = $Body.clientWidth - ($event.clientX + $Prompt.offsetWidth);
		var $OffsetY = $Body.clientHeight - ($event.clientY + $Prompt.offsetHeight);
		//------------------------------------------------------------------------------
		// по какой-то причине ширина Prompt.offsetWidth не определяется. ноль всегда.
		left = $Body.scrollLeft + $event.clientX + (($OffsetX <= 0)?$OffsetX:-$Prompt.offsetWidth);
		// кастыль, т.к. ширина не определяется
		if(($Body.clientWidth - $event.clientX) < 150)
			left = $Body.scrollLeft + $event.clientX + (($OffsetX <= 0)?$OffsetX:-$Prompt.offsetWidth) - 50;
		//------------------------------------------------------------------------------
		top  = $Body.scrollTop + $event.clientY + ($OffsetY <0?$OffsetY - 20:0) + 20;
		//------------------------------------------------------------------------------
	}
	//------------------------------------------------------------------------------
	//------------------------------------------------------------------------------
	with($Prompt){
		//------------------------------------------------------------------------------
		innerHTML = $Text;
		//------------------------------------------------------------------------------
		if($PromptIntervalID)
			window.clearInterval($PromptIntervalID);
		//------------------------------------------------------------------------------
		$PromptIntervalID = window.setInterval("if(typeof(PromptApperance) != 'undefined') PromptApperance();",200);
		//------------------------------------------------------------------------------
	}
	//------------------------------------------------------------------------------
	//------------------------------------------------------------------------------
	$PromptIsMouseOver = true;
	//------------------------------------------------------------------------------
	$Prompt.setAttribute('OnClick','PromptHide();');
	//------------------------------------------------------------------------------
}

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function PromptHide(){
	//------------------------------------------------------------------------------
	document.getElementById('Prompt').style.display = 'none';
	//------------------------------------------------------------------------------
	$PromptIsMouseOver = false;
	//------------------------------------------------------------------------------
}
