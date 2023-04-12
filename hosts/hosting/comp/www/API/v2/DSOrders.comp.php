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
$IsIPMI	=  (boolean) @$Args['IsIPMI'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/IPMI.SuperMicro.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// список колонок которые юзеру не показываем
$Config = Config();
#-------------------------------------------------------------------------------
$Exclude = Array_Keys($Config['APIv2ExcludeColumns']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Out = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = Array(SPrintF('`UserID` = %u',$GLOBALS['__USER']['ID']));
#-------------------------------------------------------------------------------
$Columns = Array(
		'*',
		'(SELECT `Name` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `Scheme`',
		'(SELECT `IPaddr` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `IPaddr`',
		'(SELECT `OS` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `OS`',
		'(SELECT `DSuser` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `DSuser`',
		'(SELECT `DSpass` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `DSpass`',
		'(SELECT `ILOaddr` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `ILOaddr`',
		'(SELECT `ILOuser` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `ILOuser`',
		'(SELECT `ILOpass` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `ILOpass`',
		'(SELECT `CPU` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `CPU`',
		'(SELECT `ram` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `ram`',
		'(SELECT `raid` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `raid`',
		'(SELECT `disks` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `disks`',
		'(SELECT `Name` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = (SELECT `ServersGroupID` FROM `Servers` WHERE `Servers`.`ID` =  (SELECT `ServerID` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`))) as `ServersGroupName`',
		'(SELECT `IsAutoProlong` FROM `Orders` WHERE `DSOrdersOwners`.`OrderID`=`Orders`.`ID`) AS `IsAutoProlong`',
		'(SELECT `UserNotice` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DSOrdersOwners`.`OrderID`) AS `UserNotice`',
		'(SELECT `AdminNotice` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DSOrdersOwners`.`OrderID`) AS `AdminNotice`',
		'(SELECT `Customer` FROM `Contracts` WHERE `Contracts`.`ID` = `DSOrdersOwners`.`ContractID`) AS `Customer`',
		'(SELECT (SELECT `Code` FROM `Services` WHERE `Orders`.`ServiceID` = `Services`.`ID`) FROM `Orders` WHERE `DSOrdersOwners`.`OrderID` = `Orders`.`ID`) AS `Code`'
		);


#-------------------------------------------------------------------------------
$DSOrders = DB_Select('DSOrdersOwners',$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($DSOrders)){
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
foreach($DSOrders as $DSOrder){
	#-------------------------------------------------------------------------------
	UnSet($DSOrder['Params']);
	#-------------------------------------------------------------------------------
	$DSOrder['IPMI']	= Array();
	$DSOrder['IPMI.Sensors']= Array();
	#-------------------------------------------------------------------------------
	// выпиливаем колонки
	foreach(Array_Keys($DSOrder) as $Column)
		if(In_Array($Column,$Exclude))
			UnSet($DSOrder[$Column]);
	#-------------------------------------------------------------------------------
	// для неактивных выпливаем тоже колонки
	if(!In_Array($DSOrder['StatusID'],Array('Active','Suspended'))){
		#-------------------------------------------------------------------------------
		$Deleted = Array('DSuser','DSpass','ILOaddr','ILOuser','ILOpass');
		#-------------------------------------------------------------------------------
		foreach(Array_Keys($DSOrder) as $Column)
			if(In_Array($Column,$Deleted))
				$DSOrder[$Column] = '*HIDDEN*';;
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		// если надо IPMI - достаём
		if($IsIPMI){
			#-------------------------------------------------------------------------------
			// достаём IPMI информацию о сервере, если он оплачен и данные введены
			if(!$DSOrder['ILOaddr'] || !$DSOrder['ILOuser'] || !$DSOrder['ILOpass']){
				// нихрена
			}else{
				#-------------------------------------------------------------------------------
				// общая информация
				$IPMI = IPMI_StatusGet($DSOrder);
				#-------------------------------------------------------------------------------
				if(Is_Exception($IPMI) || Is_Error($IPMI)){
					// нихрена
				}else{
					#-------------------------------------------------------------------------------
					$DSOrder['IPMI'] = $IPMI;
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
				// сенсоры
				$Sensors = IPMI_SDR($DSOrder);
				#-------------------------------------------------------------------------------
				if(Is_Exception($Sensors) || Is_Error($Sensors)){
					// нихрена
				}else{
					#-------------------------------------------------------------------------------
					$DSOrder['IPMI.Sensors'] = $Sensors;
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$DSOrder['ExtraIP'] = ($DSOrder['ExtraIP'])?Explode("\n",$DSOrder['ExtraIP']):Array();
	#-------------------------------------------------------------------------------
	$Out[$DSOrder['ID']] = $DSOrder;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

