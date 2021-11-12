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
$IsCreate	= (boolean) @$Args['IsCreate'];
$StartDate	= (integer) @$Args['StartDate'];
$FinishDate	= (integer) @$Args['FinishDate'];
$Details	=   (array) @$Args['Details'];
$ShowTables	= (boolean) @$Args['ShowTables'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$PaymentSystems = $Config['Invoices']['PaymentSystems'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('P','Данный вид статистики содержит информацию о количестве и сумме оплаченных счетов за выбранный период времени'));
$NoBody->AddChild(new Tag('P','под суммой, в данном случае, подразумевается сумма всех оплаченных счетов.'));
#-------------------------------------------------------------------------------
$Result = Array('Title'=>'Оплаченные счета*');
#-------------------------------------------------------------------------------
$MonthsNames = Array('Декабрь','Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
#-------------------------------------------------------------------------------
if(!$IsCreate)
	return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// выхлоп с графиками
$Graphs = Array(
		#-------------------------------------------------------------------------------
		// по дням
		'ByDaysPay'	=> Array(
					'Columns'	=> Array(
									Array('string','Дата'),		// даты
									Array('number','Платежи'),	// платежи
								),
					'Title'		=> 'Суммы оплаченных счетов, по дням',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Оплачено, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByDaysCount'	=> Array(
					'Columns'	=> Array(
									Array('string','Дата'),		// даты
									Array('number','Платежей'),	// платежи
								),
					'Title'		=> 'Количество оплаченных счетов, по дням',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Оплачено счетов, шт.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByDaysPayAvg'	=> Array(
					'Columns'	=> Array(
									Array('string','Дата'),		// даты
									Array('number','Средний чек'),	// платежи
								),
					'Title'		=> 'Средний чек, по дням',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Средний чек, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		// по месяцам
		'ByMonthPay'	=> Array(
					'Columns'	=> Array(
									Array('string','Год/месяц'),	// месяцы
									Array('number','Платежи'),	// платежи
								),
					'Title'		=> 'Суммы оплаченных счетов, по месяцам',
					'hAxisTitle'	=> 'Год/месяц',
					'vAxisTitle'	=> 'Оплачено, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByMonthCount'	=> Array(
					'Columns'	=> Array(
									Array('string','Год/месяц'),	// месяцы
									Array('number','Платежей'),	// платежи
								),
					'Title'		=> 'Количество оплаченных счетов, по месяцам',
					'hAxisTitle'	=> 'Год/месяц',
					'vAxisTitle'	=> 'Оплачено счетов, шт.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByMonthPayAvg'	=> Array(
					'Columns'	=> Array(
									Array('string','Год/месяц'),	// месяцы
									Array('number','Средний чек'),	// платежи
								),
					'Title'		=> 'Средний чек, по месяцам',
					'hAxisTitle'	=> 'Год/месяц',
					'vAxisTitle'	=> 'Средний чек, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		// по месяцам и платёжным системам
		'ByMonthPayPS'	=> Array(
					'Columns'	=> Array(
									Array('string','Год/месяц'),	// месяцы
								),
					'Title'		=> 'Суммы оплаченных счетов, по месяцам и платёжным системам',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Оплачено, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByMonthCountPS'	=> Array(
					'Columns'	=> Array(
									Array('string','Год/месяц'),	// месяцы
								),
					'Title'		=> 'Количество оплаченных счетов, по месяцам и платёжным системам',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Оплачено счетов, шт.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByMonthAvgPS'	=> Array(
					'Columns'	=> Array(
									Array('string','Год/месяц'),	// месяцы
								),
					'Title'		=> 'Средний чек, по месяцам и платёжным системам',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Средний чек, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = Array(
		SPrintF('`StatusDate` >= %u',$StartDate),
		SPrintF('`StatusDate` <= %u',$FinishDate),
		'`IsPosted` = "yes"'
		);
#-------------------------------------------------------------------------------
if(In_Array('ByDays',$Details)){
	#-------------------------------------------------------------------------------
	$Invoices = DB_Select('Invoices',Array('DATE_FORMAT(FROM_UNIXTIME(`StatusDate`),GET_FORMAT(DATE,"ISO")) AS `ISO_Date`', 'GET_DAY_FROM_TIMESTAMP(`StatusDate`) as `Date`','SUM(`Summ`) as `Summ`','COUNT(*) as `Count`'),Array('GroupBy'=>'Date','Where'=>$Where,'SortOn'=>'StatusDate'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Invoices)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		$Table = Array(Array(new Tag('TD',Array('class'=>'Head'),'День'),new Tag('TD',Array('class'=>'Head'),'Сумма'),new Tag('TD',Array('class'=>'Head'),'Кол-во')));
		#-------------------------------------------------------------------------------
		$CurrentMonth = 0;
		#-------------------------------------------------------------------------------
		foreach($Invoices as $Invoice){
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			// график по сумме платежей
			$Graphs['ByDaysPay']['Data'][] = Array($Invoice['ISO_Date'],Ceil($Invoice['Summ']));
			#-------------------------------------------------------------------------------
			// график по числу платежей
			$Graphs['ByDaysCount']['Data'][] = Array($Invoice['ISO_Date'],Ceil($Invoice['Count']));
			#-------------------------------------------------------------------------------
			// график среднего чека
			$Graphs['ByDaysPayAvg']['Data'][] = Array($Invoice['ISO_Date'],Ceil($Invoice['Summ']/$Invoice['Count']));
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$CurrentYear = Date('Y',$Invoice['Date']*86400);
			#-------------------------------------------------------------------------------
			if(Date('n',$Invoice['Date']*86400) != $CurrentMonth){
				#-------------------------------------------------------------------------------
				$Label[] = Date('j.m.Y',$Invoice['Date']*86400);
				#-------------------------------------------------------------------------------
				$CurrentMonth = Date('n',$Invoice['Date']*86400);
				#-------------------------------------------------------------------------------
				$Table[] = SPrintF('%s %u г.',$MonthsNames[Date('n',$Invoice['Date']*86400)],Date('Y',$Invoice['Date']*86400));
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			$Summ = Comp_Load('Formats/Currency',$Invoice['Summ']);
			if(Is_Error($Summ))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Table[] = Array(Date('d',$Invoice['Date']*86400),$Summ,(integer)$Invoice['Count']);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$NoBody->AddChild(new Tag('H2','Суммы оплаченных счетов по дням'));
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Tables/Extended',$Table);
		#-------------------------------------------------------------------------------
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if($ShowTables)
			$NoBody->AddChild(new Tag('DIV',Array('style'=>'float:left; display:none;'),$Comp));
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(In_Array('ByMonth',$Details)){
	#-------------------------------------------------------------------------------
	$Columns = Array("DATE_FORMAT(FROM_UNIXTIME(`StatusDate`),'%Y-%m') AS `YearMonth`",'MONTH(FROM_UNIXTIME(`StatusDate`)) as `Month`','YEAR(FROM_UNIXTIME(`StatusDate`)) as Year','SUM(`Summ`) as `Summ`','COUNT(*) as `Count`');
	#-------------------------------------------------------------------------------
	$Invoices = DB_Select('Invoices',$Columns,Array('GroupBy'=>Array('Month','Year'),'Where'=>$Where,'SortOn'=>'StatusDate'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Invoices)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		$Table = Array(Array(new Tag('TD',Array('class'=>'Head'),'Месяц'),new Tag('TD',Array('class'=>'Head'),'Сумма'),new Tag('TD',Array('class'=>'Head'),'Кол-во')));
		#-------------------------------------------------------------------------------
		$CurrentYear = 0;
		#-------------------------------------------------------------------------------
		// массив для дат (ключей), по которым будут строиться графики по платёжным системам
		$Dates = Array();
		#-------------------------------------------------------------------------------
		foreach($Invoices as $Invoice){
			#-------------------------------------------------------------------------------
			// готовим массив с датами в качестве ключей, для графиков по платёжным системам
			$Dates[$Invoice['YearMonth']] = Array();
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			// график по сумме платежей
			$Graphs['ByMonthPay']['Data'][] = Array(SPrintF('%s-%02d',$Invoice['Year'],$Invoice['Month']),Ceil($Invoice['Summ']));
			#-------------------------------------------------------------------------------
			// график по числу платежей
			$Graphs['ByMonthCount']['Data'][] = Array(SPrintF('%s-%02d',$Invoice['Year'],$Invoice['Month']),Ceil($Invoice['Count']));
			#-------------------------------------------------------------------------------
			// график среднего чека
			$Graphs['ByMonthPayAvg']['Data'][] = Array(SPrintF('%s-%02d',$Invoice['Year'],$Invoice['Month']),Ceil($Invoice['Summ']/$Invoice['Count']));
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			if($Invoice['Year'] != $CurrentYear){
				#-------------------------------------------------------------------------------
				$CurrentYear = $Invoice['Year'];
				#-------------------------------------------------------------------------------
				$Table[] = SPrintF('%u г.',$CurrentYear);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			$Summ = Comp_Load('Formats/Currency',$Invoice['Summ']);
			if(Is_Error($Summ))
				return ERROR | @Trigger_Error(500);
			#-----------------------------------------------------------------------
			$Table[] = Array($MonthsNames[$Invoice['Month']],$Summ,(integer)$Invoice['Count']);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$NoBody->AddChild(new Tag('H2','Суммы оплаченных счетов по месяцам'));
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Tables/Extended',$Table);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if($ShowTables)
			$NoBody->AddChild(new Tag('DIV',Array('style'=>'float:left; display:none;'),$Comp));
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// выбираем платёжные системы использовавшиеся в этот же период
		$PSs = DB_Select('Invoices',Array('DISTINCT(`PaymentSystemID`) AS `PaymentSystem`'),Array('Where'=>$Where));
		#-------------------------------------------------------------------------------
		switch(ValueOf($PSs)){
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
		// перебираем полученный список платёжных систем
		foreach($PSs as $PS){
			#-------------------------------------------------------------------------------
			// делаем запрос к базе, достаём платежи по конкретной платёжной системе в указанный период
			$Where1 = $Where;
			$Where1[] = SPrintF('`PaymentSystemID` = "%s"',$PS['PaymentSystem']);
			#-------------------------------------------------------------------------------
			$Invoices = DB_Select('Invoices',$Columns,Array('GroupBy'=>Array('Month','Year'),'Where'=>$Where1,'SortOn'=>'StatusDate'));
			#-------------------------------------------------------------------------------
			switch(ValueOf($Invoices)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				# No more...
				break;
			case 'array':
				#-------------------------------------------------------------------------------
				// перебираем полученный список платежей, вносим платежи в массив дат
				foreach($Invoices as $Invoice){
					#-------------------------------------------------------------------------------
					$Dates[$Invoice['YearMonth']][$PS['PaymentSystem']]['Summ'] = $Invoice['Summ'];
					#-------------------------------------------------------------------------------
					$Dates[$Invoice['YearMonth']][$PS['PaymentSystem']]['Count'] = $Invoice['Count'];
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			// некоторые платёжные системы удалены, могут быть проблемы с именем
			$PaymentSystemName = IsSet($PaymentSystems[$PS['PaymentSystem']]['Name'])?$PaymentSystems[$PS['PaymentSystem']]['Name']:$PS['PaymentSystem'];
			#-------------------------------------------------------------------------------
			// добавляем описание платёжной системы в описание колонок
			$Graphs['ByMonthPayPS']['Columns'][] = Array('number',$PaymentSystemName);
			$Graphs['ByMonthCountPS']['Columns'][] = Array('number',$PaymentSystemName);
			$Graphs['ByMonthAvgPS']['Columns'][] = Array('number',$PaymentSystemName);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		// перебираем полученный массив дат, заполняем массив для графиков
		foreach(Array_Keys($Dates) as $Key){
			#-------------------------------------------------------------------------------
			// строка таблицы графика
			$ArrayByMonthPayPS = Array($Key);
			$ArrayByMonthCountPS = Array($Key);
			$ArrayByMonthAvgPS = Array($Key);
			// перебираем платёжные системы, чтобы в том же порядке заполнить массивы
			foreach($PSs as $PS){
				#-------------------------------------------------------------------------------
				// если в этот день платёжной системой платили, то вносим цифры платежей, иначе - нули
				$Value = IsSet($Dates[$Key][$PS['PaymentSystem']])?1:0;
				#-------------------------------------------------------------------------------
				$ArrayByMonthPayPS[] = ($Value)?$Dates[$Key][$PS['PaymentSystem']]['Summ']:0;
				#-------------------------------------------------------------------------------
				$ArrayByMonthCountPS[] = ($Value)?$Dates[$Key][$PS['PaymentSystem']]['Count']:0;
				#-------------------------------------------------------------------------------
				$ArrayByMonthAvgPS[] = ($Value)?Ceil($Dates[$Key][$PS['PaymentSystem']]['Summ']/$Dates[$Key][$PS['PaymentSystem']]['Count']):0;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			// вносим значение в массив для графика
			$Graphs['ByMonthPayPS']['Data'][] = $ArrayByMonthPayPS;
			$Graphs['ByMonthCountPS']['Data'][] = $ArrayByMonthCountPS;
			$Graphs['ByMonthAvgPS']['Data'][] = $ArrayByMonthAvgPS;
			#-------------------------------------------------------------------------------
		}
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Count($NoBody->Childs) < 2)
	return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// добавляем графики в страницу
$Line = Comp_Load('Charts/Line',$Graphs);
if(Is_Error($Line))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
// накидываем DIV'ы в тело страницы
foreach($Line['FnNames'] as $FnName)
	$NoBody->AddChild(new Tag('DIV',Array('style'=>SPrintF('float:left;width:%u%%;height:400px;',$ShowTables?80:100),'id'=>SPrintF('div_%s',$FnName)),$FnName));
#-------------------------------------------------------------------------------
$Result['Script'] = $Line['Script'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Result['DOM'] = $NoBody;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
