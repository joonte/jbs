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
$Result = Array('Title'=>'Распределение доходов/нагрузки по серверам VPS');
#-------------------------------------------------------------------------------
if(!$IsCreate)
  return $Result;
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('P','Данный вид статистики содержит информацию о доходности/нагрузке каждого из имеющихся серверов VPS за 1 месяц (30 дней)'));
$NoBody->AddChild(new Tag('P','Суммируются цены за месяц тарифов всех активных заказов размещенных на сервере.'));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Graphs = Array();	# для построения графиков на выхлопе
#-------------------------------------------------------------------------------
# перебираем группы серверов, ищщем те где автобалансировка не отключена
$VPSServersGroups = DB_Select('VPSServersGroups',Array('*'));
switch(ValueOf($VPSServersGroups)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	Debug("[comp/Statistics/VPSServersIncome]: no groups found");
	break;
case 'array':
	# All OK, Servers Groups found
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($VPSServersGroups as $VPSServersGroup){
	#-------------------------------------------------------------------------------
	# выбираем сервера группы
	$Servers = DB_Select('VPSServers',Array('*'),Array('Where'=>SPrintF('`ServersGroupID` = %u',$VPSServersGroup['ID']),'SortOn'=>'Address'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Servers)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		Debug(SPrintF('[comp/Statistics/VPSServersIncome]: no servers for group %s',$VPSServersGroup['ID']));
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		$Balance = 0;
		$Accounts = 0;
		$NumPaid = 0;
		$Params = $Labels = Array();
		#-------------------------------------------------------------------------------
		$Table = Array(SPrintF('Группа серверов: %s',$VPSServersGroup['Name']));
		#-------------------------------------------------------------------------------
		$Table[] = Array(new Tag('TD',Array('class'=>'Head'),'Адрес сервера'),new Tag('TD',Array('class'=>'Head'),'Аккаунтов'),new Tag('TD',Array('class'=>'Head'),'Доход сервера'),new Tag('TD',Array('class'=>'Head'),'Доход аккаунта'),new Tag('TD',Array('class'=>'Head'),'Процессор, MHz'),new Tag('TD',Array('class'=>'Head'),'Диск, Gb'),new Tag('TD',Array('class'=>'Head'),'Память, Mb'));
		#-------------------------------------------------------------------------------
		foreach($Servers as $Server){
			#-------------------------------------------------------------------------------
			# достаём все активные аккаунты сервера
			$ServerAccounts = DB_Select('VPSOrders',Array('OrderID'),Array('Where'=>SPrintF('`ServerID` = %u AND `StatusID` = "Active"',$Server['ID'])));
			#-------------------------------------------------------------------------------
			switch(ValueOf($ServerAccounts)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				Debug(SPrintF('[comp/Statistics/VPSServersIncome]: no accounts for server %s',$Server['Address']));
				break;
			case 'array':
				# All OK, accounts found
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$Array = Array();
			#-------------------------------------------------------------------------------
			foreach($ServerAccounts as $Account)
				$Array[] = $Account['OrderID'];
			#-------------------------------------------------------------------------------
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
				Debug(SPrintF('[comp/Statistics/VPSServersIncome]: no summ for server %s',$Server['Address']));
				break;
			case 'array':
				#-------------------------------------------------------------------------------
				# считаем все не-бесплатные аккаунты, по ним будут расчёты цены и доходов
				$Count = DB_Select('OrdersConsider','DISTINCT(`OrderID`) AS `OrderID`',Array('Where'=>$Where));
				#-------------------------------------------------------------------------------
				switch(ValueOf($Count)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
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
				# достаём место на диске и оперативную память
				$Usage = DB_Select(Array('VPSOrders','VPSSchemes'),Array('CEIL(SUM(mem)) AS tmem','CEIL(SUM(ncpu * cpu)) AS tcpu','CEIL(SUM(disklimit)/1024) AS tdisk'),Array('UNIQ','Where'=>Array(SPrintF('`VPSOrders`.`ServerID` = %u',$Server['ID']),'`VPSSchemes`.`ID` = `VPSOrders`.`SchemeID`','`VPSOrders`.`StatusID` = "Active"')));
				#-------------------------------------------------------------------------------
				switch(ValueOf($Usage)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					return ERROR | @Trigger_Error(400);
				case 'array':
					# All OK, accounts found
					break;
				default:
					return ERROR | @Trigger_Error(101);
				}
				#-------------------------------------------------------------------------------
				#-------------------------------------------------------------------------------
				$NumAccounts = SizeOf($Array);
				#-------------------------------------------------------------------------------
				$AccountIncome = Comp_Load('Formats/Currency',($Income['SummRemainded'] / $Income['DaysRemainded']) * 30);# 30 дней в месяце
				if(Is_Error($AccountIncome))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$ServerIncome = Comp_Load('Formats/Currency',($Income['SummRemainded'] / $Income['DaysRemainded']) * $PaidAccounts * 30);# 30 дней в месяце
				if(Is_Error($ServerIncome))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Table[] = Array($Server['Address'],SPrintF('%s / %s',$NumAccounts,$PaidAccounts),$ServerIncome,$AccountIncome,$Usage['tmem'],$Usage['tdisk'],$Usage['tmem']);
				#-------------------------------------------------------------------------------
				#-------------------------------------------------------------------------------
				$Params[] = ($Income['SummRemainded'] / $Income['DaysRemainded']) * $PaidAccounts * 30;
				$Labels[] = $Server['Address'];
				#-------------------------------------------------------------------------------
				$Balance += ($Income['SummRemainded'] / $Income['DaysRemainded']) * $PaidAccounts * 30;
				$Accounts+= $NumAccounts;
				$NumPaid += $PaidAccounts;
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Formats/Currency',$Balance);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#----------------------------------------------------------------------------
		$Table[] = Array(new Tag('TD',Array('colspan'=>7,'class'=>'Standard'),SPrintF('Общий доход от серверов группы: %s',$Comp)));
		#----------------------------------------------------------------------------
		# средняя стоимость аккаунта
		$Comp = Comp_Load('Formats/Currency',$Balance / $NumPaid);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#----------------------------------------------------------------------------
		$Table[] = Array(new Tag('TD',Array('colspan'=>7,'class'=>'Standard'),SPrintF('Средняя цена аккаунта в группе: %s',$Comp)));
		#----------------------------------------------------------------------------
		break;
		#----------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Tables/Extended',$Table);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#----------------------------------------------------------------------------
	$NoBody->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Graphs[$VPSServersGroup['ID']] = Array('Name'=>$VPSServersGroup['Name'],'Balance'=>$Balance,'NumPaid'=>$NumPaid,'Accounts'=>$Accounts,'Params'=>$Params,'Labels'=>$Labels);
	#----------------------------------------------------------------------------
	$NoBody->AddChild(new Tag('BR'));
	#----------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# строим графики, считаем суммы
$Balance = 0;
$Accounts = 0;
$NumPaid = 0;
#-------------------------------------------------------------------------------
foreach($VPSServersGroups as $VPSServersGroup){
	#-------------------------------------------------------------------------------
	if(IsSet($Graphs[$VPSServersGroup['ID']])){
		#-------------------------------------------------------------------------------
		$Balance += $Graphs[$VPSServersGroup['ID']]['Balance'];
		#-------------------------------------------------------------------------------
		$Accounts+= $Graphs[$VPSServersGroup['ID']]['Accounts'];
		#-------------------------------------------------------------------------------
		$NumPaid += $Graphs[$VPSServersGroup['ID']]['NumPaid'];
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
foreach($VPSServersGroups as $VPSServersGroup){
	#-------------------------------------------------------------------------------
	if(IsSet($Graphs[$VPSServersGroup['ID']])){
		#-------------------------------------------------------------------------------
		if(Count($Graphs[$VPSServersGroup['ID']]['Params']) > 1){
			#-------------------------------------------------------------------------
			$File = SPrintF('%s.jpg',Md5('VPSIncome_fin' . $VPSServersGroup['ID']));
			#-------------------------------------------------------------------------
			Artichow_Pie(SPrintF('Доходы группы %s',$Graphs[$VPSServersGroup['ID']]['Name']),SPrintF('%s/%s',$Folder,$File),$Graphs[$VPSServersGroup['ID']]['Params'],$Graphs[$VPSServersGroup['ID']]['Labels']);
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
?>
