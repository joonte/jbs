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
$Result = Array('Title'=>'Распределение доходов по серверам');
#-------------------------------------------------------------------------------
if(!$IsCreate)
  return $Result;
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('P','Данный вид статистики содержит информацию о доходности каждого из имеющихся серверов за 1 месяц (30 дней)'));
$NoBody->AddChild(new Tag('P','Суммируются цены за месяц тарифов всех активных заказов размещенных на сервере.'));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Graphs = Array();	# для построения графиков на выхлопе
#-------------------------------------------------------------------------------
# перебираем группы серверов
$ServersGroups = DB_Select('ServersGroups',Array('*'),Array('SortOn'=>'SortID'));
switch(ValueOf($ServersGroups)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	Debug("[comp/Statistics/ServersIncome]: no groups found");
	break;
case 'array':
	# All OK, Servers Groups found
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($ServersGroups as $ServersGroup){
	#-------------------------------------------------------------------------------
	#if($ServersGroup['ServiceID'] != 20000)
	#	continue;
	#-------------------------------------------------------------------------------
	# выбираем сервера группы
	$Servers = DB_Select('Servers',Array('*'),Array('Where'=>SPrintF('`ServersGroupID` = %u',$ServersGroup['ID']),'SortOn'=>'Address'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Servers)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Statistics/ServersIncome]: no servers for group %s',$ServersGroup['ID']));
		#-------------------------------------------------------------------------------
		continue 2;
		#-------------------------------------------------------------------------------
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Balance = $Accounts = $NumPaid = 0;
	#-------------------------------------------------------------------------------
	$Params = $Labels = Array();
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Table = Array(SPrintF('Группа серверов: %s',$ServersGroup['Name']));
	#-------------------------------------------------------------------------------
	$Table[] = Array(new Tag('TD',Array('class'=>'Head'),'Адрес сервера'),new Tag('TD',Array('class'=>'Head'),'Аккаунтов (всего/платно)'),new Tag('TD',Array('class'=>'Head'),'Доход сервера'),new Tag('TD',Array('class'=>'Head'),'Доход аккаунта')/*,new Tag('TD',Array('class'=>'Head'),'Диск, Gb'),new Tag('TD',Array('class'=>'Head'),'Память, Mb')*/);
	#-------------------------------------------------------------------------------
	foreach($Servers as $Server){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Statistics/ServersIncome]: Address = %s',$Server['Address']));
		#-------------------------------------------------------------------------------
		# достаём все активные аккаунты сервера
		$ServerAccounts = DB_Select('Orders',Array('ID'),Array('Where'=>SPrintF('`ServerID` = %u AND `StatusID` = "Active"',$Server['ID'])));
		#-------------------------------------------------------------------------------
		switch(ValueOf($ServerAccounts)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			Debug(SPrintF('[comp/Statistics/ServersIncome]: no accounts for server %s',$Server['Address']));
			break;
		case 'array':
			# All OK, accounts found
			Debug(SPrintF('[comp/Statistics/ServersIncome]: server %s, found %u accounts',$Server['Address'],SizeOf($ServerAccounts)));
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		$Array = Array();
		#-------------------------------------------------------------------------------
		foreach($ServerAccounts as $Account)
			$Array[] = $Account['ID'];
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
                if($ServersGroup['ServiceID'] == 20000){
			#-------------------------------------------------------------------------------
			# домены обсчитываем отдельно.
			#-------------------------------------------------------------------------------
			#DaysRemainded - всегда 365?
			# выбираем
			$Income = DB_Select('DomainOrders',Array('SUM((SELECT `CostOrder` FROM `DomainSchemes` WHERE `DomainSchemes`.`ID` = `DomainOrders`.`SchemeID`)) AS `SummRemainded`'),Array('UNIQ','Where'=>SPrintF('`OrderID` IN (%s)',Implode(',',$Array))));
			#-------------------------------------------------------------------------------
			switch(ValueOf($Income)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				Debug(SPrintF('[comp/Statistics/ServersIncome]: no summ for registrator %s',$Server['Address']));
				continue 2;
			case 'array':
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			$PaidAccounts = SizeOf($Array);
			#-------------------------------------------------------------------------------
			$Income['DaysRemainded'] = $PaidAccounts * 365;
			#Debug(SPrintF('[comp/Statistics/ServersIncome]: Income = %s',print_r($Income,true)));
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			# считаем сумму всех оплаченных дней сервера
			$Where = Array('`DaysRemainded` > 0','`Discont` < 1','`Cost` > 0',SPrintF('`OrderID` IN (%s)',Implode(',',$Array)));
			#-------------------------------------------------------------------------------
			$Income = DB_Select('OrdersConsider',Array('SUM(`DaysRemainded`*`Cost`*(1-`Discont`)) as `SummRemainded`','SUM(`DaysRemainded`) AS `DaysRemainded`'),Array('UNIQ','Where'=>$Where));
			#-------------------------------------------------------------------------------
			switch(ValueOf($Income)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				Debug(SPrintF('[comp/Statistics/ServersIncome]: no summ for server %s',$Server['Address']));
				continue 2;
			case 'array':
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			#Debug(SPrintF('[comp/Statistics/ServersIncome]: Income = %s',print_r($Income,true)));
			#-------------------------------------------------------------------------------
			# считаем все не-бесплатные аккаунты, по ним будут расчёты цены и доходов
			$Count = DB_Select('OrdersConsider','DISTINCT(`OrderID`) AS `OrderID`',Array('Where'=>$Where));
			#-------------------------------------------------------------------------------
			switch(ValueOf($Count)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				#-------------------------------------------------------------------------------
				# однако, дальше на это число делится - поэтому ноль нельзя
				continue 3;
				#-------------------------------------------------------------------------------
				$PaidAccounts = 0;
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			case 'array':
				#-------------------------------------------------------------------------------
				# All OK, accounts found
				$PaidAccounts = SizeOf($Count);
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
		#Debug("[comp/Statistics/ServersIncome]: before calculate");
		$NumAccounts = SizeOf($Array);
		#-------------------------------------------------------------------------------
		$AccountIncome = Comp_Load('Formats/Currency',($Income['SummRemainded'] / $Income['DaysRemainded']) * 30);# 30 дней в месяце
		if(Is_Error($AccountIncome))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#Debug("[comp/Statistics/ServersIncome]: debug - 1");
		$ServerIncome = Comp_Load('Formats/Currency',($Income['SummRemainded'] / $Income['DaysRemainded']) * $PaidAccounts * 30);# 30 дней в месяце
		if(Is_Error($ServerIncome))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#Debug("[comp/Statistics/ServersIncome]: debug - 2");
		$Table[] = Array($Server['Address'],SPrintF('%s / %s',$NumAccounts,$PaidAccounts),$ServerIncome,$AccountIncome/*,$Usage['tdisk'],$Usage['tmem']*/);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		#Debug("[comp/Statistics/ServersIncome]: debug - 3");
		$Params[] = ($Income['SummRemainded'] / $Income['DaysRemainded']) * $PaidAccounts * 30;
		$Labels[] = $Server['Address'];
		#-------------------------------------------------------------------------------
		#Debug("[comp/Statistics/ServersIncome]: debug - 4");
		$Balance += ($Income['SummRemainded'] / $Income['DaysRemainded']) * $PaidAccounts * 30;
		$Accounts+= $NumAccounts;
		$NumPaid += $PaidAccounts;
		#-------------------------------------------------------------------------------
		#Debug("[comp/Statistics/ServersIncome]: cycle complete");
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Currency',$Balance);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#----------------------------------------------------------------------------
	$Table[] = Array(new Tag('TD',Array('colspan'=>5,'class'=>'Standard'),SPrintF('Общий доход от серверов группы: %s',$Comp)));
	#----------------------------------------------------------------------------
	# средняя стоимость аккаунта
	$Comp = Comp_Load('Formats/Currency',$Balance / $NumPaid);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#----------------------------------------------------------------------------
	$Table[] = Array(new Tag('TD',Array('colspan'=>5,'class'=>'Standard'),SPrintF('Средняя цена аккаунта в группе: %s',$Comp)));
	#----------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Tables/Extended',$Table);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#----------------------------------------------------------------------------
	$NoBody->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Graphs[$ServersGroup['ID']] = Array('Name'=>$ServersGroup['Name'],'Balance'=>$Balance,'NumPaid'=>$NumPaid,'Accounts'=>$Accounts,'Params'=>$Params,'Labels'=>$Labels);
	#----------------------------------------------------------------------------
	$NoBody->AddChild(new Tag('BR'));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# строим графики, считаем суммы
$Balance = 0;
$Accounts = 0;
$NumPaid = 0;
#-------------------------------------------------------------------------------
foreach($ServersGroups as $ServersGroup){
	#-------------------------------------------------------------------------------
	if(IsSet($Graphs[$ServersGroup['ID']])){
		#-------------------------------------------------------------------------------
		$Balance += $Graphs[$ServersGroup['ID']]['Balance'];
		#-------------------------------------------------------------------------------
		$Accounts+= $Graphs[$ServersGroup['ID']]['Accounts'];
		#-------------------------------------------------------------------------------
		$NumPaid += $Graphs[$ServersGroup['ID']]['NumPaid'];
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Currency',$Balance);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('SPAN',SPrintF('Доход от всех серверов: %s',$Comp)));
$NoBody->AddChild(new Tag('BR'));
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('SPAN',SPrintF('Число активных аккаунтов: %s',$Accounts)));
$NoBody->AddChild(new Tag('BR'));
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('SPAN',SPrintF('Число активных платных аккаунтов: %s',$NumPaid)));
$NoBody->AddChild(new Tag('BR'));

$NoBody->AddChild(new Tag('BR'));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($ServersGroups as $ServersGroup){
	#-------------------------------------------------------------------------------
	if(IsSet($Graphs[$ServersGroup['ID']])){
		#-------------------------------------------------------------------------------
		if(Count($Graphs[$ServersGroup['ID']]['Params']) > 1){
			#-------------------------------------------------------------------------
			$File = SPrintF('%s.jpg',Md5('Income_fin' . $ServersGroup['ID']));
			#-------------------------------------------------------------------------
			Artichow_Pie(SPrintF('Доходы группы %s',$Graphs[$ServersGroup['ID']]['Name']),SPrintF('%s/%s',$Folder,$File),$Graphs[$ServersGroup['ID']]['Params'],$Graphs[$ServersGroup['ID']]['Labels']);
			#-------------------------------------------------------------------------
			#$NoBody->AddChild(new Tag('BR'));
			#-------------------------------------------------------------------------
			$NoBody->AddChild(new Tag('IMG',Array('src'=>$File)));
			#-------------------------------------------------------------------------
			#$NoBody->AddChild(new Tag('BR'));
			#-------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Result['DOM'] = $NoBody;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
