/** @author Бреславский А.В. (Joonte Ltd.) */
//------------------------------------------------------------------------------
function DisabledUpdate(){
  //----------------------------------------------------------------------------
  var $Form = document.forms['ServiceFieldEditForm'];
  //----------------------------------------------------------------------------
  for(var i in $Form){
    //--------------------------------------------------------------------------
    var $Field = $Form[i];
    //--------------------------------------------------------------------------
    if($Field != null && $Field.disabled)
      $Field.disabled = false;
  }
  //----------------------------------------------------------------------------
  var $Disabled = Disabled[$Form.TypeID.value];
  //----------------------------------------------------------------------------
  for(var i in $Disabled)
    $Form[$Disabled[i]].disabled = true;
}
//------------------------------------------------------------------------------
function ServiceFieldEdit(){
  //----------------------------------------------------------------------------
  var $Form = document.forms['ServiceFieldEditForm'];
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
        GetURL(document.referrer);
      break;
      default:
        alert('Не известный ответ');
    }
  }
  //----------------------------------------------------------------------------
  var $Args = FormGet($Form);
  //----------------------------------------------------------------------------
  if(!$HTTP.Send('/Administrator/API/ServiceFieldEdit',$Args)){
    //--------------------------------------------------------------------------
    alert('Не удалось отправить запрос на сервер');
    //--------------------------------------------------------------------------
    return false;
  }
  //----------------------------------------------------------------------------
  ShowProgress($Form.ServiceFieldID?'Редактирование поля услугуи':'Добавление поля услуги');
}