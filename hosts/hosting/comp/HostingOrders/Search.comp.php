<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('LinkID');
/******************************************************************************/
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Links = &Links();
# Коллекция ссылок
$Template = &$Links[$LinkID];
/******************************************************************************/
/******************************************************************************/
$Tr = new Tag('TR');
#-------------------------------------------------------------------------------
$HostingSchemes = DB_Select('HostingSchemes',Array('ID','Name','CostMonth','(SELECT `Name` FROM `ServersGroups` WHERE `HostingSchemes`.`ServersGroupID` = `ServersGroups`.`ID`) as `ServersGroupName`'),Array('SortOn'=>'SortID'));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingSchemes)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $Options = Array();
    #---------------------------------------------------------------------------
    $Options['Default'] = 'Не указан';
    #---------------------------------------------------------------------------
    foreach($HostingSchemes as $HostingScheme){
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Formats/Currency',$HostingScheme['CostMonth']);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Options[$HostingScheme['ID']] = SPrintF('%s, %s, %s',$HostingScheme['Name'],$HostingScheme['ServersGroupName'],$Comp);
    }
    #---------------------------------------------------------------------------
    $SchemeID = 'Default';
    #---------------------------------------------------------------------------
    $Session = &$Template['Session'];
    #---------------------------------------------------------------------------
    if(IsSet($Session['SchemeID']))
      $SchemeID = $Session['SchemeID'];
    #---------------------------------------------------------------------------
    $Args = Args();
    #---------------------------------------------------------------------------
    if(IsSet($Args['SchemeID']))
      $SchemeID = $Args['SchemeID'];
    #---------------------------------------------------------------------------
    $Session['SchemeID'] = $SchemeID;
    #---------------------------------------------------------------------------
    $AddingWhere = &$Template['Source']['Adding']['Where'];
    #---------------------------------------------------------------------------
    if($SchemeID != 'Default')
      $AddingWhere[] = SPrintF('`SchemeID` = %u',$SchemeID);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Form/Select',Array('name'=>'SchemeID','onchange'=>'TableSuperReload();'),$Options,$SchemeID);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Tr->AddChild(new Tag('NOBODY',new Tag('TD',Array('class'=>'Comment'),'Тарифный план'),new Tag('TD',$Comp)));
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Servers = DB_Select('Servers',Array('ID','Address'),Array('Where'=>'(SELECT `ServiceID` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `Servers`.`ServersGroupID`) = 10000','SortOn'=>'Address'));
#-------------------------------------------------------------------------------
switch(ValueOf($Servers)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $Table = $Options = Array();
    #---------------------------------------------------------------------------
    $Options['Default'] = 'Не указан';
    #---------------------------------------------------------------------------
    foreach($Servers as $Server)
      $Options[$Server['ID']] = $Server['Address'];
    #---------------------------------------------------------------------------
    $ServerID = 'Default';
    #---------------------------------------------------------------------------
    $Session = &$Template['Session'];
    #---------------------------------------------------------------------------
    if(IsSet($Session['ServerID']))
      $ServerID = $Session['ServerID'];
    #---------------------------------------------------------------------------
    $Args = Args();
    #---------------------------------------------------------------------------
    if(IsSet($Args['ServerID']))
      $ServerID = $Args['ServerID'];
    #---------------------------------------------------------------------------
    $Session['ServerID'] = $ServerID;
    #---------------------------------------------------------------------------
    $AddingWhere = &$Template['Source']['Adding']['Where'];
    #---------------------------------------------------------------------------
    if($ServerID != 'Default')
      $AddingWhere[] = SPrintF('(SELECT `ServerID` FROM `OrdersOwners` WHERE `HostingOrdersOwners`.`OrderID` = `OrdersOwners`.`ID`) = %u',$ServerID);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Form/Select',Array('name'=>'ServerID','onchange'=>'TableSuperReload();'),$Options,$ServerID);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Tr->AddChild(new Tag('NOBODY',new Tag('TD',Array('class'=>'Comment'),'Сервер размещения'),new Tag('TD',$Comp)));
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
if(!Count($Tr->Childs))
  return FALSE;
#-------------------------------------------------------------------------------
return new Tag('TABLE',Array('class'=>'Standard','cellspacing'=>5),$Tr);
#-------------------------------------------------------------------------------

?>
