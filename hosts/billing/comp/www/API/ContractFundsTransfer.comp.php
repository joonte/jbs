<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$FromContractID	= (integer) @$Args['FromContractID'];
$ToContractID	= (integer) @$Args['ToContractID'];
$Summ		=  (double) Str_Replace(',', '.', @$Args['Summ']);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($FromContractID == $ToContractID)
	return new gException('ATTEMPT_TRANSFER_TO_SOME_CONTRACT','Нельзя переводить средства на тот же самый договор');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Summ < 0.001)
	return new gException('ZERO_TRANSFER_SUMM','Сумма перевода должна быть больше нуля');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$FromContract = DB_Select('Contracts',Array('ID','TypeID','Customer','UserID','Balance'),Array('UNIQ','ID'=>$FromContractID));
#-------------------------------------------------------------------------------
switch(ValueOf($FromContract)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('From_CONTRACT_NOT_FOUND','Не найден договор, с которого переводим средства');
case 'array':
	#---------------------------------------------------------------------------
	$__USER = $GLOBALS['__USER'];
	#---------------------------------------------------------------------------
	$IsPermission = Permission_Check('ContractsRead',(integer)$__USER['ID'],(integer)$FromContract['UserID']);
	#---------------------------------------------------------------------------
	switch(ValueOf($IsPermission)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'false':
		return ERROR | @Trigger_Error(700);
	case 'true':
		break 2;
	default:
		return ERROR | @Trigger_Error(101);
	}
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Contracts']['Types'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// проверяем тип договора с которого переводим
foreach(Array_Keys($Settings) as $Key)
	if($Key == $FromContract['TypeID'])
		if(!$Settings[$Key]['FundTransferFrom'])
			return new gException('FUND_TRANSFER_FROM_DISABLED',SPrintF('Переводы с договора "%s" запрещены',$Settings[$Key]['Name']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ToContract = DB_Select('Contracts',Array('ID','TypeID','Customer','UserID','Balance'),Array('UNIQ','ID'=>$ToContractID));
#-------------------------------------------------------------------------------
switch(ValueOf($ToContract)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('To_CONTRACT_NOT_FOUND','Не найден договор, на который переводим средства');
case 'array':
	#---------------------------------------------------------------------------
	$__USER = $GLOBALS['__USER'];
	#---------------------------------------------------------------------------
	$IsPermission = Permission_Check('ContractsRead',(integer)$__USER['ID'],(integer)$ToContract['UserID']);
	#---------------------------------------------------------------------------
	switch(ValueOf($IsPermission)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'false':
		return ERROR | @Trigger_Error(700);
	case 'true':
		break 2;
	default:
		return ERROR | @Trigger_Error(101);
	}
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// проверяем тип договора НА который переводим
foreach(Array_Keys($Settings) as $Key)
	if($Key == $ToContract['TypeID'])
		if(!$Settings[$Key]['FundTransferTo'])
			return new gException('FUND_TRANSFER_TO_DISABLED',SPrintF('Переводы на договор "%s" запрещены',$Settings[$Key]['Name']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// проверяем баланс договора с кторого переводим
if($FromContract['Balance'] < $Summ){
	$fSumm = Comp_Load('Formats/Currency',$FromContract['Balance']);
	if(Is_Error($fSumm))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	return new gException('CONTRACT_BALLANCE_TOO_LOW',SPrintF('На балансе выбранного договора слишком мало средств. Введите сумму меньшую или равную %s',$fSumm));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#-----------------------------TRANSACTION---------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('ContractFundsTransfer'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$NumberFrom = Comp_Load('Formats/Contract/Number',$FromContract['ID']);
if(Is_Error($NumberFrom))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$NumberTo = Comp_Load('Formats/Contract/Number',$ToContract['ID']);
if(Is_Error($NumberTo))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// снимаем с одного договора деньгу
$Comment = SPrintF('Перевод на договор #%s, запрошено с IP адреса %s',$NumberTo,$GLOBALS['_SERVER']['REMOTE_ADDR']);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/Administrator/API/PostingMake',Array('ContractID'=>$FromContract['ID'],'ServiceID'=>'2000','Comment'=>$Comment,'Summ'=>SPrintF("-%s",$Summ)));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# кладём деньгу на другой договор
$Comment = SPrintF('Перевод с договор #%s, запрошено с IP адреса %s',$NumberFrom,$GLOBALS['_SERVER']['REMOTE_ADDR']);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/Administrator/API/PostingMake',Array('ContractID'=>$ToContract['ID'],'ServiceID'=>'1000','Comment'=>$Comment,'Summ'=>$Summ));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# сообщение в события
$Summ = Comp_Load('Formats/Currency',$Summ);
if(Is_Error($Summ))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Event = Array(
		'UserID'	=> $FromContract['UserID'],
		'PriorityID'	=> 'Billing',
		'Text'		=> SPrintF("Осуществлён перевод с договора '%s' (#%s) на договор '%s' (#%s), сумма перевода %01.2f",$FromContract['Customer'],$NumberFrom,$ToContract['Customer'],$NumberTo,$Summ)
		);
$Event = Comp_Load('Events/EventInsert',$Event);
if(!$Event)
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(DB_Commit($TransactionID)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#---------------------------END TRANSACTION-------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
