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
$VPSOrderID	= (integer) @$Args['VPSOrderID'];
$OrderID	= (integer) @$Args['OrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/Tree.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','SchemeID','(SELECT `ServersGroupID` FROM `Servers` WHERE `Servers`.`ID` = (SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `VPSOrdersOwners`.`OrderID`)) AS `ServersGroupID`','(SELECT `Params` FROM `Servers` WHERE `Servers`.`ID` = (SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `VPSOrdersOwners`.`OrderID`)) AS `Params`','StatusID');
#-------------------------------------------------------------------------------
$Where = ($VPSOrderID?SPrintF('`ID` = %u',$VPSOrderID):SPrintF('`OrderID` = %u',$OrderID));
#-------------------------------------------------------------------------------
$VPSOrder = DB_Select('VPSOrdersOwners',$Columns,Array('UNIQ','Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrder)){
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
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('VPSOrdersRead',(integer)$__USER['ID'],(integer)$VPSOrder['UserID']);
#-------------------------------------------------------------------------------
switch(ValueOf($IsPermission)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'false':
	return ERROR | @Trigger_Error(700);
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
if($VPSOrder['StatusID'] != 'Active')
	return new gException('ORDER_NOT_ACTIVE','Заказ виртуального сервера не активен');
#-------------------------------------------------------------------------------
$OldScheme = DB_Select('VPSSchemes',Array('IsSchemeChange'),Array('UNIQ','ID'=>$VPSOrder['SchemeID']));
#-------------------------------------------------------------------------------
switch(ValueOf($OldScheme)){
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
if(!$OldScheme['IsSchemeChange'])
	return new gException('SCHEME_NOT_ALLOW_SCHEME_CHANGE','Тарифный план заказа виртуального сервера не позволяет смену тарифа');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$UniqID = UniqID('VPSSchemes');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Services/Schemes','VPSSchemes',$VPSOrder['UserID'],Array('Name','ServersGroupID'),$UniqID);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Where = Array(
		SPrintF("`ServersGroupID` = %u",$VPSOrder['ServersGroupID']),
		);
#-------------------------------------------------------------------------------
if(!$__USER['IsAdmin'])
	$Where[] = "`IsActive` = 'yes' AND `IsSchemeChangeable` = 'yes'";
#-------------------------------------------------------------------------------
$VPSSchemes = DB_Select($UniqID,Array('ID','Name','disklimit','CostMonth'),Array('SortOn'=>'SortID','Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSSchemes)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('VPS_SCHEMES_NOT_FOUND','Не тарифов для смены');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
if(SizeOf($VPSSchemes) == 1)
	return new gException('VPS_SCHEMES_NOT_FOUND','Нет тарифов для смены');
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
$DOM->AddText('Title','Смена тарифного плана');
#-------------------------------------------------------------------------------
$Table = $Options = Array();
#-------------------------------------------------------------------------------
if(In_Array($VPSOrder['Params']['SystemID'],Array('VmManager5_KVM','VmManager6_Hosting'))){
	#-------------------------------------------------------------------------------
	$Span = new Tag('SPAN',new Tag('SPAN','Обращаем ваше внимание, что ваш виртуальный сервер использует тип виртуализации KVM. В связи с этим, изменение тарифного плана возможно только в большую сторону, уменьшить размер диска - невозможно.'),new Tag('HR'),new Tag('SPAN','Также, будут удалены все снимки виртуальной машины'));
	#-------------------------------------------------------------------------------
	$Table[] = new Tag('TD',Array('colspan'=>2,'width'=>300,'class'=>'Standard','style'=>'background-color:#FDF6D3;'),$Span);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
foreach($VPSSchemes as $VPSScheme){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Currency',$VPSScheme['CostMonth']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Options[$VPSScheme['ID']] = SPrintF('%s / %s Gb / %s',$VPSScheme['Name'],$VPSScheme['disklimit']/1024,$Comp);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'NewSchemeID'),$Options,NULL,$VPSOrder['SchemeID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Новый тарифный план',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>"FormEdit('/API/VPSOrderSchemeChange','VPSOrderSchemeChangeForm','Смена тарифного плана');",'value'=>'Сменить'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'VPSOrderSchemeChangeForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'VPSOrderID','type'=>'hidden','value'=>$VPSOrder['ID']));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Form);
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Build(FALSE)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
