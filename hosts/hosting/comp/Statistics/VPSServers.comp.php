<?php

#-------------------------------------------------------------------------------
/** @author Лапшин С.М. (Joonte Ltd.) 
    модифицировано Alex Keda, для VPS */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('IsCreate','Folder','StartDate','FinishDate','Details');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/Artichow.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Result = Array('Title'=>'Распределение заказов на VPS по серверам');
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('P','Данный вид статистики содержит информацию о количестве заказов закрепленных за каждым сервером VPS.'));
#-------------------------------------------------------------------------------
if(!$IsCreate)
  return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array(
		'ID',
		'Address',
		'(SELECT COUNT(*) FROM `VPSOrders` WHERE `VPSOrders`.`ServerID` = `VPSServers`.`ID` AND (`VPSOrders`.`StatusID` = "Active" OR `VPSOrders`.`StatusID` = "Suspended")) as `Count`'
		);
#-------------------------------------------------------------------------------
$VPSServers = DB_Select('VPSServers',$Columns,Array('SortOn'=>'Address'));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSServers)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return $Result;
  case 'array':
    #---------------------------------------------------------------------------
    $Params = $Labels = Array();
    #---------------------------------------------------------------------------
    $Table = Array(Array(new Tag('TD',Array('class'=>'Head'),'Адрес сервера'),new Tag('TD',Array('class'=>'Head'),'Кол-во заказов')));
    #---------------------------------------------------------------------------
    foreach($VPSServers as $VPSServer){
      #-------------------------------------------------------------------------
      $Params[] = $VPSServer['Count'];
      $Labels[] = $VPSServer['Address'];
      #-------------------------------------------------------------------------
      $Table[] = Array($VPSServer['Address'],(integer)$VPSServer['Count']);
    }
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Tables/Extended',$Table);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $NoBody->AddChild($Comp);
    #---------------------------------------------------------------------------
    if(Count($Params) > 1){
      #-------------------------------------------------------------------------
      $File = SPrintF('%s.jpg',Md5('VPSServers1'));
      #-------------------------------------------------------------------------
      Artichow_Pie('Распределение заказов по серверам VPS',SPrintF('%s/%s',$Folder,$File),$Params,$Labels);
      #-------------------------------------------------------------------------
      $NoBody->AddChild(new Tag('BR'));
      #-------------------------------------------------------------------------
      $NoBody->AddChild(new Tag('IMG',Array('src'=>$File)));
    }
    #---------------------------------------------------------------------------
    $Result['DOM'] = $NoBody;
    #---------------------------------------------------------------------------
    return $Result;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
