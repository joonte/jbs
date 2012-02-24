<?php
#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
#-------------------------------------------------------------------------------
function Jabber_Send($Template,$UserID,$Replace = Array(),$FromID = 100){
  /****************************************************************************/
  $__args_types = Array('string','integer','array','integer');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $User = DB_Select('Users',Array('ID','Name','Sign','JabberID','Email','Mobile','UniqID','IsNotifies'),Array('UNIQ','ID'=>$UserID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($User)){
    case 'error':
      return ERROR | @Trigger_Error('[Jabber_Send]: не удалось выбрать получателя');
    case 'exception':
      return new gException('Jabber_RECIPIENT_NOT_FOUND','Получатель письма не найден');
    case 'array':
      #-------------------------------------------------------------------------
      if(!$User['IsNotifies'])
        return new gException('NOTIFIES_RECIPIENT_DISABLED','Уведомления для получателя отключены');
      #-------------------------------------------------------------------------
      if(!$User['JabberID'])
        return new gException('RECIPIENT_Jabber_UIN_NOT_FILL','Получатель не заполнил UIN службы Jabber');
      #-------------------------------------------------------------------------
      $Replace['User'] = $User;
      #-------------------------------------------------------------------------
      $From = DB_Select('Users',Array('ID','Name','Sign','JabberID','Email','Mobile','UniqID'),Array('UNIQ','ID'=>$FromID));
      #-------------------------------------------------------------------------
      switch(ValueOf($User)){
        case 'error':
          return ERROR | @Trigger_Error('[Jabber_Send]: не удалось выбрать отправителя');
        case 'exception':
          return new gException('Jabber_SENDER_NOT_FOUND','Отправитель не найден');
        case 'array':
          #---------------------------------------------------------------------
          $Replace['From'] = $From;
          #---------------------------------------------------------------------
          $Path = SPrintF('Notifies/Jabber/%s',$Template);
          #---------------------------------------------------------------------
          if(Is_Error(System_Element(SPrintF('comp/%s.comp.php',$Path))))
            return new gException('TEMPLATE_Jabber_MESSAGE_NOT_FOUND','Шаблон сообщения не найден');
          #---------------------------------------------------------------------
          $Comp = Comp_Load($Path,$Replace);
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error('[Jabber_Send]: ошибка загрузки шаблона сообщения');
          #---------------------------------------------------------------------
          if(!Is_String($Comp))
            return new gException('TEMPLATE_DISABLE','Шаблон не активен');
          #---------------------------------------------------------------------
          $IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$User['ID'],'TypeID'=>'Jabber','Params'=>Array($User['JabberID'],$Comp,$User['ID'])));
          #---------------------------------------------------------------------
          switch(ValueOf($IsAdd)){
            case 'error':
              return ERROR | @Trigger_Error('[Jabber_Send]: не удалось установить задание в очередь');
            case 'exception':
              return ERROR | @Trigger_Error('[Jabber_Send]: не удалось установить задание');
            case 'array':
              return TRUE;
            default:
              return ERROR | @Trigger_Error(101);
          }
          #---------------------------------------------------------------------
          return TRUE;
        default:
          return ERROR | @Trigger_Error(101);
      }
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
?>
