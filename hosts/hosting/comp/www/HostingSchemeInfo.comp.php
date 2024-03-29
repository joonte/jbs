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
$HostingSchemeID = (string) @$Args['HostingSchemeID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$HostingScheme = DB_Select('HostingSchemes','*',Array('UNIQ','ID'=>$HostingSchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingScheme)){
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
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Window')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Тариф хостинга');
#-------------------------------------------------------------------------------
$Table = Array('Общая информация');
#-------------------------------------------------------------------------------
$Table[] = Array('Название тарифа',$HostingScheme['Name']);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Currency',$HostingScheme['CostDay']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Цена за 1 дн.',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Currency',$HostingScheme['CostMonth']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Цена за месяц',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ServersGroup = DB_Select('ServersGroups','*',Array('UNIQ','ID'=>$HostingScheme['ServersGroupID']));
if(!Is_Array($ServersGroup))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Группа серверов',$ServersGroup['Name']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Logic',$HostingScheme['IsActive']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Тариф активен',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Logic',$HostingScheme['IsProlong']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Возможность продления',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Logic',$HostingScheme['IsSchemeChange']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Возможность смены тарифа',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($HostingScheme['MaxOrders'] > 0)
	$Table[] = Array('Максимальное число заказов',$HostingScheme['MaxOrders']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = SPrintF('Общие ограничения / %s',$HostingScheme['SchemeParams']['SystemID']);
#-------------------------------------------------------------------------------
// загружаем XML
$Fields = System_XML(SPrintF('config/Schemes.%s.xml',$HostingScheme['SchemeParams']['SystemID']));
if(Is_Error($Fields))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
foreach(Array_Keys($Fields) as $Key){
	#-------------------------------------------------------------------------------
	$Field = $Fields[$Key];
	#-------------------------------------------------------------------------------
	if(IsSet($Field['Unit'])){
		#-------------------------------------------------------------------------------
		$Name = SPrintF('%s, %s',$Field['Name'],$Field['Unit']);
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$Name = $Field['Name'];
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Value = IsSet($HostingScheme['SchemeParams'][$Key])?$HostingScheme['SchemeParams'][$Key]:$Field['Value'];
	#-------------------------------------------------------------------------------
	if(IsSet($Field['Type']) && $Field['Type'] == 'CheckBox'){
		#-------------------------------------------------------------------------------
		$Value = Comp_Load('Formats/Logic',$Value);
		if(Is_Error($Value))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Table[] = Array($Name,$Value);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Comp);
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Build(FALSE)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
