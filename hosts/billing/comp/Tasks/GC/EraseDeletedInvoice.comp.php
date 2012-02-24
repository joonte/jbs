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
$Where = SPrintF("`StatusID` = 'Rejected' AND `StatusDate` < UNIX_TIMESTAMP( ) - %d *86400", $Params['DaysBeforeErase']);
#-------------------------------------------------------------------------------
$Invoices = DB_Select('InvoicesOwners',Array('ID','UserID'),Array('SortOn'=>'CreateDate', 'IsDesc'=>TRUE, 'Where'=>$Where, 'Limits'=>Array(0,$Params['ItemPerIteration'])));
switch(ValueOf($Invoices)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return TRUE;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Invoices as $Invoice){
      Debug( SPrintF("[Tasks/GC/EraseDeletedInvoice]: Удаление счёта #%d.",$Invoice['ID']) );
      #----------------------------------TRANSACTION----------------------------
      if(Is_Error(DB_Transaction($TransactionID = UniqID('comp/Tasks/GC/EraseDeletedInvoice'))))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('www/API/Delete',Array('TableID'=>'Invoices','RowsIDs'=>$Invoice['ID']));
      #-------------------------------------------------------------------------
      switch(ValueOf($Comp)){
      case 'array':
        $Event = Array(
			'UserID'	=> $Invoice['UserID'],
			'PriorityID'	=> 'Billing',
			'Text'		=> SPrintF('Отменённый счёт #%d автоматически удалён, оплата не поступила в течение %d дней.',$Invoice['ID'],$Params['DaysBeforeErase'])
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
    $Count = DB_Count('Invoices',Array('Where'=>$Where));
    if(Is_Error($Count))
      return ERROR | @Trigger_Error(500);
    return ($Count?$Count:TRUE);
  default:
    return ERROR | @Trigger_Error(101);
}

?>
