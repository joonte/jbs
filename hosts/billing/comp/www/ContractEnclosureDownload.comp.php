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
$ContractEnclosureID = (integer) @$Args['ContractEnclosureID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/HTMLDoc.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','ContractID','Number','UserID','TypeID','Document');
#-------------------------------------------------------------------------------
$ContractEnclosure = DB_Select('ContractsEnclosuresOwners',$Columns,Array('UNIQ','ID'=>$ContractEnclosureID));
#-------------------------------------------------------------------------------
switch(ValueOf($ContractEnclosure)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('CONTRACT_ENCLOSURE_NOT_FOUND','Приложение к договору не найдено');
  case 'array':
    #---------------------------------------------------------------------------
    $Document = $ContractEnclosure['Document'];
    #---------------------------------------------------------------------------
    if(!$Document)
      return new gException('DOCUMENT_NOT_BUILDED','Документ не сформирован');
    #---------------------------------------------------------------------------
    $Permission = Permission_Check('ContractEnclosureRead',(integer)$GLOBALS['__USER']['ID'],(integer)$ContractEnclosure['UserID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($Permission)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'false':
        return ERROR | @Trigger_Error(700);
      case 'true':
        #-----------------------------------------------------------------------
        $PDF = HTMLDoc_CreatePDF('ContractEnclosure',$Document);
        #-----------------------------------------------------------------------
        switch(ValueOf($PDF)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'string':
            #-------------------------------------------------------------------
            $Length = MB_StrLen($PDF,'ASCII');
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Formats/Contract/Number',$ContractEnclosure['ContractID']);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Number = $Comp;
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Formats/Contract/Enclosure/Number',$ContractEnclosure['Number']);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Number = SPrintF('%s.%s',$Number,$Comp);
            #-------------------------------------------------------------------
            $Tmp = System_Element('tmp');
            if(Is_Error($Tmp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $File = SPrintF('Contract%s.pdf',Md5($_SERVER['REMOTE_ADDR']));
            #-------------------------------------------------------------------
            $IsWrite = IO_Write(SPrintF('%s/files/%s',$Tmp,$File),$PDF,TRUE);
            if(Is_Error($IsWrite))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            return Array('Status'=>'Ok','Location'=>SPrintF('/GetTemp?File=%s&Name=ContractEnclosure%s.pdf&Mime=application/pdf',$File,$Number));
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
