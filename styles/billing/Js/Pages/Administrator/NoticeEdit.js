//------------------------------------------------------------------------------
/** @author Бреславский А.В. (Joonte Ltd.) */
//------------------------------------------------------------------------------
function NoticeEdit($FormID){
  //----------------------------------------------------------------------------
  var $IsWidget = Boolean($FormID);
  //----------------------------------------------------------------------------
  if(!$IsWidget)
    $FormID = 'NoticeEditForm';
  //----------------------------------------------------------------------------
  $Form = document.forms[$FormID];
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
        if(!$IsWidget)
          HideWindow();
        //----------------------------------------------------------------------
        ShowTick('Заметка успешно сохранена');
      break;
      default:
        alert('Не известный ответ');
    }
  }
  //----------------------------------------------------------------------------
  var $Args = FormGet($Form);
  //----------------------------------------------------------------------------
  if(!$HTTP.Send('/Administrator/API/NoticeEdit',$Args)){
    //--------------------------------------------------------------------------
    alert('Не удалось отправить запрос на сервер');
    //--------------------------------------------------------------------------
    return false;
  }
  //----------------------------------------------------------------------------
  ShowProgress('Изменение заметки');
}