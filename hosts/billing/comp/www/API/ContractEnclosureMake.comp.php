<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$ContractID = (integer) @$Args['ContractID'];
$TypeID     =  (string) @$Args['TypeID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Types = $Config['Contracts']['Enclosures']['Types'];
#-------------------------------------------------------------------------------
if(!IsSet($Types[$TypeID]))
  return new gException('WRONG_TYPE_ID','Неверный тип приложения');
#-------------------------------------------------------------------------------
$Type = $Types[$TypeID];
#-------------------------------------------------------------------------------
$Contract = DB_Select('Contracts',Array('ID','CreateDate','UserID','TypeID','ProfileID'),Array('UNIQ','ID'=>$ContractID));
#-------------------------------------------------------------------------------
switch(ValueOf($Contract)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('CONTRACT_NOT_FOUND','Договор не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('ContractsRead',(integer)$__USER['ID'],(integer)$Contract['UserID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsPermission)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'false':
        return ERROR | @Trigger_Error(700);
      case 'true':
        #-----------------------------TRANSACTION-------------------------------
        if(Is_Error(DB_Transaction($TransactionID = UniqID('ContractEnclosureMake'))))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $IContractEnclosure = Array('ContractID'=>$Contract['ID'],'TypeID'=>$TypeID);
        #-----------------------------------------------------------------------
        $ContractEnclosureID = DB_Insert('ContractsEnclosures',$IContractEnclosure);
        if(Is_Error($ContractEnclosureID))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Contracts/Enclosures/Build',$ContractEnclosureID);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ContractsEnclosures','StatusID'=>'Waiting','RowsIDs'=>$ContractEnclosureID));
        #-----------------------------------------------------------------------
        switch(ValueOf($Comp)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return new gException('ENCLOSURE_STATUS_SET_ERROR','Ошибка установки статуса приложения к договору',$Comp);
          case 'array':
            #-------------------------------------------------------------------
            if(Is_Error(DB_Commit($TransactionID)))
              return ERROR | @Trigger_Error(500);
            #-----------------------END TRANSACTION-----------------------------
            return $ContractEnclosureID;
          default:
            return ERROR | @Trigger_Error(101);
        }
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
