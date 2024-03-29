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
$Words		=   (array) @$Args['Words'];
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
$Result = Array('Title'=>'Оплаченные счета');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$IsCreate)
	return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
//  группировка
$GroupBy = Array(
		'ByDays'	=> Array('Date'),
		'ByMonth'	=> Array('Month','Year'),
		'ByQuarter'	=> Array('Quarter','Year'),
		'ByYear'	=> Array('Year')
		);
#-------------------------------------------------------------------------------
// выхлоп с графиками
$Graphs = Array(
		#-------------------------------------------------------------------------------
		// по дням
		'ByDaysPay'	=> Array(
					'Columns'	=> Array(Array('string','Дата'),Array('number','Платежи')),
					'Title'		=> 'Суммы оплаченных счетов, по дням',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Оплачено, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByDaysCount'	=> Array(
					'Columns'	=> Array(Array('string','Дата'),Array('number','Платежей')),
					'Title'		=> 'Количество оплаченных счетов, по дням',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Оплачено счетов, шт.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByDaysPayAvg'	=> Array(
					'Columns'	=> Array(Array('string','Дата'),Array('number','Средний чек')),
					'Title'		=> 'Средний чек, по дням',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Средний чек, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByDaysPayPS'	=> Array(
					'Columns'	=> Array(Array('string','Дата')),
					'Title'		=> 'Суммы оплаченных счетов, по дням и платёжным системам',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Оплачено, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByDaysCountPS'	=> Array(
					'Columns'	=> Array(Array('string','Дата')),
					'Title'		=> 'Количество оплаченных счетов, по дням и платёжным системам',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Оплачено счетов, шт.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByDaysAvgPS'	=> Array(
					'Columns'	=> Array(Array('string','Дата')),
					'Title'		=> 'Средний чек, по дням и платёжным системам',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Средний чек, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		// по месяцам
		'ByMonthPay'	=> Array(
					'Columns'	=> Array(Array('string','Год/месяц'),Array('number','Платежи')),
					'Title'		=> 'Суммы оплаченных счетов, по месяцам',
					'hAxisTitle'	=> 'Год/месяц',
					'vAxisTitle'	=> 'Оплачено, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByMonthCount'	=> Array(
					'Columns'	=> Array(Array('string','Год/месяц'),Array('number','Платежей')),
					'Title'		=> 'Количество оплаченных счетов, по месяцам',
					'hAxisTitle'	=> 'Год/месяц',
					'vAxisTitle'	=> 'Оплачено счетов, шт.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByMonthPayAvg'	=> Array(
					'Columns'	=> Array(Array('string','Год/месяц'),Array('number','Средний чек')),
					'Title'		=> 'Средний чек, по месяцам',
					'hAxisTitle'	=> 'Год/месяц',
					'vAxisTitle'	=> 'Средний чек, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		// по месяцам и платёжным системам
		'ByMonthPayPS'	=> Array(
					'Columns'	=> Array(Array('string','Год/месяц')),
					'Title'		=> 'Суммы оплаченных счетов, по месяцам и платёжным системам',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Оплачено, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByMonthCountPS'	=> Array(
					'Columns'	=> Array(Array('string','Год/месяц')),
					'Title'		=> 'Количество оплаченных счетов, по месяцам и платёжным системам',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Оплачено счетов, шт.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByMonthAvgPS'	=> Array(
					'Columns'	=> Array(Array('string','Год/месяц')),
					'Title'		=> 'Средний чек, по месяцам и платёжным системам',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Средний чек, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		// по кварталам
		'ByQuarterPay'	=> Array(
					'Columns'	=> Array(Array('string','Год/квартал'),Array('number','Платежи')),
					'Title'		=> 'Суммы оплаченных счетов, по кварталам',
					'hAxisTitle'	=> 'Год/квартал',
					'vAxisTitle'	=> 'Оплачено, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByQuarterCount'=> Array(
					'Columns'	=> Array(Array('string','Год/квартал'),Array('number','Платежей')),
					'Title'		=> 'Количество оплаченных счетов, по кварталам',
					'hAxisTitle'	=> 'Год/квартал',
					'vAxisTitle'	=> 'Оплачено счетов, шт.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByQuarterPayAvg'=> Array(
					'Columns'	=> Array(Array('string','Год/квартал'),Array('number','Средний чек')),
					'Title'		=> 'Средний чек, по кварталам',
					'hAxisTitle'	=> 'Год/квартал',
					'vAxisTitle'	=> 'Средний чек, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		// по кварталам и платёжным системам
		'ByQuarterPayPS'=> Array(
					'Columns'	=> Array(Array('string','Год/квартал')),
					'Title'		=> 'Суммы оплаченных счетов, по кварталам и платёжным системам',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Оплачено, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByQuarterCountPS'=> Array(
					'Columns'	=> Array(Array('string','Год/квартал')),
					'Title'		=> 'Количество оплаченных счетов, по кварталам и платёжным системам',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Оплачено счетов, шт.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByQuarterAvgPS'=> Array(
					'Columns'	=> Array(Array('string','Год/квартал')),
					'Title'		=> 'Средний чек, по кварталам и платёжным системам',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Средний чек, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		// по годам
		'ByYearPay'	=> Array(
					'Columns'	=> Array(Array('string','Год'),Array('number','Платежи')),
					'Title'		=> 'Суммы оплаченных счетов, по годам',
					'hAxisTitle'	=> 'Год',
					'vAxisTitle'	=> 'Оплачено, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByYearCount'	=> Array(
					'Columns'	=> Array(Array('string','Год'),Array('number','Платежей')),
					'Title'		=> 'Количество оплаченных счетов, по годам',
					'hAxisTitle'	=> 'Год',
					'vAxisTitle'	=> 'Оплачено счетов, шт.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByYearPayAvg'	=> Array(
					'Columns'	=> Array(Array('string','Год'),Array('number','Средний чек')),
					'Title'		=> 'Средний чек, по годам',
					'hAxisTitle'	=> 'Год',
					'vAxisTitle'	=> 'Средний чек, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		// по кварталам и платёжным системам
		'ByYearPayPS'	=> Array(
					'Columns'	=> Array(Array('string','Год')),
					'Title'		=> 'Суммы оплаченных счетов, по годам и платёжным системам',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Оплачено, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByYearCountPS'	=> Array(
					'Columns'	=> Array(Array('string','Год')),
					'Title'		=> 'Количество оплаченных счетов, по годам и платёжным системам',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Оплачено счетов, шт.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
		'ByYearAvgPS'=> Array(
					'Columns'	=> Array(Array('string','Год')),
					'Title'		=> 'Средний чек, по годам и платёжным системам',
					'hAxisTitle'	=> 'Даты',
					'vAxisTitle'	=> 'Средний чек, руб.',
					'Data'		=> Array()
					),
		#-------------------------------------------------------------------------------
);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// колонки для выбора из БД
$Columns = Array(
		'DATE_FORMAT(FROM_UNIXTIME(`StatusDate`),GET_FORMAT(DATE,"ISO")) AS `ISO_Date`',
		"DATE_FORMAT(FROM_UNIXTIME(`StatusDate`),'%Y-%m') AS `YearMonth`",
		'GET_DAY_FROM_TIMESTAMP(`StatusDate`) as `Date`',
		'MONTH(FROM_UNIXTIME(`StatusDate`)) as `Month`',
		'GET_QUARTER_FROM_TIMESTAMP(`StatusDate`) as `Quarter`',
		'YEAR(FROM_UNIXTIME(`StatusDate`)) as Year',
		'SUM(`Summ`) as `Summ`',
		'COUNT(*) as `Count`'
		);
#-------------------------------------------------------------------------------
$Where = Array(
		SPrintF('`StatusDate` >= %u',$StartDate),
		SPrintF('`StatusDate` <= %u',$FinishDate),
		'`IsPosted` = "yes"',
		'`StatusID` = "Payed"'
		);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// перебираем детализацию, строим графики
foreach($Details as $Detail){
	#-------------------------------------------------------------------------------
	$Invoices = DB_Select('Invoices',$Columns,Array('GroupBy'=>$GroupBy[$Detail],'Where'=>$Where,'SortOn'=>'StatusDate'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Invoices)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		// массив для дат (ключей), по которым будут строиться графики по платёжным системам
		$Dates = Array();
		#-------------------------------------------------------------------------------
		foreach($Invoices as $Invoice){
			#-------------------------------------------------------------------------------
			// готовим массив с датами в качестве ключей, для графиков по платёжным системам
			if($Detail == 'ByMonth'){
				#-------------------------------------------------------------------------------
				$DateKey = $Invoice['YearMonth'];
				#-------------------------------------------------------------------------------
			}elseif($Detail == 'ByDays'){
				#-------------------------------------------------------------------------------
				$DateKey = $Invoice['ISO_Date'];
				#-------------------------------------------------------------------------------
			}elseif($Detail == 'ByQuarter'){
				#-------------------------------------------------------------------------------
				$DateKey = SPrintF('%u/%u',$Invoice['Year'],$Invoice['Quarter']);
				#-------------------------------------------------------------------------------
			}elseif($Detail == 'ByYear'){
				#-------------------------------------------------------------------------------
				$DateKey = $Invoice['Year'];
				#-------------------------------------------------------------------------------
			}
                        #-------------------------------------------------------------------------------
                        $Dates[$DateKey] = Array();
			#-------------------------------------------------------------------------------
			//Debug(SPrintF('[comp/Statistics/Invoices]: 1. DateKey = %s',$DateKey));
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			// график по сумме платежей
			$Graphs[SPrintF('%sPay',$Detail)]['Data'][] = Array($DateKey,Ceil($Invoice['Summ']));
			#-------------------------------------------------------------------------------
			// график по числу платежей
			$Graphs[SPrintF('%sCount',$Detail)]['Data'][] = Array($DateKey,Ceil($Invoice['Count']));
			#-------------------------------------------------------------------------------
			// график среднего чека
			$Graphs[SPrintF('%sPayAvg',$Detail)]['Data'][] = Array($DateKey,Ceil($Invoice['Summ']/$Invoice['Count']));
			#-------------------------------------------------------------------------------
		}
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
			$Invoices = DB_Select('Invoices',$Columns,Array('GroupBy'=>$GroupBy[$Detail],'Where'=>$Where1,'SortOn'=>'StatusDate'));
			#-------------------------------------------------------------------------------
			switch(ValueOf($Invoices)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				# No more...
				break;
			case 'array':
				// перебираем полученный список платежей, вносим платежи в массив дат
                                foreach($Invoices as $Invoice){
					#-------------------------------------------------------------------------------
					// готовим ключи
					if($Detail == 'ByMonth'){
						#-------------------------------------------------------------------------------
						$DateKey = $Invoice['YearMonth'];
						#-------------------------------------------------------------------------------
					}elseif($Detail == 'ByDays'){
						#-------------------------------------------------------------------------------
						$DateKey = $Invoice['ISO_Date'];
						#-------------------------------------------------------------------------------
					}elseif($Detail == 'ByQuarter'){
						#-------------------------------------------------------------------------------
						$DateKey = SPrintF('%u/%u',$Invoice['Year'],$Invoice['Quarter']);
						#-------------------------------------------------------------------------------
					}elseif($Detail == 'ByYear'){
						#-------------------------------------------------------------------------------
						$DateKey = $Invoice['Year'];
						#-------------------------------------------------------------------------------
					}
					#-------------------------------------------------------------------------------
					//Debug(SPrintF('[comp/Statistics/Invoices]: 2. Detail = %s; DateKey = %s',$Detail,$DateKey));
					#-------------------------------------------------------------------------------
					#-------------------------------------------------------------------------------
					$Dates[$DateKey][$PS['PaymentSystem']]['Summ'] = $Invoice['Summ'];
					#-------------------------------------------------------------------------------
					$Dates[$DateKey][$PS['PaymentSystem']]['Count'] = $Invoice['Count'];
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
			$Graphs[SPrintF('%sPayPS',$Detail)]['Columns'][] = Array('number',$PaymentSystemName);
			$Graphs[SPrintF('%sCountPS',$Detail)]['Columns'][] = Array('number',$PaymentSystemName);
			$Graphs[SPrintF('%sAvgPS',$Detail)]['Columns'][] = Array('number',$PaymentSystemName);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		// перебираем полученный массив дат, заполняем массив для графиков
		foreach(Array_Keys($Dates) as $Key){
			#-------------------------------------------------------------------------------
			// строка таблицы графика
			$ArrayPayPS = Array($Key);
			$ArrayCountPS = Array($Key);
			$ArrayAvgPS = Array($Key);
			// перебираем платёжные системы, чтобы в том же порядке заполнить массивы
			foreach($PSs as $PS){
				#-------------------------------------------------------------------------------
				// если в этот день платёжной системой платили, то вносим цифры платежей, иначе - нули
				$Value = IsSet($Dates[$Key][$PS['PaymentSystem']])?1:0;
				#-------------------------------------------------------------------------------
				$ArrayPayPS[] = ($Value)?$Dates[$Key][$PS['PaymentSystem']]['Summ']:0;
				#-------------------------------------------------------------------------------
				$ArrayCountPS[] = ($Value)?$Dates[$Key][$PS['PaymentSystem']]['Count']:0;
				#-------------------------------------------------------------------------------
				$ArrayAvgPS[] = ($Value)?Ceil($Dates[$Key][$PS['PaymentSystem']]['Summ']/$Dates[$Key][$PS['PaymentSystem']]['Count']):0;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			// вносим значение в массив для графика
			$Graphs[SPrintF('%sPayPS',$Detail)]['Data'][] = $ArrayPayPS;
			$Graphs[SPrintF('%sCountPS',$Detail)]['Data'][] = $ArrayCountPS;
			$Graphs[SPrintF('%sAvgPS',$Detail)]['Data'][] = $ArrayAvgPS;
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
#if(Count($NoBody->Childs) < 2)
#	return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// добавляем графики в страницу
$Line = Comp_Load('Charts/Line',$Graphs);
if(Is_Error($Line))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
// накидываем DIV'ы в тело страницы
foreach($Line['FnNames'] as $FnName)
	$NoBody->AddChild(new Tag('DIV',Array('style'=>'float:left;width:100%;height:400px;','id'=>SPrintF('div_%s',$FnName)),$FnName));
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
