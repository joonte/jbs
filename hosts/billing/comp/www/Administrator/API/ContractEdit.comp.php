<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$ContractID     = (integer) @$Args['ContractID'];
$UserID         = (integer) @$Args['UserID'];
$CreateDate     = (integer) @$Args['CreateDate'];
$IsUponConsider = (boolean) @$Args['IsUponConsider'];
$ProfileID      = (integer) @$Args['ProfileID'];
$IsEnclosures   = (boolean) @$Args['IsEnclosures'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Profile = DB_Select('Profiles',Array('ID','TemplateID'),Array('UNIQ','ID'=>$ProfileID));
#-------------------------------------------------------------------------------
switch(ValueOf($Profile)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('PROFILE_NOT_FOUND','Профиль не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $IsUpdate = DB_Update('Profiles',Array('UserID'=>$UserID),Array('ID'=>$Profile['ID']));
    if(Is_Error($IsUpdate))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $IsUpdate = DB_Update('Contracts',Array('UserID'=>$UserID,'CreateDate'=>$CreateDate,'TypeID'=>$Profile['TemplateID'],'IsUponConsider'=>$IsUponConsider,'ProfileID'=>$Profile['ID']),Array('ID'=>$ContractID));
    if(Is_Error($IsUpdate))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if($IsEnclosures){
      #-------------------------------------------------------------------------
      $IsQuery = DB_Query(SPrintF('UPDATE `ContractsEnclosures` SET `CreateDate` = %u WHERE `ContractID` = %u',$CreateDate,$ContractID));
      if(Is_Error($IsQuery))
        return ERROR | @Trigger_Error(500);
    }
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Contracts/Build',$ContractID);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    return Array('Status'=>'Ok');
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
