<?php
#-------------------------------------------------------------------------------
/*<JBsDOC>
 <Target>file</Target>
 <Org>Eximius, LLC</Org>
 <Author>Alex Keda</Author>
</JBsDOC>*/
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
# выбираем все заказы
$Columns = Array(
		'ID','UserID','ServiceID','StatusDate','OrderDate',
		'(SELECT `Email` FROM `Users` WHERE `Users`.`ID` = `OrdersOwners`.`UserID`) AS `Email`',
		'(SELECT `Name` FROM `Services` WHERE `Services`.`ID` = `OrdersOwners`.`ServiceID`) AS `ServiceName`',
		'(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = `OrdersOwners`.`ServiceID`) AS `ServiceCode`',
		);
$Orders = DB_Select('OrdersOwners',$Columns,Array());
switch(ValueOf($Orders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return TRUE;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($Orders as $Order){
		#-------------------------------------------------------------------------------
		#Debug(SPrintF('[patches/hosting/files/1000010.php]: Order = %s',print_r($Order,true)));
		#-------------------------------------------------------------------------------
		# проверяем наличие такого заказа в таблице OrdersHistory
		$Count = DB_Count('OrdersHistory',Array('Where'=>Array(SPrintF('`OrderID` = %u',$Order['ID']))));
		if(Is_Error($Count))
			return ERROR | Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if($Count)
			continue;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# достаём данные по тарифному плану заказа
		if($Order['ServiceCode'] == 'Default'){
			#-------------------------------------------------------------------------------
			$Scheme = DB_Select('OrdersOwners',Array('0 AS `SchemeID`','(SELECT `NameShort` FROM `Services` WHERE `OrdersOwners`.`ServiceID`=`Services`.`ID`) AS `SchemeName`'),Array('UNIQ','ID'=>$Order['ID']));
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$Columns = Array('SchemeID',SPrintF('(SELECT `Name` FROM `%sSchemes` WHERE `%sOrdersOwners`.`SchemeID` = `%sSchemes`.`ID`) as `SchemeName`',$Order['ServiceCode'],$Order['ServiceCode'],$Order['ServiceCode']));
			#-------------------------------------------------------------------------------
			$Scheme = DB_Select(SPrintF('%sOrdersOwners',$Order['ServiceCode']),$Columns,Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Order['ID'])));
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		switch(ValueOf($Scheme)){
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
		$IOrdersHistory = Array('UserID'=>$Order['UserID'],'Email'=>$Order['Email'],'ServiceID'=>$Order['ServiceID'],'ServiceName'=>$Order['ServiceName'],'SchemeID'=>$Scheme['SchemeID'],'SchemeName'=>$Scheme['SchemeName'],'OrderID'=>$Order['ID'],'CreateDate'=>$Order['OrderDate'],'StatusDate'=>$Order['StatusDate']);
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[patches/hosting/files/1000010.php]: IOrdersHistory = %s',print_r($IOrdersHistory,true)));
		#-------------------------------------------------------------------------------
		$IsInsert = DB_Insert('OrdersHistory',$IOrdersHistory);
		if(Is_Error($IsInsert))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>
