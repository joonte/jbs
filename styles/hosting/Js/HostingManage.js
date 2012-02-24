//------------------------------------------------------------------------------
/** @author Бреславский А.В. (Joonte Ltd.) */
//------------------------------------------------------------------------------
function HostingManage($HostingOrderID){
  //----------------------------------------------------------------------------
  var $HTTP = new HTTP();
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
        var $Width  = screen.width  - 100;
        var $Height = screen.height - 100;
        //----------------------------------------------------------------------
        var $Window = window.open('','HostingManage',SPrintF('left=%u,top=%u,width=%u,height=%u,toolbar=0, scrollbars=1',(screen.width-$Width)/2,(screen.height-$Height)/2,$Width,$Height));
        //----------------------------------------------------------------------
        $HTML = '<HTML><HEAD><LINK href="/styles/root/Css/Standard.css" rel="stylesheet" type="text/css" /><TITLE>Управление заказом хостинга</TITLE></HEAD>';
        $HTML += '<BODY><P style="font-size:12px;">Осуществляется вход. Пожалуйста, подождите...</P>';
        //----------------------------------------------------------------------
        $HTML += SPrintF('<FORM id="HostingManage" method="POST" action="%s">',$Answer.Url);
        //----------------------------------------------------------------------
        var $Args = $Answer.Args;
        //----------------------------------------------------------------------
        for(var $i in $Args){
          //--------------------------------------------------------------------
          var $Arg = $Args[$i];
          //--------------------------------------------------------------------
          if(typeof $Arg != 'string')
           continue;
          //--------------------------------------------------------------------
          $HTML += SPrintF('<INPUT type="hidden" name="%s" value="%s" />',$i,$Arg);
        }
        //----------------------------------------------------------------------
        $HTML += '</FORM>';
        $HTML += '<SCRIPT>document.getElementById("HostingManage").submit();</SCRIPT></BODY></HTML>';
        //----------------------------------------------------------------------
        $Window.document.write($HTML);
      break;
      default:
        alert('Не известный ответ');
    }
  };
  //----------------------------------------------------------------------------
  var $Args = {HostingOrderID:$HostingOrderID};
  //----------------------------------------------------------------------------
  if(!$HTTP.Send('/API/HostingManage',$Args)){
    //--------------------------------------------------------------------------
    alert('Не удалось отправить запрос на сервер');
    //--------------------------------------------------------------------------
    return false;
  }
  //----------------------------------------------------------------------------
  ShowProgress('Вход на сервер');
}