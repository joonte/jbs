<?php


#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$CacheID = Md5($__FILE__ . $GLOBALS['__USER']['ID']);
#-------------------------------------------------------------------------------
$Result = CacheManager::get($CacheID);
if($Result) {
    return $Result;
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Base')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddAttribs('MenuLeft',Array('args'=>'User/Office'));
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Мой офис → Статистика начислений');
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tab','User/PartnerProgramm',$NoBody);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Comp);
#-------------------------------------------------------------------------------
# не работает <GroupBy>Year,Month</GroupBy>
# надо думать ... 
if(TRUE)
{
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# выбираем список рефералов
$Referalls = DB_Select('Users',Array('ID'),Array('Where'=>SPrintF('`OwnerID`=%u',$GLOBALS['__USER']['ID'])));
switch(ValueOf($Referalls)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# нет рефералов
	$Span = new Tag('SPAN','У вас ещё нет рефералов.');
	$NoBody->AddChild($Span);
	break;
case 'array':
	#-------------------------------------------------------------------------------
	# есть рефералы
	$TableName = SPrintF('InvoicesOwners%s',UniqID($GLOBALS['__USER']['ID']));
	$Array = Array();
	#-------------------------------------------------------------------------------
	foreach($Referalls as $Referall)
		$Array[] = (integer)$Referall['ID'];
	#-------------------------------------------------------------------------------
	$ReferallsIDs = Implode(',',$Array);
	$Result = DB_Query(SPrintF("CREATE TEMPORARY TABLE `%s` SELECT * FROM `InvoicesOwners` WHERE `StatusID`='Payed' AND `UserID` IN (%s);",$TableName,$ReferallsIDs));
	if(Is_Error($Result))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Columns = Array(
				"FROM_UNIXTIME(`StatusDate`,'%Y') AS `Year`",
				"FROM_UNIXTIME(`StatusDate`,'%m') AS `Month`",
				'COUNT(DISTINCT(`UserID`)) AS `NumUsers`',
				'COUNT(*) AS `NumPayments`',
				'SUM(`Summ`) `MonthSum`',
				//SPrintF('ROUND(SUM(`Summ`) * %u / 100, 2) AS `MonthSum`',$Percent),
			);
	$Payments = DB_Select($TableName,$Columns,Array('GroupBy'=>Array('Year','Month')));
	switch(ValueOf($Payments)){
        case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# нет платежей от рефералов
		$Span = new Tag('SPAN','Ваши рефералы не оплачивали никаких услуг.');
		$NoBody->AddChild($Span);
		break;
	case 'array':
		$Table = Array(
				Array(
					new Tag('TD',Array('class'=>'Head'),'Год'),
					new Tag('TD',Array('class'=>'Head'),'Месяц'),
					new Tag('TD',Array('class'=>'Head'),'Пользователей'),
					new Tag('TD',Array('class'=>'Head'),'Платежей'),
					new Tag('TD',Array('class'=>'Head'),'Сумма')));
		#-------------------------------------------------------------------------
		foreach($Payments as $Payment)
			$Table[] = $Payment;
		#-------------------------------------------------------------------------
		$Comp = Comp_Load('Tables/Extended',$Table,Array('width'=>'600px;'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------
		$NoBody->AddChild($Comp);

		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	break;
default:
	return ERROR | @Trigger_Error(101);
}

}else{

$Comp = Comp_Load('Tables/Super','DependUsersStatistic');
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
  #-------------------------------------------------------------------------------
  $DOM->AddChild('Into',$Comp);

}
#-------------------------------------------------------------------------------
$Out = $DOM->Build();
#-------------------------------------------------------------------------------
if(Is_Error($Out))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
CacheManager::add($CacheID, $Out, 24 * 3600);  # cache it to 24 hour
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------

?>
