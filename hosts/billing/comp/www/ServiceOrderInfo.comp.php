<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$ServiceOrderID = (integer) @$Args['ServiceOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','OrderDate','ContractID','ExpirationDate','StatusID','StatusDate','DependOrderID','(SELECT `Name` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) as `ServiceName`','(SELECT `Address` FROM `Servers` WHERE `OrdersOwners`.`ServerID` = `Servers`.`ID`) AS `Address`','IsAutoProlong','UserNotice','AdminNotice','(SELECT `Customer` FROM `Contracts` WHERE `Contracts`.`ID` = `OrdersOwners`.`ContractID`) AS `Customer`');
#-------------------------------------------------------------------------------
$ServiceOrder = DB_Select('OrdersOwners',$Columns,Array('UNIQ','ID'=>$ServiceOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ServiceOrder)){
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
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('ServiceOrderRead',(integer)$__USER['ID'],(integer)$ServiceOrder['UserID']);
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
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
#-------------------------------------------------------------------------------
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Window')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddText('Title',SPrintF('Информация о заказе на услугу #%s',$ServiceOrder['ID']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table = Array('Общая информация');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Date/Extended',$ServiceOrder['OrderDate']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Дата заказа',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Contract/Number',$ServiceOrder['ContractID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/String',SPrintF('%s / %s',$Comp,$ServiceOrder['Customer']),35);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Договор',new Tag('TD',Array('class'=>'Standard'),$Comp));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/String',$ServiceOrder['ServiceName'],35);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Название услуги',new Tag('TD',Array('class'=>'Standard'),$Comp));
#-------------------------------------------------------------------------------
$ExpirationDate = $ServiceOrder['ExpirationDate'];
#-------------------------------------------------------------------------------
if($ExpirationDate){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Date/Standard',$ExpirationDate);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Дата окончания',$Comp);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ServiceOrder['DependOrderID']){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Services/Orders/SelectDependOrder',$ServiceOrder['UserID'],$ServiceOrder['ID'],$ServiceOrder['DependOrderID'],TRUE);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Относится к заказу', $Comp);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ServiceOrderFields = DB_Select('OrdersFields',Array('ID','ServiceFieldID','Value','FileName'),Array('Where'=>SPrintF('`OrderID` = %u',$ServiceOrderID)));
#-------------------------------------------------------------------------------
switch(ValueOf($ServiceOrderFields)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	$Fields = Array();
	#-------------------------------------------------------------------------------
	foreach($ServiceOrderFields as $ServiceOrderField){
		#-------------------------------------------------------------------------------
		$Value = $ServiceOrderField['Value'];
		#-------------------------------------------------------------------------------
		$ServiceField = DB_Select('ServicesFields',Array('Name','TypeID','Options'),Array('UNIQ','ID'=>$ServiceOrderField['ServiceFieldID']));
		#-------------------------------------------------------------------------------
		switch(ValueOf($ServiceField)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			#-------------------------------------------------------------------------------
			switch($ServiceField['TypeID']){
			case 'Select':
				#-------------------------------------------------------------------------------
				$Options = Explode("\n",$ServiceField['Options']);
				#-------------------------------------------------------------------------------
				if(Count($Options)){
					#-------------------------------------------------------------------------------
					$Alternatives = Array();
					#-------------------------------------------------------------------------------
					foreach($Options as $Option){
						#-------------------------------------------------------------------------------
						$Option = Explode("=",$Option);
						#-------------------------------------------------------------------------------
						$Alternatives[Current($Option)] = Next($Option);
						#-------------------------------------------------------------------------------
					}
					#-------------------------------------------------------------------------------
					if(IsSet($Alternatives[$Value]))
						$Value = $Alternatives[$Value];
					#-------------------------------------------------------------------------------
				}else{
					#-------------------------------------------------------------------------------
					$Value = 'Список выбора поля';
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			case 'File':
				#-------------------------------------------------------------------------------
				$FileName = $ServiceOrderField['FileName'];
				#-------------------------------------------------------------------------------
				if(Mb_StrLen($FileName) > 15)
					$FileName = SPrintF('%s...',Mb_SubStr($FileName,0,15));
				#-------------------------------------------------------------------------------
				$Value = new Tag('TD',Array('class'=>'Standard'),new Tag('A',Array('href'=>SPrintF('/OrderFileDownload?OrderFieldID=%u',$ServiceOrderField['ID'])),SPrintF('%s (%01.2f Кб.)',$FileName,StrLen(Base64_Decode($Value))/1024)));
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			case 'Hidden':
				break 2;
			default:
				# No more...
			}
			#-------------------------------------------------------------------------------
			$Fields[] = Array($ServiceField['Name'],$Value);
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	if(Count($Fields)){
		#-------------------------------------------------------------------------------
		$Table[] = 'Параметры заказа';
		#-------------------------------------------------------------------------------
		$Table = Array_Merge($Table,$Fields);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ServiceOrder['Address'])
	$Table[] = Array('Адрес сервера',$ServiceOrder['Address']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Прочее';
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Logic',$ServiceOrder['IsAutoProlong']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Автопродление',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Statuses/State','Orders',$ServiceOrder);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table = Array_Merge($Table,$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ServiceOrder['UserNotice'] || ($ServiceOrder['AdminNotice'] && $GLOBALS['__USER']['IsAdmin'])){
	#-------------------------------------------------------------------------------
	$Table[] = 'Примечания к заказу';
	#-------------------------------------------------------------------------------
	if($ServiceOrder['UserNotice'])
		$Table[] = Array('Примечание',new Tag('PRE',Array('class'=>'Standard','style'=>'width:260px; overflow:hidden;'),$ServiceOrder['UserNotice']));
	#-------------------------------------------------------------------------------
	if($ServiceOrder['AdminNotice'] && $GLOBALS['__USER']['IsAdmin'])
		$Table[] = Array('Примечание администратора',new Tag('PRE',Array('class'=>'Standard','style'=>'width:260px; overflow:hidden;'),$ServiceOrder['AdminNotice']));
	#-------------------------------------------------------------------------------
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
