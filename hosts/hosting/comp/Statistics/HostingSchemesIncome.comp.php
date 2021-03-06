<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('IsCreate','Folder');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/Artichow.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Result = Array('Title'=>'Распределение доходов/заказов на хостинг по тарифам');
#-------------------------------------------------------------------------------
if(!$IsCreate)
	return $Result;
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('P','Данный вид статистики содержит информацию о доходности/нагрузке каждого из имеющихся тарифов хостинга за 1 месяц (30 дней)'));
$NoBody->AddChild(new Tag('P','Суммируются цены за месяц, всех активных заказов тарифа.'));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# перебираем группы серверов, ищщем те где автобалансировка не отключена
$HostingSchemes = DB_Select('HostingSchemes',Array('*'),Array('SortOn'=>'SortID'));
switch(ValueOf($HostingSchemes)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	Debug("[comp/Statistics/HostingSchemesIncome]: hosting shemes not found");
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
$Data = $Params = $Labels = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table = Array(Array(new Tag('TD',Array('class'=>'Head'),'Наименование тарифа'),new Tag('TD',Array('class'=>'Head'),'Кол-во заказов'),new Tag('TD',Array('class'=>'Head'),'Доход')));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($HostingSchemes as $HostingScheme){
	#-------------------------------------------------------------------------------
	# достаём все активные аккаунты тарифа
	$SchemeAccounts = DB_Select('HostingOrders',Array('OrderID'),Array('Where'=>SPrintF('`SchemeID` = %u AND `StatusID` = "Active"',$HostingScheme['ID'])));
	#-------------------------------------------------------------------------------
	switch(ValueOf($SchemeAccounts)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Statistics/HostingSchemesIncome]: scheme "%s" not have active accounts',$HostingScheme['Name']));
		#-------------------------------------------------------------------------------
		$SchemeIncome = Comp_Load('Formats/Currency',0);
		if(Is_Error($SchemeIncome))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array($HostingScheme['Name'],'0 / 0',$SchemeIncome);
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
			Debug(SPrintF('[comp/Statistics/HostingSchemesIncome]: no summ for scheme "%s"',$HostingScheme['Name']));
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
				// если стомость аккаунта равна нулю, пропускаем его
				if($Income['CostDay'] == 0)
					continue;
				#-------------------------------------------------------------------------------
				$PaidAccounts++;
				#-------------------------------------------------------------------------------
				$SchemeIncome = $SchemeIncome + $Income['CostDay'];
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			// если платных аккаунтов нет - пропускаем
			#Debug(SPrintF('[comp/Statistics/HostingSchemesIncome]: SchemeIncome = %s; PaidAccounts = %s',$SchemeIncome,$PaidAccounts));
			if($PaidAccounts == 0)
				break;
			#-------------------------------------------------------------------------------
			// доход сервера
			$SchemeIncome = $SchemeIncome * 30;             # 30 дней в месяце
			// доход одного аккаунта
			$AccountIncome = $SchemeIncome / $PaidAccounts; # только по платным аккаунтам
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
                        // для диаграмм
			if($SchemeIncome > 0){
				#-------------------------------------------------------------------------------
				// ключ для последующей сортировки, так как тарифных планов много, надо выбрать первый десяток самых прибыльных
				$Key = $SchemeIncome * 10000;
				#-------------------------------------------------------------------------------
				// если ключ существует, добавляем случайное число
				if(IsSet($Data[$Key]))
					$Key = $SchemeIncome * 10000 + Rand(1,9999);
				#-------------------------------------------------------------------------------
				$Data[$Key] = Array('Params'=>$SchemeIncome,'Labels'=>$HostingScheme['Name']);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$SchemeIncome = Comp_Load('Formats/Currency',$SchemeIncome);
			if(Is_Error($SchemeIncome))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Table[] = Array($HostingScheme['Name'],SPrintF('%s / %s',SizeOf($Array)/* num accounts */,$PaidAccounts),$SchemeIncome);
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
$NoBody->AddChild($Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Count(Array_Keys($Data)) > 1){
	#-------------------------------------------------------------------------------
	KrSort($Data);
	#-------------------------------------------------------------------------------
	$Count = $i = 0 ;
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Data) as $Key){
		#-------------------------------------------------------------------------------
		// ограничиваем графики 10-ю
		if($i < 10){
			#-------------------------------------------------------------------------------
			$Params[] = $Data[$Key]['Params'];
			$Labels[] = $Data[$Key]['Labels'];
			#-------------------------------------------------------------------------------
			$i++;
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			// графа - прочие
			$Count = $Count + $Data[$Key]['Params'];
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
        }
	#-------------------------------------------------------------------------------
	// докидываем к графикам всё что больше 10
	$Params[] = $Count;
	$Labels[] = 'остальные';
	#-------------------------------------------------------------------------------
	$File = SPrintF('%s.jpg',Md5('HostingSchemes'));
	#-------------------------------------------------------------------------------
	Artichow_Pie('Распределение доходов по тарифам Hosting',SPrintF('%s/%s',$Folder,$File),$Params,$Labels);
	#-------------------------------------------------------------------------------
	$NoBody->AddChild(new Tag('BR'));
	#-------------------------------------------------------------------------------
	$NoBody->AddChild(new Tag('IMG',Array('src'=>$File)));
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
