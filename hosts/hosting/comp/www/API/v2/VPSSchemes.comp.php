<?php
#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Out = $SchemesIDs = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// необходимо выбрать активные тарифы, и к ним добавить те, которые уже заказаны у юзера
// т.е. тарифы которые неактивны но используются клиентом исторически
$VPSOrders = DB_Select('VPSOrdersOwners',Array('SchemeID'),Array('Where'=>SPrintF('`UserID` = %u',$GLOBALS['__USER']['ID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($VPSOrders as $VPSOrder)
		if(!In_Array($VPSOrder['SchemeID'],$SchemesIDs))
			$SchemesIDs[] = $VPSOrder['SchemeID'];
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём активные для юзера тарифы
$Where = Array(
		'(`UserID` = @local.__USER_ID OR FIND_IN_SET(`GroupID`,@local.__USER_GROUPS_PATH))',
		'`IsActive` = "yes"',
		);
#-------------------------------------------------------------------------------
$VPSSchemes = DB_Select('VPSSchemesOwners',Array('ID'),Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSSchemes)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($VPSSchemes as $VPSScheme)
		if(!In_Array($VPSScheme['ID'],$SchemesIDs))
			$SchemesIDs[] = $VPSScheme['ID'];
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// а нету тарифных планов....
if(!SizeOf($SchemesIDs))
	return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$VPSSchemes = DB_Select('VPSSchemesOwners',Array('*','(SELECT `Params` FROM `Servers` WHERE `ServersGroupID` = `VPSSchemesOwners`.`ServersGroupID` LIMIT 1) AS `Params`'),Array('Where'=>SPrintF('`ID` IN (%s)',Implode(',',$SchemesIDs)),'SortOn'=>Array('SortID','PackageID')));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSSchemes)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $Out;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
foreach($VPSSchemes as $VPSScheme){
	#-------------------------------------------------------------------------------
	// загружаем XML
	$Fields = System_XML(SPrintF('config/Schemes.%s.xml',$VPSScheme['SchemeParams']['SystemID']));
	if(Is_Error($Fields))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Fields) as $Key){
		#-------------------------------------------------------------------------------
		//$Field = $Fields[$Key];
		#-------------------------------------------------------------------------------
		if(IsSet($VPSScheme['SchemeParams'][$Key])){
			#-------------------------------------------------------------------------------
			$Fields[$Key]['Value'] = $VPSScheme['SchemeParams'][$Key];
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$Fields[$Key]['Value'] = "";
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		// запоминаем размер диска
		if(IsSet($Fields[$Key]['InternalName']) && $Fields[$Key]['InternalName'] == 'HDD')
			$HDD = $VPSScheme['SchemeParams'][$Key];
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$VPSScheme['SchemeParams'] = $Fields;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// перебираем операционки, выбираем те которые под лимиты пролезают
	$OS = Array();
	#-------------------------------------------------------------------------------
	foreach(Explode("\n",$VPSScheme['Params']['DiskTemplate']) as $Line){
		#-------------------------------------------------------------------------------
		// распиливаем по = на образ:размер и цивильное имя
		$Line1 = Explode('=',$Line);
		#-------------------------------------------------------------------------------
                // распиливаем по : на образ и размер
                $Template = Explode(':',$Line1[0]);
		#-------------------------------------------------------------------------------
		// если задан размер, сравниваем его с тарифным местом и или продолжаем или пропускаем
		if(IsSet($Template[1])){
			#-------------------------------------------------------------------------------
			// если размер меньше чем выдеелно по тарфиу - обавляем в список
			if($Template[1] < $HDD)
				$OS[] = $Template[0];
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			// если размер не задан - тоже добавляем
			$OS[] = $Template[0];
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$VPSScheme['OS'] = $OS;
	#-------------------------------------------------------------------------------
	UnSet($VPSScheme['Params']);
	#-------------------------------------------------------------------------------
	$Out[$VPSScheme['ID']] = $VPSScheme;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

