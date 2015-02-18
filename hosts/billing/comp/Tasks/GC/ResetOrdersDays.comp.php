<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Params');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
# находим всех сотрудников - их заказы не окучиваем
$Users = DB_Select('Users',Array('ID','Name'),Array('Where'=>SPrintF("(SELECT `IsDepartment` FROM `Groups` WHERE `Groups`.`ID` = `Users`.`GroupID`) = 'yes' OR `ID` = 100")));
#-------------------------------------------------------------------------------
switch(ValueOf($Users)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#-------------------------------------------------------------------------------
	$PersonalIDs = Array();
	#-------------------------------------------------------------------------------
	foreach($Users as $User)
		if(!In_Array($User['ID'],$PersonalIDs))
			$PersonalIDs[] = $User['ID'];
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# выбираем всех у кого есть бесплатные дни
$Where = Array('`DaysRemainded` > 0','`Cost` = 0 OR `Discont` = 1');
#-------------------------------------------------------------------------------
$OrdersConsider = DB_Select('OrdersConsider',Array('OrderID','SUM(`DaysRemainded`) AS `SumDaysRemainded`',),Array('GroupBy'=>'OrderID','Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($OrdersConsider)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return TRUE;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$UserIDs = Array();
#-------------------------------------------------------------------------------
foreach($OrdersConsider as $OrderConsider){
	#-------------------------------------------------------------------------------
	if($OrderConsider['SumDaysRemainded'] <= 28)
		continue;
	#-------------------------------------------------------------------------------
	#Debug(SPrintF('[comp/Tasks/GC/ResetOrdersDays]: OrderID = %s; DaysRemainded = %s',$OrderConsider['OrderID'],$OrderConsider['SumDaysRemainded']));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# Заказы этого клиента
	$Where = SPrintF('`UserID` = (SELECT `UserID` FROM `OrdersOwners` WHERE `ID` = %u)',$OrderConsider['OrderID']);
	#-------------------------------------------------------------------------------
	$Orders = DB_Select('OrdersOwners',Array('ID','UserID'),Array('Where'=>$Where));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Orders)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Order = Current($Orders);
	#-------------------------------------------------------------------------------
	$UserID = $Order['UserID'];
	#-------------------------------------------------------------------------------
	# если сотрудник - ничего не делаем
	if(In_Array($UserID,$PersonalIDs))
		continue;
	#-------------------------------------------------------------------------------
	if(In_Array($UserID,$UserIDs)){
		#-------------------------------------------------------------------------------
		continue;
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$UserIDs[] = $UserID;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Array = Array();
	#-------------------------------------------------------------------------------
	foreach($Orders as $Order)
		$Array[] = $Order['ID'];
	#-------------------------------------------------------------------------------
	#Debug(SPrintF('[comp/Tasks/GC/ResetOrdersDays]: OrderIDs = %s',Implode(',',$Array)));
	#-------------------------------------------------------------------------------
	# ищщем заказы за денежки
	$Where = Array('`Cost` > 0','`Discont` < 1','`DaysRemainded` > 0',SPrintF('`OrderID` IN (%s)',Implode(',',$Array)));
	#-------------------------------------------------------------------------------
	$Count = DB_Count('OrdersConsider',Array('Where'=>$Where));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($Count)
		continue;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/GC/ResetOrdersDays]: OrderIDs = %s',Implode(',',$Array)));
	#-------------------------------------------------------------------------------
	# а ещё у него могут быть домены... которые учитываются иначе ...

	# а ещё могут быть услуги настраиваемые вручную...



	#----------------------------------TRANSACTION----------------------------






#      if(Is_Error(DB_Transaction($TransactionID = UniqID('comp/Tasks/GC/EraseDeletedInvoice'))))
#        return ERROR | @Trigger_Error(500);
}
#      #-------------------------------------------------------------------------
#      $Comp = Comp_Load('www/API/Delete',Array('TableID'=>'Invoices','RowsIDs'=>$Invoice['ID']));
#      #-------------------------------------------------------------------------
#      switch(ValueOf($Comp)){
#      case 'array':
#        $Event = Array(
#			'UserID'	=> $Invoice['UserID'],
#			'PriorityID'	=> 'Billing',
#			'Text'		=> SPrintF('Отменённый счёт #%d автоматически удалён, оплата не поступила в течение %d дней.',$Invoice['ID'],$Params['Invoices']['DaysBeforeErase'])
#	              );
#	$Event = Comp_Load('Events/EventInsert',$Event);
#	if(!$Event)
#		return ERROR | @Trigger_Error(500);
#      break;
#      default:
#        return ERROR | @Trigger_Error(500);
#      }
#      #-------------------------------------------------------------------------
#      if(Is_Error(DB_Commit($TransactionID)))
#        return ERROR | @Trigger_Error(500);
#      #-------------------------------------------------------------------------
#    }
#    $Count = DB_Count('Invoices',Array('Where'=>$Where));
#    if(Is_Error($Count))
#      return ERROR | @Trigger_Error(500);
#    return ($Count?$Count:TRUE);
#  default:
#    return ERROR | @Trigger_Error(101);
#}

?>
