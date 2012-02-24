<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Events = DB_Select('Events',Array('ID','CreateDate','UserID','(SELECT `Email` FROM `Users` WHERE `Users`.`ID` = `Events`.`UserID`) as `Email`','Text','PriorityID'),Array('Where'=>"`CreateDate` >= BEGIN_DAY() AND `PriorityID` IN ('Billing','Warning','Error')",'SortOn'=>'CreateDate','IsDesc'=>TRUE));
#-------------------------------------------------------------------------------
switch(ValueOf($Events)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return FALSE;
  case 'array':
    #---------------------------------------------------------------------------
    $Table = new Tag('TABLE',Array('cellspacing'=>0,'width'=>'100%'));
    #---------------------------------------------------------------------------
    foreach($Events as $Event){
      #-------------------------------------------------------------------------
      $CreateDate = Comp_Load('Formats/Date/Extended',$Event['CreateDate']);
      if(Is_Error($CreateDate))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      switch($Event['PriorityID']){
        case 'Waiting':
          $Color = 'FDF6D3';
        break;
        case 'Error':
          $Color = 'FFCCCC';
        break;
        default:
          $Color = 'F1FCCE';
      }
      #-------------------------------------------------------------------------
      $Td = new Tag('TD',Array('style'=>SPrintF('border-bottom:1px solid #FFFFFF;font-size:11px;background-color:#%s;',$Color)));
      #-------------------------------------------------------------------------
      $Td->AddChild(new Tag('B',$CreateDate));
      $Td->AddChild(new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/UserInfo',{UserID:%u});",$Event['UserID'])),SPrintF('[%s]',$Event['Email'])));
      $Td->AddChild(new Tag('BR'));
      $Td->AddChild(new Tag('SPAN',$Event['Text']));
      #-------------------------------------------------------------------------
      $Table->AddChild(new Tag('TR',$Td));
    }
    #---------------------------------------------------------------------------
    $Div = new Tag('DIV',Array('style'=>'border:1px solid #FFFFFF;width:400px;height:200px;overflow:scroll;overflow-x:auto;overflow-y:auto;'),$Table);
    #---------------------------------------------------------------------------
    return Array('Title'=>'События за сегодня','DOM'=>new Tag('NOBODY',$Div,new Tag('A',Array('href'=>'/Administrator/Events'),'[все события]')));
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
