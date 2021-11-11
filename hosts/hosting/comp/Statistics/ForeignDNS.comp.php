<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('IsCreate','Folder','StartDate','FinishDate','Details');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/Artichow.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Result = Array('Title'=>'Распределение доменов по серверам DNS');
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('P','Данный вид статистики содержит информацию о используемых DNS серверах конкурентов'));
#-------------------------------------------------------------------------------
if(!$IsCreate)
	return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Servers = DB_Select('Servers',Array('ID','Params'),Array('Where'=>'(SELECT `ServiceID` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `Servers`.`ServersGroupID`) = 20000'));
#-------------------------------------------------------------------------------
switch(ValueOf($Servers)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $Result;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# JBS-1080: выбираем все статусы для доменов, с подсчётом количества
$DomainOrders = DB_Select('DomainOrders',Array('DISTINCT(`StatusID`) AS `StatusID`','COUNT(*) AS `Count`'),Array('GroupBy'=>'StatusID','SortOn'=>'Count'));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $Result;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Statuses = $Config['Statuses']['DomainOrders'];
#-------------------------------------------------------------------------------
$Total = 0;
$Graphs = Array('Домены по статусу'=>Array());
#-------------------------------------------------------------------------------
$Table = Array(Array(new Tag('TD',Array('class'=>'Head'),'Статус'),new Tag('TD',Array('class'=>'Head'),'Кол-во')));
#-------------------------------------------------------------------------------
foreach($DomainOrders as $DomainOrder){
	#-------------------------------------------------------------------------------
	$Table[] = Array(IsSet($Statuses[$DomainOrder['StatusID']])?$Statuses[$DomainOrder['StatusID']]['Name']:$DomainOrder['StatusID'],$DomainOrder['Count']);
	#-------------------------------------------------------------------------------
	$Graphs['Домены по статусу'][] =  Array(IsSet($Statuses[$DomainOrder['StatusID']])?$Statuses[$DomainOrder['StatusID']]['Name']:$DomainOrder['StatusID'],$DomainOrder['Count']);
	#-------------------------------------------------------------------------------
	// общее число доменов
	$Total += $DomainOrder['Count'];
	#-------------------------------------------------------------------------------
	// число активных, для подсчёта активных не с нашими ДНС серверами
	if($DomainOrder['StatusID'] == 'Active')
		$CountActive = $DomainOrder['Count'];
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('TD',Array('class'=>'Head'),'Всего'),new Tag('TD',Array('class'=>'Head'),$Total));
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Extended',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('DIV',Array('style'=>'float:left;'),$Comp));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// график для наших/не наших ДНС
$Where = Array();
#-------------------------------------------------------------------------------
foreach($Servers as $Server)
	$Where[] = '`Ns1Name` NOT LIKE "%' . SubStr($Server['Params']['Ns1Name'], StrPos($Server['Params']['Ns1Name'], '.') + 1, StrLen($Server['Params']['Ns1Name'])) . '%"';
#-------------------------------------------------------------------------------
$Where[] = '`Ns1Name` != ""';
$Where[] = '`StatusID` = "Active"';
$Where[] = '`Ns1Name` NOT LIKE CONCAT ("%",`DomainName`,".",`Name`)';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# число активных доменов с ненашими ДНС серверами
if(IsSet($CountActive) && $CountActive > 0){
	#-------------------------------------------------------------------------------
	$Where1 = $Where;
	#-------------------------------------------------------------------------------
	$Where1[] = '`StatusID` = "Active"';
	#-------------------------------------------------------------------------------
	$Count = DB_Count('DomainOrdersOwners',Array('Where'=>$Where1));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Graphs['Наши/чужие ДНС, для активных доменов'] = Array(Array('Наши DNS',$CountActive - $Count),Array('Чужие DNS',$Count));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// таблица и график для распределения по чужим ДНС серверам
$Columns = Array(
		'SUBSTRING_INDEX(`Ns1Name`, ".", -2) AS Address',
		'COUNT(*) AS Count',
		);
#-------------------------------------------------------------------------------
$DNSs = DB_Select('DomainOrdersOwners',$Columns,Array('Where'=>$Where,'SortOn'=>'Count','IsDesc'=>TRUE,'GroupBy'=>'Address'));
#-------------------------------------------------------------------------------
switch(ValueOf($DNSs)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $Result;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Count = $i = 0 ;
#-------------------------------------------------------------------------------
$Graphs['Распределение по чужим ДНС, активные'] = Array();
#-------------------------------------------------------------------------------
$Table = Array(Array(new Tag('TD',Array('class'=>'Head'),'Провайдер'),new Tag('TD',Array('class'=>'Head'),'Кол-во доменов')));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($DNSs as $DNS){
	#-------------------------------------------------------------------------------
	$Graphs['Распределение по чужим ДНС, активные'][] = Array($DNS['Address'],$DNS['Count']);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Table[] = Array($DNS['Address'],(integer)$DNS['Count']);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Extended',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('DIV',Array('style'=>'float:left;'),$Comp));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// рисуем диаграммы
$Pie = Comp_Load('Charts/Pie',$Graphs);
if(Is_Error($Pie))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
// накидываем DIV'ы в тело страницы
foreach($Pie['FnNames'] as $FnName)
	$NoBody->AddChild(new Tag('DIV',Array('style'=>'float:left;width:30%;height:400px;','id'=>SPrintF('div_%s',$FnName)),$FnName));
#-------------------------------------------------------------------------------
$Result['Script'] = $Pie['Script'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Result['DOM'] = $NoBody;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
