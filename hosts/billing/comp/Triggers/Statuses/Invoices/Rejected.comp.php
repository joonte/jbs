<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Invoice');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
// надо как-то узнать предыдущий статус - был ли он условно оплачен...
$Count = DB_Count('InvoicesOwners',Array('Where'=>SPrintF('`ID` = %u AND `StatusID` = "Conditionally"',$Invoice['ID'])));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
// предыдущий статус не-условно оплаченный
if(!$Count)
	return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// перенесено из hosts/billing/comp/Tasks/GC/NotifyConditionallyInvoice.comp.php
// вычитаем сумму счёта из договора, на который счёт.
$Contract = Comp_Load('Contracts/Fetch',$Invoice['ContractID']);
if(Is_Error($Contract))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$After = $Contract['Balance'] - $Invoice['Summ'];
#-------------------------------------------------------------------------------
$IsUpdate = DB_Update('Contracts',Array('Balance'=>$After),Array('ID'=>$Invoice['ContractID']));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
// заносим запись в историю операций с контрактами
$Comp = Comp_Load('Formats/Invoice/Number',$Invoice['ID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IPosting = Array(
		'ContractID'	=> $Invoice['ContractID'],
		'ServiceID'	=> 2000,
		'Comment'	=> SPrintF('Возврат средств условно зачисленных по счёту #%u',$Comp),
		'Before'	=> $Contract['Balance'],
		'After'		=> $After
		);
#-------------------------------------------------------------------------------
$PostingID = DB_Insert('Postings',$IPosting);
if(Is_Error($PostingID))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
