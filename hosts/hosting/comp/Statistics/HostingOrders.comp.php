<?php

#-------------------------------------------------------------------------------
/** @author Лапшин С.М. (Joonte Ltd.) */
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
$Folder		=  (string) @$Args['Folder'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/Artichow.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Result = Array('Title'=>'Распределение заказов на хостинг по времени');
#-------------------------------------------------------------------------------
$MonthsNames = Array('Декабрь','Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
#-------------------------------------------------------------------------------
if(!$IsCreate)
  return $Result;
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('P','Данный вид статистики содержит информацию о количестве заказов в указанный период времени.'));
#-------------------------------------------------------------------------------
$Where = SPrintF('`OrderDate` >= %u AND `OrderDate` <= %u',$StartDate,$FinishDate);
#-------------------------------------------------------------------------------
if(In_Array('ByDays',$Details)){
  #-----------------------------------------------------------------------------
  $HostingOrders = DB_Select(Array('Orders','HostingOrders'),Array('COUNT(*) as `Count`','OrderID','OrderDate',' GET_DAY_FROM_TIMESTAMP(`OrderDate`) as `Day`'),Array('Where'=>'`HostingOrders`.`OrderID` = `Orders`.`ID` AND `HostingOrders`.`StatusID` = "Active" AND ' . $Where,'GroupBy'=>'Day','SortOn'=>'OrderDate'));
  #-----------------------------------------------------------------------------
  switch(ValueOf($HostingOrders)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      # No more...
    break;
    case 'array':
      #-------------------------------------------------------------------------
      $Table = Array(Array(new Tag('TD',Array('class'=>'Head'),'Дата'),new Tag('TD',Array('class'=>'Head'),'Кол-во')));
      #-------------------------------------------------------------------------
      $CurrentMonth = 0;
      #-------------------------------------------------------------------------
      foreach($HostingOrders as $HostingOrder){
        #-----------------------------------------------------------------------
        if(Date('n',$HostingOrder['Day']*86400) != $CurrentMonth){
          #---------------------------------------------------------------------
          $CurrentMonth = Date('n',$HostingOrder['Day']*86400);
          #---------------------------------------------------------------------
          $Table[] = SPrintF('%s %u г.',$MonthsNames[$CurrentMonth],Date('Y',$HostingOrder['Day']*86400));
        }
        #-----------------------------------------------------------------------
        $Table[] = Array(Date('d',$HostingOrder['Day']*86400),(integer)$HostingOrder['Count']);
      }
      #-------------------------------------------------------------------------
      $NoBody->AddChild(new Tag('H2','Распределение заказов по дням'));
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Tables/Extended',$Table);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      if($ShowTables)
	      $NoBody->AddChild($Comp);
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
if(In_Array('ByMonth',$Details)){
  #-----------------------------------------------------------------------------
  $HostingOrders = DB_Select(Array('Orders','HostingOrders'),Array('OrderID','COUNT(*) as `Count`','MONTH(FROM_UNIXTIME(`OrderDate`)) as `Month`','OrderDate','YEAR(FROM_UNIXTIME(`OrderDate`)) as Year'),Array('Where'=>'`HostingOrders`.`OrderID` = `Orders`.`ID` AND ' . $Where,'GroupBy'=>Array('Month','Year'),'SortOn'=>'OrderDate'));
  #-----------------------------------------------------------------------------
  switch(ValueOf($HostingOrders)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      # No more...
    break;
    case 'array':
      #-------------------------------------------------------------------------
      $Table = Array(Array(new Tag('TD',Array('class'=>'Head'),'Месяц'),new Tag('TD',Array('class'=>'Head'),'Кол-во')));
      #-------------------------------------------------------------------------
      $Order = Current($HostingOrders);
      $sMonth = $Order['Month']+$Order['Year']*12;
      #-------------------------------------------------------------------------
      $Order = End($HostingOrders);
      $eMonth = $Order['Month']+$Order['Year']*12;
      #-------------------------------------------------------------------------
      $Months = Array();
      #-------------------------------------------------------------------------
      foreach($HostingOrders as $Order)
        $Months[$Order['Month']+$Order['Year']*12] = $Order;
      #-------------------------------------------------------------------------
      $Labels = $Line = Array();
      #-------------------------------------------------------------------------
      $CurrentYear = 0;
      #-------------------------------------------------------------------------
      for($Month=$sMonth;$Month<=$eMonth;$Month++){
        #-----------------------------------------------------------------------
        $Order = (IsSet($Months[$Month])?$Months[$Month]:Array('Month'=>$Month%12,'Year'=>(integer)($Month/12),'Count'=>0,'OrderID'=>'-'));
        #-----------------------------------------------------------------------
        $Labels[] = SPrintF("%s\n%u г.",$MonthsNames[$Order['Month']],$Order['Year']);
        #-----------------------------------------------------------------------
        $Line[] = $Order['Count'];
        #-----------------------------------------------------------------------
        if($Order['Year'] != $CurrentYear){
          #---------------------------------------------------------------------
          $CurrentYear = $Order['Year'];
          #---------------------------------------------------------------------
          $Table[] = SPrintF('%u г.',$CurrentYear);
        }
        #-----------------------------------------------------------------------
        $Table[] = Array($MonthsNames[$Order['Month']],(integer)$Order['Count']);
      }
      #-------------------------------------------------------------------------
      $NoBody->AddChild(new Tag('H2','Распределение заказов по месяцам'));
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Tables/Extended',$Table);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      if($ShowTables)
	      $NoBody->AddChild($Comp);
      #-------------------------------------------------------------------------
      if(Count($Line) > 1){
        #-----------------------------------------------------------------------
        $File = SPrintF('%s.jpg',Md5('HostingOrders1'));
        #-----------------------------------------------------------------------
        Artichow_Line('Распределение заказов по месяцам',SPrintF('%s/%s',$Folder,$File),Array($Line),$Labels,Array(0x233454));
        #-----------------------------------------------------------------------
        $NoBody->AddChild(new Tag('BR'));
        #-----------------------------------------------------------------------
        $NoBody->AddChild(new Tag('IMG',Array('src'=>$File)));
      }
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
if(In_Array('ByQuarter',$Details)){
  #-----------------------------------------------------------------------------
  $HostingOrders = DB_Select(Array('Orders','HostingOrders'),Array('OrderID','COUNT(*) as `Count`','GET_QUARTER_FROM_TIMESTAMP(`OrderDate`) as `Quarter`','OrderDate','YEAR(FROM_UNIXTIME(`OrderDate`)) as Year'),Array('Where'=>'`HostingOrders`.`OrderID` = `Orders`.`ID` AND ' . $Where,'GroupBy'=>Array('Quarter','Year'),'SortOn'=>'OrderDate'));
  #-----------------------------------------------------------------------------
  switch(ValueOf($HostingOrders)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      # No more...
    break;
    case 'array':
     #--------------------------------------------------------------------------
      $Table = Array(Array(new Tag('TD',Array('class'=>'Head'),'Квартал'),new Tag('TD',Array('class'=>'Head'),'Кол-во')));
      #-------------------------------------------------------------------------
      $Order = Current($HostingOrders);
      $sQuarter = $Order['Quarter'] + $Order['Year']*4;
      #-------------------------------------------------------------------------
      $Order = End($HostingOrders);
      $eQuarter = $Order['Quarter'] + $Order['Year']*4;
      #-------------------------------------------------------------------------
      $Quarters = Array();
      #-------------------------------------------------------------------------
      foreach($HostingOrders as $Order)
        $Quarters[$Order['Quarter'] + $Order['Year']*4] = $Order;
      #-------------------------------------------------------------------------
      $Labels = $Line = Array();
      #-------------------------------------------------------------------------
      $CurrentYear = 0;
      #-------------------------------------------------------------------------
      for($Quarter = $sQuarter;$Quarter<=$eQuarter;$Quarter++){
        #-----------------------------------------------------------------------
        $Order = (IsSet($Quarters[$Quarter])?$Quarters[$Quarter]:Array('Quarter'=>($Quarter - ((integer)($Quarter/4))*4),'Year'=>(integer)($Quarter/4),'Count'=>0,'OrderID'=>'-'));
        #-----------------------------------------------------------------------
        $Labels[] = SPrintF('%u кв.(%u г.)',$Order['Quarter'],$Order['Year']);
        #-----------------------------------------------------------------------
        $Line[] = $Order['Count'];
        #-----------------------------------------------------------------------
        if($Order['Year'] != $CurrentYear){
          #---------------------------------------------------------------------
          $CurrentYear = $Order['Year'];
          $Table[] = SPrintF('%u г.',$Order['Year']);
        }
        #-----------------------------------------------------------------------
        $Table[] = Array(SPrintF('%u кв.',$Order['Quarter']),$Order['Count']);
      }
      #-------------------------------------------------------------------------
      $NoBody->AddChild(new Tag('H2','Распределение заказов по кварталам'));
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Tables/Extended',$Table);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      if($ShowTables)
	      $NoBody->AddChild($Comp);
      #-------------------------------------------------------------------------
      if(Count($Line) > 1){
        #-----------------------------------------------------------------------
        $File = SPrintF('%s.jpg',Md5('HostingOrders2'));
        #-----------------------------------------------------------------------
        Artichow_Line('Распределение заказов по кварталам',SPrintF('%s/%s',$Folder,$File),Array($Line),$Labels,Array(0x233454));
        #-----------------------------------------------------------------------
        $NoBody->AddChild(new Tag('BR'));
        #-----------------------------------------------------------------------
        $NoBody->AddChild(new Tag('IMG',Array('src'=>$File)));
      }
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
if(Count($NoBody->Childs) < 2)
  return $Result;
#-------------------------------------------------------------------------------
$Result['DOM'] = $NoBody;
#-------------------------------------------------------------------------------
return $Result;

?>
