<?php
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
function Email_Send($Template,$UserID,$Replace = Array(),$FromID = 100){
  /****************************************************************************/
  $__args_types = Array('string','integer','array','integer');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $User = DB_Select('Users',Array('ID','Name','Sign','ICQ','Email','Mobile','UniqID','IsNotifies'),Array('UNIQ','ID'=>$UserID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($User)){
    case 'error':
      return ERROR | @Trigger_Error('[Email_Send]: не удалось выбрать получателя');
    case 'exception':
      return new gException('EMAIL_RECIPIENT_NOT_FOUND','Получатель письма не найден');
    case 'array':
      #-------------------------------------------------------------------------
      if(!$User['IsNotifies'])
        return new gException('NOTIFIES_RECIPIENT_DISABLED','Уведомления для получателя отключены');
      #-------------------------------------------------------------------------
      if(!$User['Email'])
        return new gException('RECIPIENT_EMAIL_ADDRESS_NOT_FILLED','Получатель не заполнил электронный адрес');
      #-------------------------------------------------------------------------
      $Replace['User'] = $User;
      #-------------------------------------------------------------------------
      $From = DB_Select('Users',Array('ID','Name','Sign','ICQ','Email','Mobile','UniqID'),Array('UNIQ','ID'=>$FromID));
      #-------------------------------------------------------------------------
      switch(ValueOf($From)){
        case 'error':
          return ERROR | @Trigger_Error('[Email_Send]: не удалось выбрать отправителя');
        case 'exception':
          return new gException('EMAIL_SENDER_NOT_FOUND','Отправитель не найден');
        case 'array':
          #---------------------------------------------------------------------
          $Replace['From'] = $From;
          #---------------------------------------------------------------------
          $Path = SPrintF('Notifies/Email/%s',$Template);
          #---------------------------------------------------------------------
          if(Is_Error(System_Element(SPrintF('comp/%s.comp.php',$Path))))
            return new gException('TEMPLATE_EMAIL_LETTER_NOT_FOUND','Шаблон сообщения не найден');
          #---------------------------------------------------------------------
          $Comp = Comp_Load($Path,$Replace);
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error('[Email_Send]: ошибка загрузки шаблона письма');
          #---------------------------------------------------------------------
          $smarty=$GLOBALS['smarty'];
          $smarty->assign('Params', $Replace);
          #---------------------------------------------------------------------
          try {
          $Comp['Message'] = $smarty->fetch('Notifies/Email/UserRegister.tpl');
          }
          catch(Exception $e){
              return new gException('TEMPLATE_ERROR','TEMPLATE_ERROR');
          }
          #---------------------------------------------------------------------
          if(!Is_Array($Comp))
            return new gException('TEMPLATE_DISABLE','Шаблон не активен');
          #---------------------------------------------------------------------
          $Heads = Array(SPrintF('From: %s',$From['Email']),'MIME-Version: 1.0','Content-Type: text/plain; charset=UTF-8','Content-Transfer-Encoding: 8bit');
          #---------------------------------------------------------------------
          if(IsSet($Comp['Heads']))
            $Heads = Array_Merge($Heads,$Comp['Heads']);
          #---------------------------------------------------------------------
          $IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$User['ID'],'TypeID'=>'Email','Params'=>Array($User['Email'],$Comp['Theme'],$Comp['Message'],Implode("\r\n",$Heads),$User['ID'])));
          #---------------------------------------------------------------------
          switch(ValueOf($IsAdd)){
            case 'error':
              return ERROR | @Trigger_Error('[Email_Send]: не удалось установить задание в очередь');
            case 'exception':
              return ERROR | @Trigger_Error('[Email_Send]: не удалось установить задание');
            case 'array':
              return TRUE;
            default:
              return ERROR | @Trigger_Error(101);
          }
        default:
          return ERROR | @Trigger_Error(101);
      }
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
?>
