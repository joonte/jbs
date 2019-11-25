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
# проверяем тип договора с которого переводим
if($FromContract['TypeID'] == "Juridical")
	return new gException('ATTEMPT_TRANSFER_FROM_Juridical','Переводы с договоров юридических лиц запрещены');
# проверяем тип договора на который переводим
if($ToContract['TypeID'] == "Juridical")
	return new gException('ATTEMPT_TRANSFER_TO_Juridical','Переводы на договора юридических лиц запрещены');
if($ToContract['TypeID'] == "NaturalPartner")
	return new gException('ATTEMPT_TRANSFER_TO_Juridical','Переводы на партнёрский договор запрещены');
# проверяем баланс договора с кторого переводим
if($FromContract['Balance'] < $Summ){
	$fSumm = Comp_Load('Formats/Currency',$FromContract['Balance']);
	if(Is_Error($fSumm))
		return ERROR | @Trigger_Error(500);
	return new gException('CONTRACT_BALLANCE_TOO_LOW','На балансе выбранного договора слишком мало средств. Введите сумму меньшую или равную ' . $fSumm);
}
#-------------------------------------------------------------------
#-------------------------------------------------------------------
#-----------------------------TRANSACTION-------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('ContractFundsTransfer'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------
$NumberFrom = Comp_Load('Formats/Contract/Number',$FromContract['ID']);
if(Is_Error($NumberFrom))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------
$NumberTo = Comp_Load('Formats/Contract/Number',$ToContract['ID']);
if(Is_Error($NumberTo))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------
# снимаем с одного договора деньгу
#Debug(print_r($GLOBALS, true));
$Comment = "Перевод на договор '#" . $NumberTo . "', запрошено с IP адреса '" . $GLOBALS['_SERVER']['REMOTE_ADDR'] . "'";
$Comp = Comp_Load(
			'www/Administrator/API/PostingMake',
			Array(
				'ContractID'	=> $FromContract['ID'],
				'ServiceID'     => '2000',
				'Comment'       => $Comment,
				'Summ'          => "-" . $Summ
			)
		);
# кладём деньгу на другой договор
$Comment = "Перевод с договора '#" . $NumberFrom . "', запрошено с IP адреса '" . $GLOBALS['_SERVER']['REMOTE_ADDR'] . "'";
$Comp = Comp_Load(
			'www/Administrator/API/PostingMake',
			Array(
				'ContractID'	=> $ToContract['ID'],
				'ServiceID'     => '1000',
				'Comment'       => $Comment,
				'Summ'          => $Summ
			)
		);
#-------------------------------------------------------------------
#-------------------------------------------------------------------
# сообщение в события
$Summ = Comp_Load('Formats/Currency',$Summ);
if(Is_Error($Summ))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------
$Event = Array(
		'UserID'	=> $FromContract['UserID'],
		'PriorityID'	=> 'Billing',
		'Text'		=> SPrintF("Осуществлён перевод с договора '%s' (#%s) на договор '%s' (#%s), сумма перевода %01.2f",$FromContract['Customer'],$NumberFrom,$ToContract['Customer'],$NumberTo,$Summ)
		);
$Event = Comp_Load('Events/EventInsert',$Event);
if(!$Event)
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------
if(Is_Error(DB_Commit($TransactionID)))
	return ERROR | @Trigger_Error(500);
#---------------------------END TRANSACTION-------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
