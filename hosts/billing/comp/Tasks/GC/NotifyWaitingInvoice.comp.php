<?php


#-------------------------------------------------------------------------------
/** @author Sergey Sedov (for www.host-food.ru) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Params');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Where = SPrintF("`StatusID` = 'Waiting' AND `StatusDate` BETWEEN (UNIX_TIMESTAMP( ) - (%d+1) *86400) AND (UNIX_TIMESTAMP( ) - %d *86400)",$Params['DaysBeforeNotice'],$Params['DaysBeforeNotice']);
#-------------------------------------------------------------------------------
$Invoices = DB_Select('InvoicesOwners',Array('ID','UserID'),Array('SortOn'=>'CreateDate', 'IsDesc'=>TRUE, 'Where'=>$Where));
switch(ValueOf($Invoices)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return TRUE;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Invoices as $Invoice){
      Debug( SPrintF("[Tasks/GC/NotifyWaitingInvoice]: Уведомление о неоплаченном счете #%d.",$Invoice['ID']) );
      #----------------------------------TRANSACTION----------------------------
      if(Is_Error(DB_Transaction($TransactionID = UniqID('comp/Tasks/GC/NotifyWaitingInvoice'))))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $IsSend = NotificationManager::sendMsg('NotPayedInvoice', (integer)$Invoice['UserID'], Array('Theme'=>SPrintF('Неоплаченный счет #%d',$Invoice['ID']),'InvoiceID'=>$Invoice['ID']));
      #-------------------------------------------------------------------------
      switch(ValueOf($IsSend)){
      case 'true':
        #-------------------------------------------------------------------------
        $Event = Array(
			'UserID'	=> $Invoice['UserID'],
			'PriorityID'	=> 'Billing',
			'Text'		=> SPrintF('Уведомление о неоплаченном счете #%d, неоплачен более %d дней',$Invoice['ID'],$Params['DaysBeforeNotice'])
		      );
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
      break;
      case 'exception':
        #-------------------------------------------------------------------------
        $Event = Array(
			'UserID'	=> $Invoice['UserID'],
			'PriorityID'	=> 'Billing',
			'Text'		=> SPrintF('Уведомление о неоплаченном счете #%d не доставлено. Не удалось оповестить пользователя ни одним из методов.',$Invoice['ID'])
		      );
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
      break;
      default:
        return ERROR | @Trigger_Error(500);
      }
      #-------------------------------------------------------------------------
      if(Is_Error(DB_Commit($TransactionID)))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
    }
    return TRUE;
  default:
    return ERROR | @Trigger_Error(101);
}

?>
