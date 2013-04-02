//------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
//------------------------------------------------------------------------------
function selectStars(e, $ticketId, $starId) {
  for(var $i = 0; $i < 5; $i++) {
    document.getElementById('star_'+$ticketId+'_'+$i).src = '/styles/billing/Images/Icons/DisableStar.png';
  }

  for(var $i = 0; $i <= $starId; $i++) {
    document.getElementById('star_'+$ticketId+'_'+$i).src = '/styles/billing/Images/Icons/EnableStar.png';
  }
}
//------------------------------------------------------------------------------
function quote($message) {
  //----------------------------------------------------------------------------
  var $ticketsWin = document.getElementById('TicketReadMessages').contentWindow;
  var $ticketsDoc = $ticketsWin.document;

  var $selection = '';

  if ($ticketsWin.getSelection) {
    $selection = $ticketsWin.getSelection();
  }
  else if ($ticketsDoc.getSelection) {
    $selection = $ticketsDoc.getSelection();
  }
  else if ($ticketsDoc.selection) {
    $selection = $ticketsDoc.selection.createRange().text;
  }
  else {
    return;
  }

  if ($message.createTextRange && $message.caretPos) {
    var $caretPos = $message.caretPos;
    $caretPos.text = '[quote]' + $selection + '[/quote]';
  }
  else if (!isNaN($message.selectionStart)) {
    //--------------------------------------------------------------------------
    $startText = ($message.value).substring(0, $message.selectionStart);
    $selectedText = ($message.value).substring($message.selectionStart,
        $message.selectionEnd);
    $endText = ($message.value).substring($message.selectionEnd, $message.value.length);

    $message.value = $startText + '[quote]' + $selection + '[/quote]' + $selectedText + $endText;
  }
  else {
    $message.value += '[quote]' + $selection + '[/quote]';
  }

  $message.focus();
}
//------------------------------------------------------------------------------
function ctrlEnterEvent(e) {
  if (e.ctrlKey && (e.keyCode == 10 || e.keyCode == 13)) {
    TicketAddMessage();
  }
}
//------------------------------------------------------------------------------
function TicketAddMessage(){
  //----------------------------------------------------------------------------
  var $Form = document.forms['TicketReadForm'];
  //----------------------------------------------------------------------------
  $HTTP = new HTTP();
  //----------------------------------------------------------------------------
  if(!$HTTP.Resource){
    //--------------------------------------------------------------------------
    alert('Не удалось создать HTTP соединение');
    //--------------------------------------------------------------------------
    return false;
  }
  //----------------------------------------------------------------------------
  $HTTP.onLoaded = function(){
    //--------------------------------------------------------------------------
    HideProgress();
  }
  //----------------------------------------------------------------------------
  $HTTP.onAnswer = function($Answer){
    //--------------------------------------------------------------------------
    switch($Answer.Status){
      case 'Error':
        ShowAlert($Answer.Error.String,'Warning');
      break;
      case 'Exception':
        ShowAlert(ExceptionsStack($Answer.Exception),'Warning');
      break;
      case 'Ok':
        //----------------------------------------------------------------------
        var $Form = document.forms['TicketReadForm'];
        //----------------------------------------------------------------------
        if($Form.IsNext){
          //--------------------------------------------------------------------
          if($Form.IsNext.checked){
            //------------------------------------------------------------------
            ShowWindow('/TicketRead',{TicketID:$Form.IsNext.value});
            //------------------------------------------------------------------
            break;
          }
        }
        //----------------------------------------------------------------------
	if($Form.Flags.selectedIndex){
		var Selected = $Form.Flags.selectedIndex;
		var SelectedOption = $Form.Flags.options[Selected].value;
		if(SelectedOption != "No"){
			var close = "yes";
		}else{
			var close = "no";
		}
	}else{
		if($Form.Flags.checked){
			var close = "yes";
		}else{
			var close = "no";
		}
	}
        if(close == "yes"){
          GetURL(document.location);
        }else{
          //--------------------------------------------------------------------
          document.forms['TicketReadForm'].Message.value = '';
          //--------------------------------------------------------------------
          UploadDelete('TicketReadForm','TicketMessageFile');
          //--------------------------------------------------------------------
          document.getElementById('TicketReadMessages').contentWindow.document.location.reload();
        }
      break;
      default:
        alert('Не известный ответ');
    }
  }
  //----------------------------------------------------------------------------
  var $Args = FormGet($Form);
  //----------------------------------------------------------------------------
  if(!$HTTP.Send('/API/TicketMessageEdit',$Args)){
    //--------------------------------------------------------------------------
    alert('Не удалось отправить запрос на сервер');
    //--------------------------------------------------------------------------
    return false;
  }
  //----------------------------------------------------------------------------
  ShowProgress('Добавление сообщения в запрос');
}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
// функция вставки текста на текущую позицию курсора - свистнул откуда то, встраивается в jquery
$.fn.extend({
	insertAtCaret: function(myValue){
		var obj;
		if( typeof this[0].name !='undefined' ) obj = this[0];
		else obj = this;
		if ($.browser.msie) {
			obj.focus();
			sel = document.selection.createRange();
			sel.text = myValue;
			obj.focus();
		}
		else if ($.browser.mozilla || $.browser.webkit) {
			var startPos = obj.selectionStart;
			var endPos = obj.selectionEnd;
			var scrollTop = obj.scrollTop;
			obj.value = obj.value.substring(0, startPos)+myValue+obj.value.substring(endPos,obj.value.length);
			obj.focus();
			obj.selectionStart = startPos + myValue.length;
			obj.selectionEnd = startPos + myValue.length;
			obj.scrollTop = scrollTop;
		}else{
			obj.value += myValue;
			obj.focus();
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



