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
$Result = Array('Title'=>'Планирование поступлений по хостингу');
#-------------------------------------------------------------------------------
$MonthsNames = Array('Декабрь','Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
#-------------------------------------------------------------------------------
if(!$IsCreate)
  return $Result;
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('P','Данный вид аналитики позволяет произвести планирование будущих поступлений по заказам хостинга, продление которых предполагается в будущем.'));
#-------------------------------------------------------------------------------
$Query = "CREATE TEMPORARY TABLE `%s`(
  `ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `OrderID`        int(11),
  `Customer`       text,
  `Login`          char(20),
  `Domain`         char(100),
  `ExpirationDate` int(11),
  `MinDaysPay`     int(6),
  `CostDay`        float(6,2),
  `ContractID`     int(11),
  `Balance`        float(7,2)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
#-------------------------------------------------------------------------------
$UniqID = UniqID('ID');
#-------------------------------------------------------------------------------
$IsQuery = DB_Query(SPrintF($Query,$UniqID));
if(Is_Error($IsQuery))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Epoches = 3;
#-------------------------------------------------------------------------------
$Incomes = Array();
#-------------------------------------------------------------------------------
$Orders = DB_Select(Array('HostingOrders','Orders','Contracts'),Array('ContractID','Balance','OrderID','Customer','Login','Domain','(UNIX_TIMESTAMP() + `DaysRemainded`*86400) as `ExpirationDate`','(SELECT `MinDaysPay` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = `HostingOrders`.`SchemeID`) as `MinDaysPay`','(SELECT `CostDay` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = `HostingOrders`.`SchemeID`) as `CostDay`'),Array('Where'=>SPrintF('(UNIX_TIMESTAMP() + `DaysRemainded`*86400) < %u AND `Orders`.`ID` = `HostingOrders`.`OrderID` AND `HostingOrders`.`StatusID` = "Active" AND `Contracts`.`ID` = `Orders`.`ContractID`',MkTime(0,0,1,(Date('n')+$Epoches))),'SortOn'=>'ExpirationDate'));
#-------------------------------------------------------------------------------
switch(ValueOf($Orders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return $Result;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Orders as $Order){
      #-------------------------------------------------------------------------
      $IsInsert = DB_Insert($UniqID,$Order);
      if(Is_Error($IsInsert))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Incomes[$Order['OrderID']] = $Order;
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$sMonth = Date('Y')*12 + Date('n');
#-------------------------------------------------------------------------------
$Head = Array(new Tag('TD',Array('class'=>'Head'),'Клиент'),new Tag('TD',Array('class'=>'Head'),'Логин'),new Tag('TD',Array('class'=>'Head'),'Домен'));
#-------------------------------------------------------------------------------
$Totals = $Labels = Array();
#-------------------------------------------------------------------------------
for($Month=$sMonth;$Month<=($sMonth+$Epoches);$Month++){
  #-----------------------------------------------------------------------------
  $Totals[$Month] = 0;
  #-----------------------------------------------------------------------------
  $Head[] = new Tag('TD',Array('class'=>'Head'),$MonthsNames[$Month%12]);
  #-----------------------------------------------------------------------------
  $Labels[] = $MonthsNames[$Month%12];
  #-----------------------------------------------------------------------------
  $Balances = Array();
  #-----------------------------------------------------------------------------
  $Orders = DB_Select($UniqID,Array('ID','OrderID','ExpirationDate','MinDaysPay','(`MinDaysPay`*`CostDay`) as `Income`','ContractID','Balance'),Array('Where'=>SPrintF('MONTH(FROM_UNIXTIME(`ExpirationDate`)) + YEAR(FROM_UNIXTIME(`ExpirationDate`))*12 = %u',$Month)));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Orders)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      # No more...
    break;
    case 'array':
      #-------------------------------------------------------------------------
      foreach($Orders as $Order)
        $Balances[$Order['ContractID']] = $Order['Balance'];
      #-------------------------------------------------------------------------
      foreach($Orders as $Order){
        #-----------------------------------------------------------------------
        $ContractID = $Order['ContractID'];
        #-----------------------------------------------------------------------
        $Balance = &$Balances[$ContractID];
        #-----------------------------------------------------------------------
        if($Balance >= $Order['Income']){
          #---------------------------------------------------------------------
          $Balance -= $Order['Income'];
          #---------------------------------------------------------------------
        }else{
          #---------------------------------------------------------------------
          $Incomes[$Order['OrderID']][$Month] = $Order['Income'] - $Balance;
          #---------------------------------------------------------------------
          $Balance = 0;
        }
        #-----------------------------------------------------------------------
        $ExpirationDate = $Order['ExpirationDate'] + $Order['MinDaysPay']*86400;
        #-----------------------------------------------------------------------
        $IsUpdate = DB_Update($UniqID,Array('ExpirationDate'=>$ExpirationDate),Array('ID'=>$Order['ID']));
        if(Is_Error($IsUpdate))
          return ERROR | @Trigger_Error(500);
      }
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
  #-----------------------------------------------------------------------------
  foreach($Balances as $ContractID=>$Balance){
    #---------------------------------------------------------------------------
    $IsUpdate = DB_Update($UniqID,Array('Balance'=>$Balance),Array('Where'=>SPrintF('`ContractID` = %u',$ContractID)));
    if(Is_Error($IsUpdate))
      return ERROR | @Trigger_Error(500);
  }
}
#-------------------------------------------------------------------------------
$Table[] = $Head;
#-------------------------------------------------------------------------------
foreach($Incomes as $Income){
  #-----------------------------------------------------------------------------
  $Line = Array($Income['Customer'],$Income['Login'],$Income['Domain']);
  #-----------------------------------------------------------------------------
  for($Month=$sMonth;$Month<=($sMonth+$Epoches);$Month++){
    #---------------------------------------------------------------------------
    if(IsSet($Income[$Month])){
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Formats/Currency',$Income[$Month]);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Line[] = $Comp;
      #-------------------------------------------------------------------------
      $Totals[$Month] += $Income[$Month];
    }else
      $Line[] = new Tag('TD');
  }
  #-----------------------------------------------------------------------------
  $Table[] = $Line;
}
#-------------------------------------------------------------------------------
$Lines = Array(new Tag('TD',Array('colspan'=>3),'Общие поступления'));
#-------------------------------------------------------------------------------
$Line = Array();
#-------------------------------------------------------------------------------
foreach($Totals as $Total){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Formats/Currency',$Total);
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Lines[] = $Comp;
  #-----------------------------------------------------------------------------
  $Line[] = SPrintF('%01.2f',$Total);
}
#-------------------------------------------------------------------------------
Array_Splice($Table,1,0,Array($Lines));
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Extended',$Table);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($ShowTables)
	$NoBody ->AddChild($Comp);
#-------------------------------------------------------------------------------
$File = SPrintF('%s.jpg',Md5('HostingOrdersPlaning1'));
#-------------------------------------------------------------------------------
Artichow_Line('Планирование поступлений по хостингу',SPrintF('%s/%s',$Folder,$File),Array($Line),$Labels,Array(0x233454));
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('BR'));
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('IMG',Array('src'=>$File)));
#-------------------------------------------------------------------------------
$Lines = Array(new Tag('TD',Array('colspan'=>3),'Общие поступления'));
#-------------------------------------------------------------------------------
$Result['DOM'] = $NoBody;
#-------------------------------------------------------------------------------
return $Result;
#-------------------------------------------------------------------------------

?>
