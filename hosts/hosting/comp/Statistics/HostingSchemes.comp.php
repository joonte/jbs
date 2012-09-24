<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('IsCreate','Folder','StartDate','FinishDate','Details');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Result = Array('Title'=>'Распределение доходов/заказов на хостинг по тарифам');
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
if(!$IsCreate)
  return $Result;
#-------------------------------------------------------------------------------
$HostingOrders = DB_Select('HostingSchemes',Array('Name','ServersGroupID','(SELECT COUNT(*) FROM `HostingOrders` WHERE `SchemeID` = `HostingSchemes`.`ID` AND `StatusID`="Active") as `Count`','(SELECT `Name` FROM `HostingServersGroups` WHERE `HostingServersGroups`.`ID`=`HostingSchemes`.`ServersGroupID`) as `ServersGroupName`','SUM(`CostDay`*`MinDaysPay`)*(SELECT COUNT(*) FROM `HostingOrders` WHERE `SchemeID` = `HostingSchemes`.`ID` AND `StatusID`="Active") as `Income`'),Array('SortOn'=>Array('ServersGroupID','SortID'),'GroupBy'=>'ID'));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return $Result;
  case 'array':
    #---------------------------------------------------------------------------
    $NoBody->AddChild(new Tag('P','Данный вид статистики дает детальную информацию о количестве активных заказов и доходов на каждом из тарифов.'));
    #---------------------------------------------------------------------------
    $Table = Array(Array(new Tag('TD',Array('class'=>'Head'),'Наименование тарифа'),new Tag('TD',Array('class'=>'Head'),'Кол-во заказов'),new Tag('TD',Array('class'=>'Head'),'Доход')));
    #---------------------------------------------------------------------------
    $ServersGroupName = UniqID();
    #---------------------------------------------------------------------------
    foreach($HostingOrders as $HostingOrder){
      #-------------------------------------------------------------------------
      if($ServersGroupName != $HostingOrder['ServersGroupName']){
        #-----------------------------------------------------------------------
        $ServersGroupName = $HostingOrder['ServersGroupName'];
        #-----------------------------------------------------------------------
        $Table[] = $ServersGroupName;
      }
      #-------------------------------------------------------------------------
      $Table[] = Array($HostingOrder['Name'],(integer)$HostingOrder['Count'],$HostingOrder['Income']);
    }
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Tables/Extended',$Table);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $NoBody->AddChild($Comp);
    #---------------------------------------------------------------------------
    $Result['DOM'] = $NoBody;
    #---------------------------------------------------------------------------
    return $Result;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
