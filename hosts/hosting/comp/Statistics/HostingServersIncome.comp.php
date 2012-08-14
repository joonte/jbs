<?php


#-------------------------------------------------------------------------------
/** @author Лапшин С.М. (Joonte Ltd.) */
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
$Result = Array('Title'=>'Распределение доходов/нагрузки по серверам хостинга');
#-------------------------------------------------------------------------------
if(!$IsCreate)
  return $Result;
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('P','Данный вид статистики содержит информацию о доходности/нагрузке каждого из имеющихся серверов хостинга за 1 мес.'));
$NoBody->AddChild(new Tag('P','Суммируются цены за месяц тарифов всех активных заказов размещенных на сервере.'));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Graphs = Array();	# для построения графиков на выхлопе
#-------------------------------------------------------------------------------
# перебираем группы серверов, ищщем те где автобалансировка не отключена
$HostingServersGroups = DB_Select('HostingServersGroups',Array('*'));
switch(ValueOf($HostingServersGroups)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	Debug("[comp/Statistics/HostingServersIncome]: no groups found");
	break;
case 'array':
	# All OK, servers found
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
foreach($HostingServersGroups as $HostingServersGroup){
#-------------------------------------------------------------------------------
  $Tables = Array('HostingServers','HostingOrders','HostingSchemes');
  $Columns = Array('COUNT(*) AS NumAcounts','SUM(`CostDay`*`MinDaysPay`) as `Income`','Address','CEIL(SUM(QuotaMEM)) AS tmem','CEIL(SUM(QuotaDisk)/1024) AS tdisk');
  $Condition = Array(
			'Where'	 =>Array(
					SPrintF('`HostingServers`.`ServersGroupID`=%u',$HostingServersGroup['ID']),
					'`HostingSchemes`.`ID` = `HostingOrders`.`SchemeID`',
					'`HostingServers`.`ID` = `HostingOrders`.`ServerID`',
					'`HostingOrders`.`StatusID`="Active"'
					),
			'GroupBy'=>'ServerID',
			'SortOn' =>'Address'
		);
  #-------------------------------------------------------------------------------
  $Incomes = DB_Select($Tables,$Columns,$Condition);
  #-------------------------------------------------------------------------------
  switch(ValueOf($Incomes)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return $Result;
    case 'array':
     #----------------------------------------------------------------------------
     $Balance = 0;
     $Accounts = 0;
     #----------------------------------------------------------------------------
     $Params = $Labels = Array();
     #----------------------------------------------------------------------------
     $Table = Array(Array(new Tag('TD',Array('class'=>'Transparent','colspan'=>7),SPrintF('Группа серверов: %s',$HostingServersGroup['Name']))));
     $Table[] = Array(new Tag('TD',Array('class'=>'Head'),'Адрес сервера'),new Tag('TD',Array('class'=>'Head'),'Аккаунтов'),new Tag('TD',Array('class'=>'Head'),'Доход сервера'),new Tag('TD',Array('class'=>'Head'),'Доход аккаунта'),new Tag('TD',Array('class'=>'Head'),'Диск, Gb'),new Tag('TD',Array('class'=>'Head'),'Память, Mb'));
     #----------------------------------------------------------------------------
     foreach($Incomes as $Income){
       #--------------------------------------------------------------------------
       $Balance += $Income['Income'];
       $Accounts+= $Income['NumAcounts'];
       #--------------------------------------------------------------------------
       $Params[]	= $Income['Income'];
       $Labels[]	= $Income['Address'];
       #--------------------------------------------------------------------------
       $Summ = Comp_Load('Formats/Currency',$Income['Income']);
       if(Is_Error($Summ))
         return ERROR | @Trigger_Error(500);
       #--------------------------------------------------------------------------
       $AccountPrice = Comp_Load('Formats/Currency',$Income['Income'] / $Income['NumAcounts']);
       if(Is_Error($AccountPrice))
         return ERROR | @Trigger_Error(500);
       #--------------------------------------------------------------------------
       $Table[] = Array($Income['Address'],$Income['NumAcounts'],$Summ,$AccountPrice,$Income['tdisk'],$Income['tmem']);
     }
     #----------------------------------------------------------------------------
     $Comp = Comp_Load('Formats/Currency',$Balance);
     if(Is_Error($Comp))
       return ERROR | @Trigger_Error(500);
     #----------------------------------------------------------------------------
     $Table[] = Array(new Tag('TD',Array('colspan'=>7,'class'=>'Standard'),SPrintF('Общий доход от серверов группы: %s',$Comp)));
     #----------------------------------------------------------------------------
     # средняя стоимость аккаунта
     $Comp = Comp_Load('Formats/Currency',$Balance / $Accounts);
     if(Is_Error($Comp))
       return ERROR | @Trigger_Error(500);
     #----------------------------------------------------------------------------
     $Table[] = Array(new Tag('TD',Array('colspan'=>7,'class'=>'Standard'),SPrintF('Средняя цена аккаунта в группе: %s',$Comp)));
     #----------------------------------------------------------------------------
     $Comp = Comp_Load('Tables/Extended',$Table);
     if(Is_Error($Comp))
       return ERROR | @Trigger_Error(500);
     #----------------------------------------------------------------------------
     $NoBody->AddChild($Comp);
     #----------------------------------------------------------------------------
     #----------------------------------------------------------------------------
     $Graphs[$HostingServersGroup['ID']] = Array('Name'=>$HostingServersGroup['Name'],'Balance'=>$Balance,'Accounts'=>$Accounts,'Params'=>$Params,'Labels'=>$Labels);
     #----------------------------------------------------------------------------
     $NoBody->AddChild(new Tag('BR'));
     #----------------------------------------------------------------------------
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
  #-------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# строим графики, считаем суммы
$Balance = 0;
$Accounts = 0;
#-------------------------------------------------------------------------------
foreach($HostingServersGroups as $HostingServersGroup){
  $Balance += $Graphs[$HostingServersGroup['ID']]['Balance'];
  $Accounts+= $Graphs[$HostingServersGroup['ID']]['Accounts'];
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
$NoBody->AddChild(new Tag('BR'));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($HostingServersGroups as $HostingServersGroup){
  #Debug(print_r($Graphs[$HostingServersGroup['ID']],true));
  #-------------------------------------------------------------------------------
  if(Count($Graphs[$HostingServersGroup['ID']]['Params']) > 1){
    #-------------------------------------------------------------------------
    $File = SPrintF('%s.jpg',Md5('HostingIncome_fin' . $HostingServersGroup['ID']));
    #-------------------------------------------------------------------------
    Artichow_Pie(SPrintF('Доходы группы %s',$Graphs[$HostingServersGroup['ID']]['Name']),SPrintF('%s/%s',$Folder,$File),$Graphs[$HostingServersGroup['ID']]['Params'],$Graphs[$HostingServersGroup['ID']]['Labels']);
    #-------------------------------------------------------------------------
//    $NoBody->AddChild(new Tag('BR'));
    #-------------------------------------------------------------------------
    $NoBody->AddChild(new Tag('IMG',Array('src'=>$File)));
    #-------------------------------------------------------------------------
//    $NoBody->AddChild(new Tag('BR'));
    #-------------------------------------------------------------------------
  }
}
#-------------------------------------------------------------------------------
$Result['DOM'] = $NoBody;
#-------------------------------------------------------------------------------
return $Result;

?>
