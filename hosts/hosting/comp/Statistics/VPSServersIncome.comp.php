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
$NoBody->AddChild(new Tag('P','Данный вид статистики содержит информацию о доходности/нагрузке каждого из имеющихся серверов VPS за 1 мес.'));
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
	# All OK, servers found
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
foreach($VPSServersGroups as $VPSServersGroup){
#-------------------------------------------------------------------------------
  $Tables = Array('VPSServers','VPSOrders','VPSSchemes');
  $Columns = Array('COUNT(*) AS NumAcounts','SUM(`CostDay`*`MinDaysPay`) as `Income`','Address','CEIL(SUM(mem)) AS tmem','CEIL(SUM(ncpu * cpu)) AS tcpu','CEIL(SUM(disklimit)/1024) AS tdisk');
  $Condition = Array(
			'Where'	 =>Array(
					SPrintF('`VPSServers`.`ServersGroupID`=%u',$VPSServersGroup['ID']),
					'`VPSSchemes`.`ID` = `VPSOrders`.`SchemeID`',
					'`VPSServers`.`ID` = `VPSOrders`.`ServerID`',
					'`VPSOrders`.`StatusID`="Active"'
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
      break;
      #return $Result;
    case 'array':
     #----------------------------------------------------------------------------
     $Balance = 0;
     $Accounts = 0;
     #----------------------------------------------------------------------------
     $Params = $Labels = Array();
     #----------------------------------------------------------------------------
     $Table = Array(Array(new Tag('TD',Array('class'=>'Transparent','colspan'=>7),SPrintF('Группа серверов: %s',$VPSServersGroup['Name']))));
     #$Table = Array(Array(new Tag('TD',Array('class'=>'Head'),'Сервер'),new Tag('TD',Array('class'=>'Head'),'Процессор, MHz'),new Tag('TD',Array('class'=>'Head'),'Память, Mb'),new Tag('TD',Array('class'=>'Head'),'Диск, Gb')));
     $Table[] = Array(new Tag('TD',Array('class'=>'Head'),'Адрес сервера'),new Tag('TD',Array('class'=>'Head'),'Аккаунтов'),new Tag('TD',Array('class'=>'Head'),'Доход сервера'),new Tag('TD',Array('class'=>'Head'),'Доход аккаунта'),new Tag('TD',Array('class'=>'Head'),'Процессор, MHz'),new Tag('TD',Array('class'=>'Head'),'Диск, Gb'),new Tag('TD',Array('class'=>'Head'),'Память, Mb'));
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
       $Table[] = Array($Income['Address'],$Income['NumAcounts'],$Summ,$AccountPrice,$Income['tcpu'],$Income['tdisk'],$Income['tmem']);
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
     $Graphs[$VPSServersGroup['ID']] = Array('Name'=>$VPSServersGroup['Name'],'Balance'=>$Balance,'Accounts'=>$Accounts,'Params'=>$Params,'Labels'=>$Labels);
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
foreach($VPSServersGroups as $VPSServersGroup){
  if(IsSet($Graphs[$VPSServersGroup['ID']])){
    $Balance += $Graphs[$VPSServersGroup['ID']]['Balance'];
    $Accounts+= $Graphs[$VPSServersGroup['ID']]['Accounts'];
  }
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
foreach($VPSServersGroups as $VPSServersGroup){
  if(IsSet($Graphs[$VPSServersGroup['ID']])){
    #Debug(print_r($Graphs[$VPSServersGroup['ID']],true));
    #-------------------------------------------------------------------------------
    if(Count($Graphs[$VPSServersGroup['ID']]['Params']) > 1){
      #-------------------------------------------------------------------------
      $File = SPrintF('%s.jpg',Md5('VPSIncome_fin' . $VPSServersGroup['ID']));
      #-------------------------------------------------------------------------
      Artichow_Pie(SPrintF('Доходы группы %s',$Graphs[$VPSServersGroup['ID']]['Name']),SPrintF('%s/%s',$Folder,$File),$Graphs[$VPSServersGroup['ID']]['Params'],$Graphs[$VPSServersGroup['ID']]['Labels']);
      #-------------------------------------------------------------------------
//    $NoBody->AddChild(new Tag('BR'));
      #-------------------------------------------------------------------------
      $NoBody->AddChild(new Tag('IMG',Array('src'=>$File)));
      #-------------------------------------------------------------------------
//    $NoBody->AddChild(new Tag('BR'));
      #-------------------------------------------------------------------------
    }
  }
}
#-------------------------------------------------------------------------------
$Result['DOM'] = $NoBody;
#-------------------------------------------------------------------------------
return $Result;

?>
