<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ContractID = (integer) @$Args['ContractID'];
$Summ       =  (double) Str_Replace(',', '.', @$Args['Summ']);
$ServiceID  = (integer) @$Args['ServiceID'];
$Comment    =  (string) @$Args['Comment'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
Debug("[comp/www/Administrator/API/PostingMake]: ContractID = $ContractID; Summ = $Summ; ServiceID = $ServiceID; Comment = $Comment");
#-------------------------------------------------------------------------------
$Service = DB_Select('Services',Array('ID','Name','OperationSign'),Array('UNIQ','ID'=>$ServiceID));
#-------------------------------------------------------------------------------
switch(ValueOf($Service)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SERVICE_NOT_FOUND','Указанная услуга не найдена');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Contract = DB_Select('Contracts',Array('ID','Balance','UserID'),Array('UNIQ','ID'=>$ContractID));
#-------------------------------------------------------------------------------
switch(ValueOf($Contract)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('CONTRACT_NOT_FOUND','Договор не найден');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$User = DB_Select('Users',Array('ID','GroupID'),Array('UNIQ','ID'=>$Contract['UserID']));
#-------------------------------------------------------------------------------
switch(ValueOf($User)){
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
$Before = (double)$Contract['Balance'];
#-------------------------------------------------------------------------------
# значение с минусом может прийти изнутри, при списании за услугу!!
if($Summ < 0)
	$Summ = $Summ * -1;
#-------------------------------------------------------------------------------
# считаем баланс после
if($Service['OperationSign'] == "+"){
	#-------------------------------------------------------------------------------
	$After = $Before + $Summ;
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$After = $Before - $Summ;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
if($After < 0){
	#-------------------------------------------------------------------------------
	# сумма баланса меньше нуля, но, если это начисление/возврат, то операцию не надо запрещать
	if($Service['OperationSign'] != "+"){
		return new gException('NO_BALANCE_MONEY','На балансе договора недостаточно средств для осуществления данной операции');
	#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#------------------------------TRANSACTION--------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('PostingMake'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsUpdated = DB_Update('Contracts',Array('Balance'=>$After),Array('ID'=>$Contract['ID']));
if(Is_Error($IsUpdated))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IPosting = Array(
		'ContractID' => $Contract['ID'],
		'ServiceID'  => $Service['ID'],
		'Comment'    => $Comment,
		'Before'     => $Before,
		'After'      => $After
		);
#-------------------------------------------------------------------------------
$PostingID = DB_Insert('Postings',$IPosting);
if(Is_Error($PostingID))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(DB_Commit($TransactionID)))
	return ERROR | @Trigger_Error(500);
#-------------------------END TRANSACTION---------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
