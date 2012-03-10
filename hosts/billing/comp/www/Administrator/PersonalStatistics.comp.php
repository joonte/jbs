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
$IsCreate      = (boolean) @$Args['IsCreate'];
$StartDate     = (integer) @$Args['StartDate'];
$FinishDate    = (integer) @$Args['FinishDate'];
$StatisticsIDs =   (array) @$Args['StatisticsIDs'];
$Details       =   (array) @$Args['Details'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$CacheID = Md5($__FILE__);
#-------------------------------------------------------------------------------
$Result = CacheManager::get($CacheID);
if(!Is_Error($Result))
  return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class','libs/HTMLDoc.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
#-------------------------------------------------------------------------------
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Base')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddAttribs('MenuLeft',Array('args'=>'Administrator/AddIns'));
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Дополнения → Статистика → Статистика персонала');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tab','Administrator/Statistic',$NoBody);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

if(!$IsCreate){
	# выводим выбор временных интервалов
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# периоды времени...
	$Table = Array();
	#-------------------------------------------------------------------------------
	$Table[] = 'Период формирования';
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('jQuery/DatePicker','StartDate',MkTime(0,0,0,Date('n'),Date('j'),Date('Y')-1));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Начальная дата',$Comp);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('jQuery/DatePicker','FinishDate',Time());
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Конечная дата',$Comp);
	#-------------------------------------------------------------------------------
#	$Table[] = 'Уровень детализации';
#	#-------------------------------------------------------------------------------
#	$Input = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'Details[]','value'=>'ByDays'));
#	if(Is_Error($Input))
#		return ERROR | @Trigger_Error(500);
#	#-------------------------------------------------------------------------------
#	$Table[] = Array('По дням',$Input);
#	#-------------------------------------------------------------------------------
#	$Input = Comp_Load('Form/Input',Array('type'=>'checkbox','checked'=>'true','name'=>'Details[]','value'=>'ByMonth'));
#	if(Is_Error($Input))
#		return ERROR | @Trigger_Error(500);
#	#-------------------------------------------------------------------------------
#	$Table[] = Array('По месяцам',$Input);
#	#-------------------------------------------------------------------------------
#	$Input = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'Details[]','value'=>'ByQuarter'));
#	if(Is_Error($Input))
#		return ERROR | @Trigger_Error(500);
#	#-------------------------------------------------------------------------------
#	$Table[] = Array('По кварталам',$Input);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>'form.submit();','value'=>'Сформировать'));
	#-------------------------------------------------------------------------------
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = $Comp;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Tables/Standard',$Table);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Form = new Tag('FORM',Array('action'=>'/Administrator/PersonalStatistics','method'=>'POST','onsubmit'=>'return false;'),$Comp);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'hidden','name'=>'IsCreate','value'=>'yes'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$Form);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Out = $DOM->Build();
	#-------------------------------------------------------------------------------
	return $Out;
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# получаем список всех сотрудников
$Entrance = Tree_Entrance('Groups',3000000);
#-------------------------------------------------------------------
switch(ValueOf($Entrance)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#---------------------------------------------------------------
	$String = Implode(',',$Entrance);
	#---------------------------------------------------------------
	$Employers = DB_Select('Users','ID',Array('Where'=>SPrintF('`GroupID` IN (%s)',$String)));
	#---------------------------------------------------------------
	switch(ValueOf($Employers)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		Debug(SPrintF("[comp/www/Administrator/PersonalStatistics]: найдено %s сотрудников",SizeOf($Employers)));
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#if(In_Array('ByDays',$Details)){
#}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# строим шапочку таблицы
$Span = new Tag('SPAN','Статистика за период с ' . Date('Y-m-d',$StartDate) . ' по ' . Date('Y-m-d',$FinishDate));
$Td = new Tag('TD',Array('class'=>'Separator','colspan'=>7),$Span);
$Tr = new Tag('TR');
$Tr->AddChild($Td);
$Table[] = $Tr;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Tr = new Tag('TR');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/String','Сотрудник',20);
if(Is_Error($Comp))
return ERROR | @Trigger_Error(500);
$Tr->AddChild(new Tag('TD',Array('class'=>'Head','align'=>'center'),$Comp));
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/String','Email',20);
if(Is_Error($Comp))
return ERROR | @Trigger_Error(500);
$Tr->AddChild(new Tag('TD',Array('class'=>'Head','align'=>'center'),$Comp));
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/String','HTTP запросов к биллингу',8);
if(Is_Error($Comp))
return ERROR | @Trigger_Error(500);
$Tr->AddChild(new Tag('TD',Array('class'=>'Head','align'=>'center'),$Comp));
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/String','Назначенных тикетов',8);
if(Is_Error($Comp))
return ERROR | @Trigger_Error(500);
$Tr->AddChild(new Tag('TD',Array('class'=>'Head','align'=>'center'),$Comp));
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/String','Ответов в тикеты',8);
if(Is_Error($Comp))
return ERROR | @Trigger_Error(500);
$Tr->AddChild(new Tag('TD',Array('class'=>'Head','align'=>'center'),$Comp));
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/String','Сообщений с оценкой',8);
if(Is_Error($Comp))
return ERROR | @Trigger_Error(500);
$Tr->AddChild(new Tag('TD',Array('class'=>'Head','align'=>'center'),$Comp));
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/String','Средний балл оценок ответов',8);
if(Is_Error($Comp))
return ERROR | @Trigger_Error(500);
$Tr->AddChild(new Tag('TD',Array('class'=>'Head','align'=>'center'),$Comp));
#-------------------------------------------------------------------------------
$Table[] = $Tr;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($Employers as $Employer){
	if($Employer['ID'] > 2000 || $Employer['ID'] == 100){
		#-------------------------------------------------------------------------------
		Debug(SPrintF("[comp/www/Administrator/PersonalStatistics]: Построение данных для сотрудника #%s",$Employer['ID']));
		#-------------------------------------------------------------------------------
		$Tr = new Tag('TR');
		# Имя сотрудника / мыло сотрудника
		$Employee = DB_Select('Users',Array('Name','Email'),Array('UNIQ', 'ID'=>$Employer['ID']));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Employee)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			$Tr->AddChild(new Tag('TD',Array('align'=>'left','class'=>'Standard','style'=>'background-color:#FDF6D3;'),$Employee['Name']));
			$Tr->AddChild(new Tag('TD',Array('align'=>'left','class'=>'Standard','style'=>'background-color:#B9CCDF;'),$Employee['Email']));
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# число запросов по HTTP к биллингу
		$Where	= Array();
		$Where[]= SPrintF('`UserID` = %u',$Employer['ID']);
		$Where[]= SPrintF('`CreateDate` BETWEEN %u AND %u',$StartDate,$FinishDate);
		#-------------------------------------------------------------------------------
		$HttpQuery = DB_Select('RequestLog',Array('COUNT(*) AS Counter'),Array('UNIQ','Where'=>$Where));
		switch(ValueOf($HttpQuery)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			$Tr->AddChild(new Tag('TD',Array('align'=>'right','class'=>'Standard'),$HttpQuery['Counter']));
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# Число назначенных тикетов
		$Where  = Array();
		$Where[]= SPrintF('`TargetUserID` = %u',$Employer['ID']);
		$Where[]= SPrintF('`CreateDate` BETWEEN %u AND %u',$StartDate,$FinishDate);
		$Tickets = DB_Select('EdesksOwners',Array('COUNT(*) AS Counter'),Array('UNIQ','Where'=>$Where));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Tickets)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			$Tr->AddChild(new Tag('TD',Array('align'=>'right','class'=>'Standard'),$Tickets['Counter']));
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# число ответов в тикетнице
                $Where  = Array();
		$Where[]= '`EdesksMessages`.`EdeskID` = `Edesks`.`ID`';
		$Where[]= SPrintF('`Edesks`.`TargetUserID` = %u',$Employer['ID']);
		$Where[]= SPrintF('`EdesksMessages`.`CreateDate` BETWEEN %u AND %u',$StartDate,$FinishDate);
		$Answers = DB_Select(Array('EdesksMessages','Edesks'),Array('COUNT(*) AS Counter'),Array('UNIQ','Where'=>$Where));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Answers)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			$Tr->AddChild(new Tag('TD',Array('align'=>'right','class'=>'Standard'),$Answers['Counter']));
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# сообщений с оценками
                $Where  = Array();
		$Where[]= '`EdesksMessages`.`EdeskID` = `Edesks`.`ID`';
		$Where[]= SPrintF('`Edesks`.`TargetUserID` = %u',$Employer['ID']);
		$Where[]= '`EdesksMessages`.`VoteBall` > 0';
		$Where[]= SPrintF('`EdesksMessages`.`CreateDate` BETWEEN %u AND %u',$StartDate,$FinishDate);
		$NumVotes = DB_Select(Array('EdesksMessages','Edesks'),Array('COUNT(*) AS Counter'),Array('UNIQ','Where'=>$Where));
		#-------------------------------------------------------------------------------
		switch(ValueOf($NumVotes)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			Debug(SPrintF("[comp/www/Administrator/PersonalStatistics]: Число голосовавших: %u",$NumVotes['Counter']));
			if(IntVal($NumVotes['Counter']) > 0){
				$VoteNum = $NumVotes['Counter'];
			}else{
				$VoteNum = '-';
			}
			$Tr->AddChild(new Tag('TD',Array('align'=>'right','class'=>'Standard'),$VoteNum));
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# средний балл оценки в тикетнице
                $Where  = Array();
		$Where[]= '`EdesksMessages`.`EdeskID` = `Edesks`.`ID`';
		$Where[]= SPrintF('`Edesks`.`TargetUserID` = %u',$Employer['ID']);
		$Where[]= '`EdesksMessages`.`VoteBall` > 0';
		$Where[]= SPrintF('`EdesksMessages`.`CreateDate` BETWEEN %u AND %u',$StartDate,$FinishDate);
		$SumVotes = DB_Select(Array('EdesksMessages','Edesks'),Array('SUM(`EdesksMessages`.`VoteBall`) AS VoteSumm'),Array('UNIQ','Where'=>$Where));
		#-------------------------------------------------------------------------------
		switch(ValueOf($NumVotes)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			Debug(SPrintF("[comp/www/Administrator/PersonalStatistics]: Сумма баллов: %u",$SumVotes['VoteSumm']));
			if(IntVal($NumVotes['Counter']) > 0){
				$VoteAvg = Round(($SumVotes['VoteSumm'] / $NumVotes['Counter']),2);
			}else{
				$VoteAvg = '-';
			}
			$Tr->AddChild(new Tag('TD',Array('align'=>'right','class'=>'Standard'),$VoteAvg));
			#$Tr->AddChild(new Tag('TD',Array('align'=>'right','class'=>'Standard'),IntVal($SumVotes['VoteSumm'])));
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		
		#-------------------------------------------------------------------------------
		$Table[] = $Tr;
	}
}



$Comp = Comp_Load('Tables/Extended',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Comp);

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Out = $DOM->Build();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#CacheManager::add($CacheID, $Out, 3600);	# cache it to 1 hour
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------

?>
