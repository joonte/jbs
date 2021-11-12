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
$IsCreate       = (boolean) @$Args['IsCreate'];
$StartDate      = (integer) @$Args['StartDate'];
$FinishDate     = (integer) @$Args['FinishDate'];
$Details        =   (array) @$Args['Details'];
$ShowTables     = (boolean) @$Args['ShowTables'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Result = Array('Title'=>'Распределение доходов/заказов на VPS по тарифам');
#-------------------------------------------------------------------------------
if(!$IsCreate)
	return $Result;
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('P','Данный вид статистики содержит информацию о доходности/нагрузке каждого из имеющихся тарифов VPS за 1 месяц (30 дней)'));
$NoBody->AddChild(new Tag('P','Суммируются цены за месяц, всех активных заказов тарифа.'));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# перебираем группы серверов, ищщем те где автобалансировка не отключена
$VPSSchemes = DB_Select('VPSSchemes',Array('ID','Name'),Array('SortOn'=>'SortID'));
switch(ValueOf($VPSSchemes)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	Debug("[comp/Statistics/VPSSchemesIncome]: hosting shemes not found");
	return $Result;
case 'array':
	# All OK, Servers Groups found
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// для построения графиков на выхлопе
$dGraphs = $bGraphs = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table = Array(Array(new Tag('TD',Array('class'=>'Head'),'Наименование тарифа'),new Tag('TD',Array('class'=>'Head'),'Кол-во заказов'),new Tag('TD',Array('class'=>'Head'),'Доход')));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($VPSSchemes as $VPSScheme){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Statistics/VPSSchemesIncome]: processing scheme "%s"',$VPSScheme['Name']));
	#-------------------------------------------------------------------------------
	# достаём все активные аккаунты тарифа
	$SchemeAccounts = DB_Select('VPSOrders',Array('OrderID'),Array('Where'=>SPrintF('`SchemeID` = %u AND `StatusID` = "Active"',$VPSScheme['ID'])));
	#-------------------------------------------------------------------------------
	switch(ValueOf($SchemeAccounts)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Statistics/VPSSchemesIncome]: scheme "%s" not have active accounts',$VPSScheme['Name']));
		#-------------------------------------------------------------------------------
		$SchemeIncome = Comp_Load('Formats/Currency',0);
		if(Is_Error($SchemeIncome))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array($VPSScheme['Name'],'0 / 0',$SchemeIncome);
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	case 'array':
		# All OK, accounts found
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Array = Array();
		#-------------------------------------------------------------------------------
		foreach($SchemeAccounts as $Account)
			$Array[] = $Account['OrderID'];
			#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# считаем сумму всех оплаченных дней тарифа
		$Where = Array('`DaysRemainded` > 0',SPrintF('`OrderID` IN (%s)',Implode(',',$Array)));
		#-------------------------------------------------------------------------------
		$Incomes = DB_Select('OrdersConsider',Array('SUM(`DaysRemainded`*`Cost`*(1-`Discont`))/SUM(`DaysRemainded`) as `CostDay`'),Array('Where'=>$Where,'GroupBy'=>'OrderID'));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Incomes)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/Statistics/VPSSchemesIncome]: no summ for scheme "%s"',$VPSScheme['Name']));
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		case 'array':
			#-------------------------------------------------------------------------------
			// изначально, всё по нулям
			$PaidAccounts = $SchemeIncome = $AccountIncome = 0;
			#-------------------------------------------------------------------------------
			// перебираем аккаунты, считаем сумму дохода всего тарифа в ДЕНЬ, стоимость одного аккаунта, количество платных аккаунтов
			foreach($Incomes as $Income){
				#-------------------------------------------------------------------------------
				// если стомость аккаунта больше нуля, считаем как оплаченный
				if($Income['CostDay'] > 0)
					$PaidAccounts++;
				#-------------------------------------------------------------------------------
				$SchemeIncome = $SchemeIncome + $Income['CostDay'];
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			// доход сервера
			$SchemeIncome = $SchemeIncome * 30;             # 30 дней в месяце
			// доход одного аккаунта (если платные вообще есть, иначе - по нулям)
			$AccountIncome = ($PaidAccounts > 0)?$SchemeIncome / $PaidAccounts:0; # только по платным аккаунтам
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
                        // для диаграмм
			if($SchemeIncome > 0){
				#-------------------------------------------------------------------------------
				// прибыль с тарифа
				$bGraphs[] = Array($VPSScheme['Name'],$SchemeIncome);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			// общее число активных акааунтов
			$dGraphs[] = Array($VPSScheme['Name'],SizeOf($Array));
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$SchemeIncome = Comp_Load('Formats/Currency',$SchemeIncome);
			if(Is_Error($SchemeIncome))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Table[] = Array($VPSScheme['Name'],SPrintF('%s / %s',SizeOf($Array)/* num accounts */,$PaidAccounts),$SchemeIncome);
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
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
$Comp = Comp_Load('Tables/Extended',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($ShowTables)
	$NoBody->AddChild(new Tag('DIV',Array('style'=>'float:left;'),$Comp));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(SizeOf($dGraphs) > 1){
	#-------------------------------------------------------------------------------
	$Graphs = Array('Количество заказов VPS, по тарифам'=>$dGraphs,'Доход тарифных планов VPS'=>$bGraphs);
	#-------------------------------------------------------------------------------
	$Pie = Comp_Load('Charts/Pie',$Graphs);
	if(Is_Error($Pie))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	// накидываем DIV'ы в тело страницы
	foreach($Pie['FnNames'] as $FnName)
		$NoBody->AddChild(new Tag('DIV',Array('style'=>SPrintF('float:left;width:%u%%;height:400px;',$ShowTables?30:50),'id'=>SPrintF('div_%s',$FnName)),$FnName));
	#-------------------------------------------------------------------------------
	$Result['Script'] = $Pie['Script'];
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Result['DOM'] = $NoBody;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Result;
#-------------------------------------------------------------------------------
?>
