<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$ServiceFieldID = (integer) @$Args['ServiceFieldID'];
$ServiceID      =  (string) @$Args['ServiceID'];
$Name           =  (string) @$Args['Name'];
$Prompt         =  (string) @$Args['Prompt'];
$TypeID         =  (string) @$Args['TypeID'];
$Options        =  (string) @$Args['Options'];
$ValidatorID    =  (string) @$Args['ValidatorID'];
$Default        =  (string) @$Args['Default'];
$IsDuty         = (boolean) @$Args['IsDuty'];
$IsKey          = (boolean) @$Args['IsKey'];
$SortID         = (integer) @$Args['SortID'];
#-------------------------------------------------------------------------------
if($Options){
  #-----------------------------------------------------------------------------
  $Array = Array();
  #-----------------------------------------------------------------------------
  $Options = Explode("\n",$Options);
  #-----------------------------------------------------------------------------
  if(!Count($Options))
    return new gException('OPTIONS_IS_EMPTY','Список выбора пуст');
  #-----------------------------------------------------------------------------
  foreach($Options as $Option){
    #---------------------------------------------------------------------------
    $Option = Explode("=",$Option);
    #---------------------------------------------------------------------------
    if(Count($Option) < 3)
      return new gException('WRONG_OPTION',SPrintF('Неверный формат выбора (%s)',Current($Option)));
    #---------------------------------------------------------------------------
    $Array[] = SPrintF('%s=%s=%01.2f',Trim(Current($Option)),Trim(Next($Option)),Trim(Next($Option)));
  }
  #-----------------------------------------------------------------------------
  $Options = Implode("\n",$Array);
}
#-------------------------------------------------------------------------------
$IServiceField = Array(
  #-----------------------------------------------------------------------------
  'Name'        => $Name,
  'Prompt'      => $Prompt,
  'TypeID'      => $TypeID,
  'Options'     => $Options,
  'ValidatorID' => $ValidatorID,
  'Default'     => $Default,
  'IsDuty'      => $IsDuty,
  'IsKey'       => $IsKey,
  'SortID'      => $SortID
);
#-------------------------------------------------------------------------------
if($ServiceFieldID){
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('ServicesFields',$IServiceField,Array('ID'=>$ServiceFieldID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
}else{
  #-----------------------------------------------------------------------------
  $IServiceField['ServiceID'] = $ServiceID;
  #-----------------------------------------------------------------------------
  $IsInsert = DB_Insert('ServicesFields',$IServiceField);
  if(Is_Error($IsInsert))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
$IsFlush = MemoryCache_Flush();
if(Is_Error($IsFlush))
  @Trigger_Error(500);
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
