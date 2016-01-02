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
$Details       =   (boolean) @$Args['Details'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#$CacheID = Md5($GLOBALS['__USER']['ID'] . $__FILE__);
#-------------------------------------------------------------------------------
#$Result = CacheManager::get($CacheID);
#if($Result)
#  return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/HTMLDoc.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
$Settings = $Config['Invoices']['PaymentSystems'];
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
$DOM->AddText('Title','Дополнения → Статистика → Платёжные системы');
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
# выбираем максимальную и минимальную даты из таблицы платежей
# это будет начало и конец периода по которому будем отображать статистику
$Columns = Array('MIN(`StatusDate`) AS `DateFirst`','MAX(`StatusDate`) AS `DateLast`');
$Where = Array("`StatusID` = 'Payed'");
#-------------------------------------------------------------------------------
if(!$Details)
	$Where[] = SPrintF('`StatusDate` > %u', MkTime (0,0,0,1,1,Date("Y")));
#-------------------------------------------------------------------------------
$Dates = DB_Select('Invoices',$Columns,Array('UNIQ', 'Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($Dates)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	Debug("[comp/www/Administrator/PaymentsSystemsStatistics]: DateFirst is " . $Dates['DateFirst'] . ", DateLast is " . $Dates['DateLast']);
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем последнюю дату - если она ноль - то выходим, нет счетов
if(IntVal($Dates['DateLast']) < 1){
	#-------------------------------------------------------------------------------
	$Table = Array('Оплаченных счетов не найдено');
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Tables/Extended',$Table);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$Comp);
	$Out = $DOM->Build();
	#-------------------------------------------------------------------------------
	return $Out;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# считаем за этот месяц
$Table = Array(SPrintF('Статистика по дням, за последнюю неделю [%s]',Date('Y-m-d G:i:s',Time())));
#-------------------------------------------------------------------------------
$LastDayOfMonth = Date('d', MkTime(0, 0, 0, Date('m',Time()) + 1, 0, Date('Y',Time())));
#-------------------------------------------------------------------------------
$Days = Array();
#-------------------------------------------------------------------------------
$Summs = Array();
#-------------------------------------------------------------------------------
for ($day = Date('d',time()) - 7; $day <= Date('d',time()); $day++){
	#-------------------------------------------------------------------------------
	$TimeBegin = MkTime(0, 0, 0, Date('m',time()), $day, Date('Y',time()));
	#-------------------------------------------------------------------------------
	$TimeEnd   = MkTime(23, 59, 59, Date('m',time()), $day, Date('Y',time()));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# SQL
	$Columns = Array('SUM(`Summ`) AS `Summ`');
	#-------------------------------------------------------------------------------
	$Where = Array("`StatusID` = 'Payed'",SprintF('`StatusDate` BETWEEN %s AND %s',$TimeBegin,$TimeEnd));
	#$Where = "`StatusID` = 'Payed' AND `StatusDate` BETWEEN $TimeBegin AND $TimeEnd";
	#-------------------------------------------------------------------------------
	$Total = DB_Select('Invoices',$Columns,Array('UNIQ', 'Where'=>$Where));
	switch(ValueOf($Total)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#return ERROR | @Trigger_Error(400);
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/www/Administrator/PaymentsSystemsStatistics]: общая сумма за %s = %s',$day,$Total['Summ']));
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	# заполняем массивы
	if(IntVal($Total['Summ']) == 0){$Total['Summ'] = 0;}
	#-------------------------------------------------------------------------------
	$Days[] = Date('Y',$TimeBegin) . "-" . Date('m',$TimeBegin) . "-" . Date('d',$TimeBegin);
	#-------------------------------------------------------------------------------
	$Summs[] = Number_Format($Total['Summ'],2,'.',' ');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Table[] = $Days;
#-------------------------------------------------------------------------------
$Table[] = $Summs;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Extended',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
for ($Year = date('Y',$Dates['DateLast']); date('Y',$Dates['DateFirst']) <= $Year; $Year--){
	#-------------------------------------------------------------------------------
	$CacheID = Md5($__FILE__ . $Year);
	#-------------------------------------------------------------------------------
	$Result = CacheManager::get($CacheID);
	if($Result && $Year != Date('Y', Time())){
		#-------------------------------------------------------------------------------
		$DOM->AddChild('Into',$Result);
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$Table = Array();
		#-------------------------------------------------------------------------------
		$TimeBegin = MkTime(0, 0, 0, 1, 1, $Year);
		$TimeEnd   = MkTime(23, 59, 59, 12, 31, $Year);
		Debug("[comp/www/Administrator/PaymentsSystemsStatistics]: period is " . date('Y-m-d G:i:s' , $TimeBegin) . " -> " . date('Y-m-d G:i:s' , $TimeEnd));
		#-------------------------------------------------------------------------------
		# выбираем типы платёжных систем по которым были платежи в указанный период времени
		$Columns = Array('DISTINCT(`PaymentSystemID`) AS `PaymentSystemID`','SUM(`Summ`) AS `Summ`');
		$Where = "`StatusID` = 'Payed' AND `StatusDate` BETWEEN $TimeBegin AND $TimeEnd";
		$PaymentSystems = DB_Select('Invoices',$Columns,Array('Where'=>$Where,'SortOn'=>'Summ','IsDesc'=>TRUE,'GroupBy'=>'PaymentSystemID'));
		switch(ValueOf($PaymentSystems)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			#return ERROR | @Trigger_Error(400);
			Debug("[comp/www/Administrator/PaymentsSystemsStatistics]: нет платёжных систем");
			break;
		case 'array':
			Debug("[comp/www/Administrator/PaymentsSystemsStatistics]: num payment systems is " . SizeOf($PaymentSystems));
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		$Tr = new Tag('TR');
		$Tr->AddChild(new Tag('TD',Array('class'=>'Separator','colspan'=>(SizeOf($PaymentSystems)) + 3),new Tag('SPAN','Статистика за ' . $Year . ' год')));
		$Table[] = $Tr;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Tr = new Tag('TR');
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Formats/String','Дата',6);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		$Tr->AddChild(new Tag('TD',Array('class'=>'Head','align'=>'center'),$Comp));
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Formats/String','Всего оплачено',6);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		$Tr->AddChild(new Tag('TD',Array('class'=>'Head','align'=>'center'),$Comp));
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Formats/String','Среднее, в день',6);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		$Tr->AddChild(new Tag('TD',Array('class'=>'Head','align'=>'center'),$Comp));
		#-------------------------------------------------------------------------------
		foreach($PaymentSystems as $PaymentSystem){
			$SystemCode = $PaymentSystem['PaymentSystemID'];
			$Comp = Comp_Load('Formats/String',$Settings[$SystemCode]['Name'],6);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			$Tr->AddChild(new Tag('TD',Array('class'=>'Head','align'=>'center','style'=>'white-space: nowrap;'),$Comp));
		}
		$Table[] = $Tr;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Months = Array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
		foreach($Months as $Month){
			#-------------------------------------------------------------------------------
			$CacheID1 = Md5( $__FILE__ . $Year . $Month);
			#-------------------------------------------------------------------------------
			$Result = CacheManager::get($CacheID1);
			if($Result && $Year != Date('Y', Time()) && $Month != Date('m', Time())){
				$Table[] = $Result;
			}else{
				Debug("[comp/www/Administrator/PaymentsSystemsStatistics]: period is $Year $Month");
				$TimeBegin = MkTime(0, 0, 0, $Month, 1, $Year);
				$LastDayOfMonth = date('d', mktime(0, 0, 0, $Month + 1, 0, $Year));
				$TimeEnd   = MkTime(23, 59, 59, $Month, $LastDayOfMonth, $Year);
				#-------------------------------------------------------------------------------
				$Tr = new Tag('TR');
				#-------------------------------------------------------------------------------
				# ячейка с датой
				$Tr->AddChild(new Tag('TD',Array('align'=>'center','class'=>'Standard'),$Year . "-" . $Month . "-" . $LastDayOfMonth));
				#-------------------------------------------------------------------------------
				# ячейка с общей суммой за месяц
				$Columns = Array('SUM(`Summ`) AS `Summ`');
				$Where = "`StatusID` = 'Payed' AND `StatusDate` BETWEEN $TimeBegin AND $TimeEnd";
			        $Total = DB_Select('Invoices',$Columns,Array('UNIQ', 'Where'=>$Where));
				switch(ValueOf($Total)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					return ERROR | @Trigger_Error(400);
				case 'array':
					Debug("[comp/www/Administrator/PaymentsSystemsStatistics]: общая сумма за $Year/$Month = " . $Total['Summ']);
					break;
				default:
					return ERROR | @Trigger_Error(101);
				}
				#$TableLine[] = FloatVal($Total['Summ']);
				$Tr->AddChild(new Tag('TD',Array('align'=>'right','class'=>'Standard','style'=>'background-color:#FDF6D3;'),Number_Format($Total['Summ'],2,'.',' ')));
				#-------------------------------------------------------------------------------
				# ячейка со средним за месяц
				# если это текущий год и текущий месяц - то расчёт будет иным
				if(Date('Y', Time()) == $Year && Date('m', Time()) == $Month){
					$AvgVal = Round((FloatVal($Total['Summ']) / Date('d', Time())),2);
				}else{
					$AvgVal = Round((FloatVal($Total['Summ']) / $LastDayOfMonth),2);
				}
				$Tr->AddChild(new Tag('TD',Array('align'=>'right','class'=>'Standard','style'=>'background-color:#B9CCDF;'),Number_Format($AvgVal,2,'.',' ')));
				#-------------------------------------------------------------------------------
				# перебираем все платёжные системы, считаем для них суммы
				foreach($PaymentSystems as $PaymentSystem){
					#-------------------------------------------------------------------------------
					$Columns = Array('SUM(`Summ`) AS `Summ`');
					#-------------------------------------------------------------------------------
					$Where = "`StatusID` = 'Payed' AND `PaymentSystemID` = '" . $PaymentSystem['PaymentSystemID'] . "' AND `StatusDate` BETWEEN $TimeBegin AND $TimeEnd";
					#-------------------------------------------------------------------------------
					$Summ = DB_Select('Invoices',$Columns,Array('UNIQ', 'Where'=>$Where));
					#-------------------------------------------------------------------------------
					switch(ValueOf($Summ)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						return ERROR | @Trigger_Error(400);
					case 'array':
						Debug("[comp/www/Administrator/PaymentsSystemsStatistics]: сумма для " . $PaymentSystem['PaymentSystemID'] . " за $Year/$Month = " . $Summ['Summ']);
						break;
					default:
						return ERROR | @Trigger_Error(101);
					}
					#-------------------------------------------------------------------------------
					$Tr->AddChild(new Tag('TD',Array('align'=>'right','class'=>'Standard'),Number_Format($Summ['Summ'],2,'.',' ')));
				}
				#-------------------------------------------------------------------------------
				# если общая сумма больше нуля - добавляем строку - иначе - смысла нет
				if(IntVal($Total['Summ']) > 0){
					#$Table[] = $TableLine;
					$Table[] = $Tr;
					CacheManager::add($CacheID1, $Tr, 24 * 3600);
				}
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		# полная статистика за год
		$Tr = new Tag('TR');
		#-------------------------------------------------------------------------------
		# ячейка с датой
		$Tr->AddChild(new Tag('TD',Array('align'=>'center','class'=>'Standard'),'Итого:'));
		#-------------------------------------------------------------------------------
		$TimeBegin = MkTime(0, 0, 0, 1, 1, $Year);
		$TimeEnd   = MkTime(23, 59, 59, 12, 31, $Year);
		#-------------------------------------------------------------------------------
		# ячейка с общей суммой за месяц
		$Columns = Array('SUM(`Summ`) AS `Summ`');
		$Where = "`StatusID` = 'Payed' AND `StatusDate` BETWEEN $TimeBegin AND $TimeEnd";
		$Total = DB_Select('Invoices',$Columns,Array('UNIQ', 'Where'=>$Where));
		switch(ValueOf($Total)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			Debug("[comp/www/Administrator/PaymentsSystemsStatistics]: общая сумма за $Year = " . $Total['Summ']);
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		$Tr->AddChild(new Tag('TD',Array('align'=>'right','class'=>'Standard','style'=>'background-color:#FDF6D3;'),new Tag('NOBR',Number_Format($Total['Summ'],2,'.',' '))));
		#-------------------------------------------------------------------------------
		# ячейка со средним за год
		# если это текущий год - то расчёт будет иным
		if(Date('Y', Time()) == $Year){
			$AvgVal = Round((FloatVal($Total['Summ']) / (Date('z', Time()) + 1)),2);
		}else{
			$AvgVal = Round((FloatVal($Total['Summ']) / (Date('z', $TimeEnd) + 1)),2);
		}
		$Tr->AddChild(new Tag('TD',Array('align'=>'right','class'=>'Standard','style'=>'background-color:#B9CCDF;'),new Tag('NOBR',Number_Format($AvgVal,2,'.',' '))));
		#-------------------------------------------------------------------------------
		# перебираем все платёжные системы, считаем для них суммы
		foreach($PaymentSystems as $PaymentSystem){
			$Columns = Array('SUM(`Summ`) AS `Summ`');
			$Where = "`StatusID` = 'Payed' AND `PaymentSystemID` = '" . $PaymentSystem['PaymentSystemID'] . "' AND `StatusDate` BETWEEN $TimeBegin AND $TimeEnd";
			$Summ = DB_Select('Invoices',$Columns,Array('UNIQ', 'Where'=>$Where));
			switch(ValueOf($Total)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				Debug("[comp/www/Administrator/PaymentsSystemsStatistics]: сумма для " . $PaymentSystem['PaymentSystemID'] . " за $Year = " . $Summ['Summ']);
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			$Tr->AddChild(new Tag('TD',Array('align'=>'right','class'=>'Standard'),new Tag('NOBR',Number_Format($Summ['Summ'],2,'.',' '))));
		}
		#-------------------------------------------------------------------------------
		# добавляем строку в таблицу
		$Table[] = $Tr;
		#Debug(print_r($Table, true));
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Tables/Extended',$Table);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		CacheManager::add($CacheID, $Comp, 24 * 3600);       # cache it to 1 day
		#-------------------------------------------------------------------------------
		$DOM->AddChild('Into',$Comp);
	}
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Details){
	#-------------------------------------------------------------------------------
	$Form = new Tag('FORM',Array('action'=>'/Administrator/PaymentsSystemsStatistics','method'=>'POST','onsubmit'=>'return false;'));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'hidden','name'=>'Details','value'=>'yes'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>'form.submit();','value'=>'Отобразить за все годы','prompt'=>'Показать статистику за весь период работы биллинговой системы'));
	#-------------------------------------------------------------------------------
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$Form);
	#-------------------------------------------------------------------------------
}
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
