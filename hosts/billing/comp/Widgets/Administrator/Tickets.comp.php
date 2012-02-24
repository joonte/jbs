<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Tickets = DB_Select('Edesks',Array('COUNT(*) as `Count`','StatusID'),Array('Where'=>Array("(SELECT `IsDepartment` FROM `Groups` WHERE `Groups`.`ID` = `Edesks`.`TargetGroupID`) = 'yes'","(SELECT `IsDepartment` FROM `Groups` WHERE `Groups`.`ID` = (SELECT `GroupID` FROM `Users` WHERE `Users`.`ID` = `Edesks`.`UserID`)) = 'no'"),'GroupBy'=>'StatusID'));
#-------------------------------------------------------------------------------
switch(ValueOf($Tickets)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return FALSE;
  case 'array':
    #---------------------------------------------------------------------------
    $Config = Config();
    #---------------------------------------------------------------------------
    $Statuses = $Config['Statuses']['Edesks'];
    #---------------------------------------------------------------------------
    $Table = Array('Центр поддержки');
    #---------------------------------------------------------------------------
    foreach(Array_Keys($Statuses) as $StatusID){
      #-------------------------------------------------------------------------
      $Status = $Statuses[$StatusID];
      #-------------------------------------------------------------------------
      foreach($Tickets as $Ticket){
        #-----------------------------------------------------------------------
        if($Ticket['StatusID'] != $StatusID)
          continue;
        #-----------------------------------------------------------------------
        $Table[] = Array(new Tag('A',Array('href'=>SPrintF('/Administrator/Tickets?PatternOutID=%s',$StatusID)),$Status['Name']),(integer)$Ticket['Count']);
      }
    }
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Tables/Standard',$Table);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    return Array('Title'=>'Статусы запросов','DOM'=>$Comp);
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
