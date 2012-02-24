/** @author Бреславский А.В. (Joonte Ltd.) */
//------------------------------------------------------------------------------
function SettingsUpdate(){
  //----------------------------------------------------------------------------
  var $Form = document.forms['ISPswGroupEditForm'];
  //----------------------------------------------------------------------------
  var $System = Settings[$Form.SystemID.value];
  //----------------------------------------------------------------------------
  for(var i in $System)
    $Form[i].value = $System[i];
}
//------------------------------------------------------------------------------
function ISPswGroupEdit(){
  //----------------------------------------------------------------------------
  var $Form = document.forms['ISPswGroupEditForm'];
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
        GetURL(document.location);
      break;
      default:
        alert('Не известный ответ');
    }
  }
  //----------------------------------------------------------------------------
  var $Args = FormGet($Form);
  //----------------------------------------------------------------------------
  if(!$HTTP.Send('/Administrator/API/ISPswGroupEdit',$Args)){
    //--------------------------------------------------------------------------
    alert('Не удалось отправить запрос на сервер');
    //--------------------------------------------------------------------------
    return false;
  }
  //----------------------------------------------------------------------------
  ShowProgress($Form.ISPswGroupID?'Редактирование сервера':'Добавление сервера');
}
