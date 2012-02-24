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
$ContractID = (integer) @$Args['ContractID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/HTMLDoc.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Contract = DB_Select('Contracts',Array('ID','UserID','Document'),Array('UNIQ','ID'=>$ContractID));
#-------------------------------------------------------------------------------
switch(ValueOf($Contract)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('CONTRACT_NOT_FOUND','Договор не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $Document = $Contract['Document'];
    #---------------------------------------------------------------------------
    if(!$Document)
      return new gException('DOCUMENT_NOT_BUILDED','Документ не сформирован');
    #---------------------------------------------------------------------------
    $Permission = Permission_Check('ContractRead',(integer)$GLOBALS['__USER']['ID'],(integer)$Contract['UserID']);
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
        $PDF = HTMLDoc_CreatePDF('Contract',$Contract['Document']);
        #-----------------------------------------------------------------------
        switch(ValueOf($PDF)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'string':
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Formats/Contract/Number',$Contract['ID']);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
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
            return Array('Status'=>'Ok','Location'=>SPrintF('/GetTemp?File=%s&Name=Contract%s.pdf&Mime=application/pdf',$File,$Comp));
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
