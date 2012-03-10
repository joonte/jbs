<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/Tree.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$Path = Tree_Path('Groups',(integer)$__USER['GroupID'],'ID');
#-------------------------------------------------------------------------------
switch(ValueOf($Path)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $Result = Array();
    #---------------------------------------------------------------------------
    $Where = Array(SPrintF("((`GroupID` IN (%s) OR `UserID` = %u) AND `IsActive` = 'yes') OR ((SELECT COUNT(*) FROM `OrdersOwners` WHERE `OrdersOwners`.`ServiceID` = `Services`.`ID` AND `UserID` = %u) > 0)",Implode(',',$Path),$__USER['ID'],$__USER['ID']),"`IsHidden` != 'yes'");
    #---------------------------------------------------------------------------
    $Services = DB_Select('Services',Array('ID','Code','Item','ServicesGroupID','IsActive','(SELECT `Name` FROM `ServicesGroups` WHERE `ServicesGroups`.`ID` = `Services`.`ServicesGroupID`) as `ServicesGroupName`','(SELECT `SortID` FROM `ServicesGroups` WHERE `ServicesGroups`.`ID` = `Services`.`ServicesGroupID`) as `ServicesSortID`'),Array('SortOn'=>Array('ServicesSortID','SortID'),'Where'=>$Where));
    #---------------------------------------------------------------------------
    switch(ValueOf($Services)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return FALSE;
      case 'array':
        #-----------------------------------------------------------------------
        $ServicesGroupID = UniqID();
        #-----------------------------------------------------------------------
        foreach($Services as $Service){
          #---------------------------------------------------------------------
          if($Service['ServicesGroupID'] != $ServicesGroupID){
            #-------------------------------------------------------------------
            $ServicesGroupID = $Service['ServicesGroupID'];
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Formats/String',$Service['ServicesGroupName'],40);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Result[UniqID('ID')] = Array('Text'=>$Comp,'Level'=>1,'Paths'=>Array('^NULL$'),'Href'=>'');
          }
          #---------------------------------------------------------------------
          $Comp = Comp_Load('Formats/String',$Service['Item'],40);
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          if($Service['Code'] == "Domains"){
              $Service['Code'] = "Domain";
          }
          #-------------------------------------------------------------------------
          $Code = $Service['Code'];
          #---------------------------------------------------------------------
          $Item = Array('Text'=>$Comp,'Level'=>2,'Paths'=>Array($Code != 'Default'?SPrintF('\/%s[a-zA-Z0-9]+',$Service['Code']):SPrintF('\/ServicesOrders\?ServiceID=%u',$Service['ID'])),'Href'=>SPrintF('/%s',($Code != 'Default'?SPrintF('%sOrders',$Code):SPrintF('ServicesOrders?ServiceID=%s',$Service['ID']))));
          #---------------------------------------------------------------------
          if(!$Service['IsActive'])
            $Item['Attribs'] = Array('style'=>'color:#969696;');
          #---------------------------------------------------------------------
          $Result[UniqID('ID')] = $Item;
        }
        #-----------------------------------------------------------------------
        return $Result;
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------


?>
