<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
$Config = Config();
$Settings = $Config['Tasks']['Types']['NoticeDelete'];
#-------------------------------------------------------------------------------
# достаём время выполнения
$ExecuteTime = Comp_Load('Formats/Task/ExecuteTime',Array('ExecuteTime'=>$Settings['ExecuteTime'],'ExecuteDays'=>@$Settings['ExecuteDays'],'DefaultTime'=>MkTime(4,25,0,Date('n'),Date('j')+1,Date('Y'))));
if(Is_Error($ExecuteTime))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
# если неактивна, то через день запуск
if(!$Settings['IsActive'])
	return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = Array();
#-------------------------------------------------------------------------------
$Where = Array('`Code` != "Default"','`IsHidden` = "no"');
#-------------------------------------------------------------------------------
$Services = DB_Select('Services',Array('ID','Code','Name'),Array('Where'=>$Where));
switch(ValueOf($Services)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo'][] = 'no services for delete notice';
	#-------------------------------------------------------------------------------
	return $ExecuteTime;
	#-------------------------------------------------------------------------------
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($Services as $Service){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/NoticeDelete]: Service = %s',$Service['Code']));
	#-------------------------------------------------------------------------------
	#if($Service['Code'] != 'Domain')
	#	continue;
	#-------------------------------------------------------------------------------
	$Columns = Array(
			'*',
			SPrintF('(SELECT `Balance` FROM `Contracts` WHERE `%sOrdersOwners`.`ContractID` = `ID`) AS `Balance`',$Service['Code']),
			SPrintF('(SELECT `Name` FROM `%sSchemes` WHERE `%sOrdersOwners`.`SchemeID` = `ID`) AS `SchemeName`',$Service['Code'],$Service['Code']),
			SPrintF('(SELECT `IsProlong` FROM `%sSchemes` WHERE `%sOrdersOwners`.`SchemeID` = `ID`) AS `IsProlong`',$Service['Code'],$Service['Code']),
			);
	#-------------------------------------------------------------------------------
	$Where = "`StatusID` = 'Suspended' AND ROUND((UNIX_TIMESTAMP() - `StatusDate`)/86400) IN (2,3,6,11,16,21,31,41,51,61,71,101)";
	#-------------------------------------------------------------------------------
	if($Service['Code'] == 'Domain'){
		#-------------------------------------------------------------------------------
		$Columns[] = '(SELECT `Name` FROM `DomainSchemes` WHERE `DomainSchemes`.`ID` = `DomainOrdersOwners`.`SchemeID`) AS `DomainZone`';
		$Columns[] = '(SELECT `CostProlong` FROM `DomainSchemes` WHERE `DomainOrdersOwners`.`SchemeID` = `ID`) AS `Cost`';
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		// добавляем выборку ценника за месяц
		$Columns[] = SPrintF('(SELECT `CostMonth` FROM `%sSchemes` WHERE `%sOrdersOwners`.`SchemeID` = `ID`) AS `Cost`',$Service['Code'],$Service['Code']);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Orders = DB_Select(SPrintF('%sOrdersOwners',$Service['Code']),$Columns,Array('Where'=>$Where));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Orders)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
		continue 2;
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo'][$Service['Code']] = Array(SizeOf($Orders));
	#-------------------------------------------------------------------------------
	foreach($Orders as $Order){
		#-------------------------------------------------------------------------------
		$Balance = Comp_Load('Formats/Currency',$Order['Balance']);
		if(Is_Error($Balance))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Order['Balance'] = $Balance;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Cost = Comp_Load('Formats/Currency',$Order['Cost']);
		if(Is_Error($Cost))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Order['Cost'] = $Cost;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// ссылка на продление заказа
		$Ajax = SPrintF("ShowWindow('/%sOrderPay',{%sOrderID:'%s'});",$Service['Code'],$Service['Code'],$Order['ID']);
		#-------------------------------------------------------------------------------
		$ProlongLink = Comp_Load('Formats/System/EvalLink',$Ajax);
		if(Is_Error($ProlongLink))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Order['ProlongLink'] = $ProlongLink;
		#-------------------------------------------------------------------------------
		// ссылка на смену тарифа
		$Ajax = SPrintF("ShowWindow('/%sOrderSchemeChange',{%sOrderID:'%s'});",$Service['Code'],$Service['Code'],$Order['ID']);
		#-------------------------------------------------------------------------------
		$SchemeChangeLink = Comp_Load('Formats/System/EvalLink',$Ajax);
		if(Is_Error($SchemeChangeLink))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Order['SchemeChangeLink'] = $SchemeChangeLink;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$msg = new Message(SPrintF('%sNoticeDelete',$Service['Code']),(integer)$Order['UserID'],Array(SPrintF('%sOrder',$Service['Code'])=>$Order));
		#-------------------------------------------------------------------------------
		$IsSend = NotificationManager::sendMsg($msg);
		#-------------------------------------------------------------------------------
		switch(ValueOf($IsSend)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			# No more...
		case 'true':
			# No more...
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
