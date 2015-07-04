<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Invoice');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
# JBS-195: запрет второго условно проведённого
$Where = SPrintF("`StatusID` = 'Conditionally' AND `UserID` = (SELECT `UserID` FROM `InvoicesOwners` WHERE `ID` = %u )",$Invoice['ID']);
$Count = DB_Count('InvoicesOwners',Array('Where'=>$Where));
#-------------------------------------------------------------------------------
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count && !$GLOBALS['__USER']['IsAdmin'])
	return new gException('DENY_SECOND_CONDITIONALLY_INVOICE','У пользователя уже есть условно проведённые счета. Нельзя провести более одного счёта условно.');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Invoice['IsPosted'])
	return TRUE;
#----------------------------------TRANSACTION----------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('comp/Triggers/Statuses/Invoices/Payed'))))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Number = Comp_Load('Formats/Invoice/Number',$Invoice['ID']);
if(Is_Error($Number))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsUpdate = Comp_Load('www/Administrator/API/PostingMake',Array('ContractID'=>$Invoice['ContractID'],'Summ'=>$Invoice['Summ'],'ServiceID'=>1000,'Comment'=>SPrintF('по счёту №%s',$Number)));
#-------------------------------------------------------------------------------
switch(ValueOf($IsUpdate)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $Event = Array(
			'UserID'	=> $Invoice['UserID'],
			'PriorityID'	=> 'Billing',
			'Text'		=> SPrintF('Счет №%s, на сумму %s, условно оплачен',$Number,$Invoice['Summ'])
                  );
    $Event = Comp_Load('Events/EventInsert',$Event);
    if(!$Event)
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Columns = Array('OrderID','Amount','(SELECT `ServiceID` FROM `Orders` WHERE `Orders`.`ID` = `OrderID`) as `ServiceID`');
    #---------------------------------------------------------------------------
    $Items = DB_Select('InvoicesItems',$Columns,Array('SortOn'=>'Summ','IsDesc'=>TRUE,'Where'=>SPrintF('`InvoiceID` = %u',$Invoice['ID'])));
    #---------------------------------------------------------------------------
    switch(ValueOf($Items)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        # No more...
      break;
      case 'array':
        #-----------------------------------------------------------------------
        foreach($Items as $Item){
          #---------------------------------------------------------------------
          $Path = SPrintF('Services/%u',$Item['ServiceID']);
          #---------------------------------------------------------------------
          $Element = System_Element(SPrintF('comp/%s.comp.php',$Path));
          if(!Is_Error($Element)){
            #-------------------------------------------------------------------
            $Comp = Comp_Load($Path,$Item);
            #-------------------------------------------------------------------
            switch(ValueOf($Comp)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                #---------------------------------------------------------------
                $Number = Comp_Load('Formats/Order/Number',$Item['OrderID']);
                if(Is_Error($Number))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
		$Event = Array(
				'UserID'	=> $Invoice['UserID'],
				'PriorityID'	=> 'Error',
				'Text'		=> SPrintF('Не удалось произвести автоматическую оплату заказа №%s, причина (%s)',$Number,$Comp->String),'PriorityID'=>'Error'
			      );
		$Event = Comp_Load('Events/EventInsert',$Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
              break;
              case 'true':
                # No more...
              break;
              default:
                return ERROR | @Trigger_Error(101);
            }
          }elseif($Item['OrderID']){
            #-------------------------------------------------------------------
            $Comp = Comp_Load('www/API/ServiceOrderPay',Array('ServiceOrderID'=>$Item['OrderID'],'AmountPay'=>$Item['Amount']));
            #-------------------------------------------------------------------
            switch(ValueOf($Comp)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                #---------------------------------------------------------------
                $Number = Comp_Load('Formats/Order/Number',$Item['OrderID']);
                if(Is_Error($Number))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
		$Event = Array(
				'UserID'	=> $Invoice['UserID'],
				'PriorityID'	=> 'Error',
				'Text'		=> SPrintF('Не удалось произвести автоматическую оплату заказа №%s, причина (%s)',$Number,$Comp->String),
			      );
		$Event = Comp_Load('Events/EventInsert',$Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
              break;
              case 'array':
                # No more...
              break;
              default:
                return ERROR | @Trigger_Error(101);
            }
          }
        }
      break;
      default:
        return ERROR | @Trigger_Error(101);
    }
    #---------------------------------------------------------------------------
    $IsUpdate = DB_Update('Invoices',Array('IsPosted'=>TRUE),Array('ID'=>$Invoice['ID']));
    if(Is_Error($IsUpdate))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if(Is_Error(DB_Commit($TransactionID)))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    return TRUE;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
