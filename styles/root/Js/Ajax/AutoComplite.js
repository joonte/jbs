//------------------------------------------------------------------------------
/** @author Бреславский А.В. (Joonte Ltd.) */
//------------------------------------------------------------------------------
// Целевой объект
var $AutoCompliteTarget = null;
// Ресурс HTTP соединения
var $AutoCompliteHTTP = new HTTP();
// Объект положения
var $AutoCompliteEvent = {clientX:null,clientY:null};
//------------------------------------------------------------------------------
if($AutoCompliteHTTP.IsCreated()){
  //----------------------------------------------------------------------------
  $AutoCompliteHTTP.onAnswer = function($Answer){
    //--------------------------------------------------------------------------
    var $AutoCompliteSelect = document.getElementById('AutoCompliteSelect');
    //--------------------------------------------------------------------------
    var $Options = $AutoCompliteSelect.options;
    //--------------------------------------------------------------------------
    while($Options.length != 0)
      $AutoCompliteSelect.remove($Options.length - 1);
    //--------------------------------------------------------------------------
    var $AutoComplite = document.getElementById('AutoComplite')
    //--------------------------------------------------------------------------
    switch($Answer.Status){
      case 'Error':
        //------>;
      break;
      case 'Exception':
        $AutoComplite.style.display = 'none';
      break;
      case 'Ok':
        //----------------------------------------------------------------------
        for(var i in $Answer.Options){
          //--------------------------------------------------------------------
          var $Option = $Answer.Options[i];
          //--------------------------------------------------------------------
          var $Adding = document.createElement('OPTION');
          $Adding.value = $Option.Value;
          $Adding.innerHTML = $Option.Label;
          //--------------------------------------------------------------------
          $AutoCompliteSelect.appendChild($Adding);
        }
        //----------------------------------------------------------------------
        $AutoCompliteSelect.selectedIndex = -1;
        //----------------------------------------------------------------------
        with($AutoComplite.style){
          //--------------------------------------------------------------------
          var $Body = document.body;
          //--------------------------------------------------------------------
          var $OffsetX = $Body.clientWidth - ($AutoCompliteEvent.clientX + $AutoComplite.offsetWidth);
          var $OffsetY = $Body.clientHeight - ($AutoCompliteEvent.clientY + $AutoComplite.offsetHeight);
          //--------------------------------------------------------------------
          left = $Body.scrollLeft + $AutoCompliteEvent.clientX + ($OffsetX <0?$OffsetX:0);
          top  = $Body.scrollTop + $AutoCompliteEvent.clientY + ($OffsetY <0?$OffsetY - 20:0) + 20;
          //--------------------------------------------------------------------
          $AutoComplite.style.display = 'block';
        }
      break;
      default:
        alert('Не известный ответ');
    }
  }
}
//------------------------------------------------------------------------------
function AutoComplite($Target,$event,$URL,$Function){
  //----------------------------------------------------------------------------
  $AutoCompliteEvent = {clientX:$event.clientX,clientY:$event.clientY};
  //----------------------------------------------------------------------------
  document.getElementById('AutoComplite').style.zIndex = GetMaxZIndex() + 1;
  //----------------------------------------------------------------------------
  var $AutoCompliteSelect = document.getElementById('AutoCompliteSelect');
  //----------------------------------------------------------------------------
  $AutoCompliteSelect.onchange = function(){
    //--------------------------------------------------------------------------
    var $AutoCompliteSelect = document.getElementById('AutoCompliteSelect');
    //--------------------------------------------------------------------------
    var $Option = $AutoCompliteSelect.options[$AutoCompliteSelect.selectedIndex];
    //--------------------------------------------------------------------------
    $AutoCompliteTarget.value = $Option.value;
    //--------------------------------------------------------------------------
    if($Function)
      $Function($Option.text,$Option.value);
    //--------------------------------------------------------------------------
    document.getElementById('AutoComplite').style.display = 'none';
  };
  //----------------------------------------------------------------------------
  $AutoCompliteSelect.onmouseover = function(){
    //--------------------------------------------------------------------------
    this.AutoCompliteOnBlur = $AutoCompliteTarget.onblur;
    $AutoCompliteTarget.onblur = null;
  };
  //----------------------------------------------------------------------------
  $AutoCompliteSelect.onmouseout = function(){
    //--------------------------------------------------------------------------
    $AutoCompliteTarget.onblur = this.AutoCompliteOnBlur;
  };
  //----------------------------------------------------------------------------
  $Target.onkeyup = function(){
    //--------------------------------------------------------------------------
    var $AutoComplite = document.getElementById('AutoComplite');
    //--------------------------------------------------------------------------
    var $Search = $AutoCompliteTarget.value;
    //--------------------------------------------------------------------------
    if($Search.length > 3){
      //------------------------------------------------------------------------
      $AutoCompliteHTTP.Stack = [];
      //------------------------------------------------------------------------
      var $Args = {Search:$Search};
      //------------------------------------------------------------------------
      if(!$AutoCompliteHTTP.Send($URL,$Args)){
        //----------------------------------------------------------------------
        alert('Не удалось отправить запрос на сервер');
        //----------------------------------------------------------------------
        return false;
      }
    }else
      $AutoComplite.style.display = 'none';
  }
  //----------------------------------------------------------------------------
  $Target.onblur = function(){
    //--------------------------------------------------------------------------
    document.getElementById('AutoComplite').style.display = 'none';
  }
  //----------------------------------------------------------------------------
  $AutoCompliteTarget = $Target;
}