<?php
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
function Array_Union(&$What,$Whom,$IsReplace = TRUE){
  /****************************************************************************/
  $__args_types = Array('array','array','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args();
//Die(FUNCTION_INIT);
Eval(FUNCTION_INIT);
//echo Base64_Decode('JF9fYXJnc19fID0gRnVuY19HZXRfQXJncygpOw0KaWYoSXNTZXQoJF9fYXJnc190eXBlcykpew0KICBmb3IoJGk9MDskaTxDb3VudCgkX19hcmdzX18pOyRpKyspew0KICAgICRfX2FyZ190eXBlID0gKCRpIDwgQ291bnQoJF9fYXJnc190eXBlcyk/JF9fYXJnc190eXBlc1skaV06JF9fYXJnc190eXBlc1tDb3VudCgkX19hcmdzX3R5cGVzKS0xXSk7DQogICAgaWYoJF9fYXJnX3R5cGUgPT0gJyonKQ0KICAgICAgY29udGludWU7DQogICAgJF9fdHlwZSA9IEdldFR5cGUoJF9fYXJnc19fWyRpXSk7DQogICAgaWYoIUluX0FycmF5KCRfX3R5cGUsRXhwbG9kZSgnLCcsJF9fYXJnX3R5cGUpKSl7DQogICAgICBEZWJ1ZyhQcmludF9SKCRfX2FyZ3NfXyxUUlVFKSk7DQogICAgICBUcmlnZ2VyX0Vycm9yKFNQcmludEYoJ1tGVU5DVElPTl9JTklUXTog0L/QsNGA0LDQvNC10YLRgCAoJXMpINC/0YDQuNC90Y/RgiAoJXMpINC+0LbQuNC00LDQu9GB0Y8gKCVzKScsJGksJF9fdHlwZSwkX19hcmdfdHlwZSkpOw0KICAgIH0NCiAgfQ0KfQ==');



//echo Base64_Decode('aWYoSXNTZXQoJF9fYXJnc190eXBlcykpeyBmb3IoJGk9MDskaTwgQ291bnQoJF9fYXJnc190eXBlcyk/JF9fYXJnc190eXBlc1skaV06JF9fYXJnc190eXBlc1tDb3VudCgkX19hcmdzX3R5cGVzKS0xXSk7IGlmKCRfX2FyZ190eXBlID09ICcqJykgY29udGludWU7ICRfX3R5cGUgPSBHZXRUeXBlKCRfX2FyZ3NfX1skaV0pOyBpZighSW5fQXJyYXkoJF9fdHlwZSxFeHBsb2RlKCcsJywkX19hcmdfdHlwZSkpKXsgRGVidWcoUHJpbnRfUigkX19hcmdzX18sVFJVRSkpOyBUcmlnZ2VyX0Vycm9yKFNQcmludEYoJ1tGVU5DVElPTl9JTklUXTog0L/QsNGA0LDQvNC10YLRgCAoJXMpINC/0YDQuNC90Y/RgiAoJXMpINC+0LbQuNC00LDQu9GB0Y8gKCVzKScsJGksJF9fdHlwZSwkX19hcmdfdHlwZSkpOyB9IH0gfQ==');
//Die();
  /****************************************************************************/
  foreach(Array_Keys($Whom) as $Key){
    #---------------------------------------------------------------------------
    if(!IsSet($What[$Key]))
      $What[$Key] = $Whom[$Key];
    else{
      #-------------------------------------------------------------------------
      $Element1 = &$What[$Key];
      $Element2 = &$Whom[$Key];
      #-------------------------------------------------------------------------
      if(Is_Array($Element1) && Is_Array($Element2))
        Array_Union($Element1,$Element2);
      else{
        #-----------------------------------------------------------------------
        if($IsReplace)
          $What[$Key] = $Whom[$Key];
      }
    }
  }
}
#-------------------------------------------------------------------------------
function Array_Cut(&$What,$Whom,$IsFull = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','array','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  foreach(Array_Keys($Whom) as $Key){
    #---------------------------------------------------------------------------
    if(IsSet($What[$Key])){
      #-------------------------------------------------------------------------
      $ElementA = &$What[$Key];
      $ElementB =  $Whom[$Key];
      #-------------------------------------------------------------------------
      if(Is_Array($ElementA) && Is_Array($ElementB)){
        #-----------------------------------------------------------------------
        Array_Cut($ElementA,$ElementB,$IsFull);
        #-----------------------------------------------------------------------
        if($IsFull && !Count($ElementA))
          UnSet($What[$Key]);
      }else
        UnSet($What[$Key]);
    }
  }
}
#-------------------------------------------------------------------------------
function Is_Keys_Exists($Array,$Key){
  /****************************************************************************/
  $__args_types = Array('array','string,int');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  Array_Shift($__args__);
  #-----------------------------------------------------------------------------
  foreach($__args__ as $__arg__){
    #---------------------------------------------------------------------------
    if(!IsSet($Array[$__arg__]))
      return FALSE;
  }
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function Array_ToLine($Array,$Border = '',$Path = '',&$Result = Array()){
  /****************************************************************************/
  $__args_types = Array('array','string','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  foreach(Array_Keys($Array) as $Key){
    #---------------------------------------------------------------------------
    $Name = ($Path?SPrintF('%s.%s',$Path,$Key):$Key);
    #---------------------------------------------------------------------------
    if(Is_Array($Child = $Array[$Key])){
      #-------------------------------------------------------------------------
      if(Is_Error(Array_ToLine($Child,$Border,$Name,$Result)))
        return ERROR | Trigger_Error('[Array_ToLine]: ошибка рекурсивного вызова');
    }else
      #-------------------------------------------------------------------------
      $Result[$Border.$Name.$Border] = $Child;
  }
  #-----------------------------------------------------------------------------
  return $Result;
}
//****************************************************************************//
?>