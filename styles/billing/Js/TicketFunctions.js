//------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
//------------------------------------------------------------------------------
function selectStars(e, $ticketId, $starId) {
	//------------------------------------------------------------------------------
	for(var $i = 0; $i < 5; $i++) {
		document.getElementById('star_'+$ticketId+'_'+$i).src = '/styles/billing/Images/Icons/DisableStar.png';
	}
	//------------------------------------------------------------------------------
	for(var $i = 0; $i <= $starId; $i++) {
		document.getElementById('star_'+$ticketId+'_'+$i).src = '/styles/billing/Images/Icons/EnableStar.png';
	}
	//------------------------------------------------------------------------------
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function quote($message) {
	//------------------------------------------------------------------------------
	if(!document.getElementById('TicketReadMessages')){
		$message.value += '[quote][/quote]';
		return;
	}
	//------------------------------------------------------------------------------
	var $ticketsWin = document.getElementById('TicketReadMessages').contentWindow;
	var $ticketsDoc = $ticketsWin.document;

	var $selection = '';

	if ($ticketsWin.getSelection) {
		$selection = $ticketsWin.getSelection();
	}else if ($ticketsDoc.getSelection) {
		$selection = $ticketsDoc.getSelection();
	}else if ($ticketsDoc.selection) {
		$selection = $ticketsDoc.selection.createRange().text;
	} else {
		return;
	}
	//------------------------------------------------------------------------------
	if ($message.createTextRange && $message.caretPos) {
		var $caretPos = $message.caretPos;
		$caretPos.text = '[quote]' + $selection + '[/quote]';
	} else if (!isNaN($message.selectionStart)) {
		//------------------------------------------------------------------------------
		$startText = ($message.value).substring(0, $message.selectionStart);
		$selectedText = ($message.value).substring($message.selectionStart,$message.selectionEnd);
		$endText = ($message.value).substring($message.selectionEnd, $message.value.length);
		$message.value = $startText + '[quote]' + $selection + '[/quote]' + $selectedText + $endText;
		//------------------------------------------------------------------------------
	} else {
		$message.value += '[quote]' + $selection + '[/quote]';
	}
	//------------------------------------------------------------------------------
	$message.focus();
	//------------------------------------------------------------------------------
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
// added by lissyara, 2013-05-26 in 21:25 MSK for JBS-458
function bb_code($Obj,$CodeBegin,$CodeEnd) {
	//------------------------------------------------------------------------------
	if(!$CodeEnd)
		$CodeEnd = $CodeBegin;
	//------------------------------------------------------------------------------
	// from: http://www.gamedev.net/topic/400585-javascript---add-html-tags-around-selection/
	if(typeof $Obj.selectionStart == 'number'){
		//------------------------------------------------------------------------------
		// Mozilla, Opera, and other browsers
		var start = $Obj.selectionStart;
		var end   = $Obj.selectionEnd;
		$Obj.value = $Obj.value.substring(0, start) + '[' + $CodeBegin + ']' + $Obj.value.substring(start, end) + '[/' + $CodeEnd + ']' + $Obj.value.substring(end, $Obj.value.length);
		//------------------------------------------------------------------------------
	}else if(document.selection){
		//------------------------------------------------------------------------------
		// Internet Explorer
		// TODO: проверить работу кнопки в ослике
		// make sure it's the textarea's selection
		$Obj.focus();
		var range = document.selection.createRange();
		if(range.parentElement() != $Obj) return false;
		if(typeof range.text == 'string')
			document.selection.createRange().text = '[' + $CodeBegin + ']' + range.text + '[/' + $CodeEnd + ']';
		//------------------------------------------------------------------------------
	}else{
		$Obj.value += text;
	}																				    	        						//------------------------------------------------------------------------------
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function ctrlEnterEvent(e) {
	if (e.ctrlKey && (e.keyCode == 10 || e.keyCode == 13)) {
		TicketAddMessage();
	}
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
// функция вставки текста на текущую позицию курсора - свистнул откуда то, встраивается в jquery
$.fn.extend({
	insertAtCaret: function(myValue){
		var $Obj;
		if( typeof this[0].name !='undefined' ) $Obj = this[0];
		else $Obj = this;
		if ($.browser.msie) {
			$Obj.focus();
			sel = document.selection.createRange();
			sel.text = myValue;
			$Obj.focus();
		}
		else if ($.browser.mozilla || $.browser.webkit) {
			var startPos = $Obj.selectionStart;
			var endPos = $Obj.selectionEnd;
			var scrollTop = $Obj.scrollTop;
			$Obj.value = $Obj.value.substring(0, startPos)+myValue+$Obj.value.substring(endPos,$Obj.value.length);
			$Obj.focus();
			$Obj.selectionStart = startPos + myValue.length;
			$Obj.selectionEnd = startPos + myValue.length;
			$Obj.scrollTop = scrollTop;
		}else{
			$Obj.value += myValue;
			$Obj.focus();
		}
	}

}) // кончилась функция. 
//------------------------------------------------------------------------------
//дальше пойдет код окошка с запросом
$(document).ready(function(){
	$('#image').click(function(){
		var $UserInput = prompt("Please enter image url","http://");
		if($UserInput){
			$('#Message').insertAtCaret('[image]' + $UserInput + '[/image]');
		}else{
			return false;
		}
	});// click
}); // ready

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
// автоматическое обновления фрейма с сообщениями
function update_messages() {
	//------------------------------------------------------------------------------
	if(document.getElementById('LockPage' + 'Window')){
		//------------------------------------------------------------------------------
		var $Form = document.forms['TicketReadForm'];
		//------------------------------------------------------------------------------
		$.ajax({
			type:		'POST',
			url:		'/TicketMessages',
			data:		{TicketID: $Form.TicketID.value},
			dataType:	"html",
			success:	function(data) {
				//alert("Debug: " + );
				$("iframe").contents().find('#Body').html(data);
			}
		});
		//------------------------------------------------------------------------------
	}
	//------------------------------------------------------------------------------
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$(document).ready(function() {
	setInterval('update_messages()', 15000);
});
//------------------------------------------------------------------------------


