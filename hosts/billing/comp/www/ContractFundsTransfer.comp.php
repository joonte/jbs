<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/

#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Window')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Перевод средств между счетами');
#-------------------------------------------------------------------------------
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/ContractFundsTransfer.js}'));
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'ContractFundsTransferForm','onsubmit'=>'return false;'));
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
#---------------------------------------------------------------------------
$Where = "`UserID` = " . $GLOBALS['__USER']['ID'] . " AND (`TypeID` = 'Default' OR `TypeID` = 'Individual' OR `TypeID` = 'Natural' OR `TypeID` = 'NaturalPartner')";
$ContractsFrom = DB_Select('Contracts',Array('ID','TypeID','Customer','Balance'),Array('Where'=>$Where));
switch(ValueOf($ContractsFrom)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('CONTRACT_FROM_NOT_FOUND','Нет подходящих договоров, с которых можно делать переводы.');
case 'array':
        break;
default:
	return ERROR | @Trigger_Error(101);
}
#---------------------------------------------------------------------------
#---------------------------------------------------------------------------
$Where = "`UserID` = " . $GLOBALS['__USER']['ID'] . " AND (`TypeID` = 'Default' OR `TypeID` = 'Individual' OR `TypeID` = 'Natural')";
$ContractsTo = DB_Select('Contracts',Array('ID','TypeID','Customer','Balance'),Array('Where'=>$Where));
switch(ValueOf($ContractsTo)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('CONTRACT_FROM_NOT_FOUND','Нет подходящих договоров, на которые можно делать переводы.');
case 'array':
        break;
default:
	return ERROR | @Trigger_Error(101);
}
#---------------------------------------------------------------------------
#---------------------------------------------------------------------------
$Options = Array();
foreach($ContractsFrom as $Contract){
	# проверяем, что баланс не нулевой
	if($Contract['Balance'] > 0){
		$Customer = $Contract['Customer'];
		if(Mb_StrLen($Customer) > 20)
			$Customer = SPrintF('%s...',Mb_SubStr($Customer,0,20));
		# add ballance
		$SummFrom = Comp_Load('Formats/Currency',$Contract['Balance']);
		if(Is_Error($SummFrom))
			return ERROR | @Trigger_Error(500);
		$Customer .= " [" . $SummFrom . "]";
		$Options[$Contract['ID']] = $Customer;
	}
}
#-----------------------------------------------------------------------
if(SizeOf($Options) == 0)
	return new gException('CONTRACT_FROM_NOT_FOUND','На всех договорах, с которых можно сделать перевод, нулевой баланс');
#-----------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'FromContractID'),$Options);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
$NoBody = new Tag('NOBODY',$Comp);
$Table[] = Array('Откуда',$NoBody);
#-----------------------------------------------------------------------
#-----------------------------------------------------------------------
$Options = Array();
foreach($ContractsTo as $Contract){
	$Customer = $Contract['Customer'];
	if(Mb_StrLen($Customer) > 20)
	$Customer = SPrintF('%s...',Mb_SubStr($Customer,0,20));
	# add ballance
	$SummTo = Comp_Load('Formats/Currency',$Contract['Balance']);
	$Customer .= " [" . $SummTo . "]";
	$Options[$Contract['ID']] = $Customer;
}
#-----------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'ToContractID'),$Options);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
$NoBody = new Tag('NOBODY',$Comp);
$Table[] = Array('Куда',$NoBody);
#-----------------------------------------------------------------------
#-----------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'name'  => 'Summ',
			'value' => '0.00',
			'type'  => 'text',
		)
	);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-----------------------------------------------------------------------
$Table[] = Array('Сумма',$Comp);
#-----------------------------------------------------------------------
#-----------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'onclick' => 'ContractFundsTransfer();',
			'type'    => 'button',
			'value'   => 'Перевести'
		)
	);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-----------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Form);
#-------------------------------------------------------------------------------
$Out = $DOM->Build(FALSE);
#-------------------------------------------------------------------------------
if(Is_Error($Out))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------

?>
