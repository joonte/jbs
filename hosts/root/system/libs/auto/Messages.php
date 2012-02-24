<?php
#-------------------------------------------------------------------------------
/** @author Бреславский А.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
function &Messages(){
  #-----------------------------------------------------------------------------
  $Name = Md5('Messages');
  #-----------------------------------------------------------------------------
  if(!IsSet($GLOBALS[$Name])){
    #---------------------------------------------------------------------------
    $GLOBALS[$Name] = System_XML('config/Messages.xml');
    if(Is_Error($GLOBALS[$Name]))
      return ERROR | Trigger_Error('[Messages]: не удалось загрузить файл сообщений');
  }
  #-----------------------------------------------------------------------------
  return $GLOBALS[$Name];
}
//****************************************************************************//
?>