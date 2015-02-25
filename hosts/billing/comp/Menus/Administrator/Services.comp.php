<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Result = Array();
#-------------------------------------------------------------------------------
$Services = DB_Select('Services',Array('ID','Code','Name','Item','IsActive','ServicesGroupID','(SELECT `Name` FROM `ServicesGroups` WHERE `ServicesGroups`.`ID` = `Services`.`ServicesGroupID`) as `ServicesGroupName`','(SELECT `SortID` FROM `ServicesGroups` WHERE `ServicesGroups`.`ID` = `Services`.`ServicesGroupID`) as `ServicesSortID`'),Array('SortOn'=>Array('ServicesSortID','SortID'),'Where'=>"`IsHidden` != 'yes'"));
#-------------------------------------------------------------------------------
switch(ValueOf($Services)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $ServicesGroupID = UniqID();
    #---------------------------------------------------------------------------
    foreach($Services as $Service){
      #-------------------------------------------------------------------------
      if($Service['ServicesGroupID'] != $ServicesGroupID){
        #-----------------------------------------------------------------------
        $ServicesGroupID = $Service['ServicesGroupID'];
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/String',$Service['ServicesGroupName'],40);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Result[UniqID('ID')] = Array('Text'=>$Comp,'Level'=>1,'Paths'=>Array('^NULL$'),'Href'=>'');
      }
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Formats/String',$Service['Item'],40);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Code = $Service['Code'];
      #-------------------------------------------------------------------------
      $Item = Array('Text'=>$Comp,'Level'=>2,'Paths'=>Array($Code != 'Default'?SPrintF('\/Administrator\/%s[a-zA-Z0-9]+',$Service['Code']):SPrintF('\/Administrator\/ServicesOrders\?ServiceID=%u',$Service['ID'])),'Href'=>SPrintF('/Administrator/%s',($Code != 'Default'?SPrintF('%sOrders',$Code):SPrintF('ServicesOrders?ServiceID=%s',$Service['ID']))));
      #-------------------------------------------------------------------------
      if(!$Service['IsActive'])
        $Item['Attribs'] = Array('style'=>'color:#969696;');
      #-------------------------------------------------------------------------
      $Result[UniqID('ID')] = $Item;
    }
    #---------------------------------------------------------------------------
    return $Result;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------


?>
