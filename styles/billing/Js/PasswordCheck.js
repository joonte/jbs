//------------------------------------------------------------------------------
/** @author Бреславский А.В. (Joonte Ltd.) */
//------------------------------------------------------------------------------
function PasswordCheck($Form,$InputName){
  //----------------------------------------------------------------------------
  if($Form[$InputName].value != $Form['_'+$InputName].value){
    //--------------------------------------------------------------------------
    ShowAlert('Введенные Вами пароли не совпадают. Пожалуйста, подтвердите пароль еще раз.','Warning');
    //--------------------------------------------------------------------------
    $Form['_'+$InputName].focus();
    //--------------------------------------------------------------------------
    return false;
  }
  //----------------------------------------------------------------------------
  return true;
}