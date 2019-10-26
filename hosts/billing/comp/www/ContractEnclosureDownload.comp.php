<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$ContractEnclosureID = (integer) @$Args['ContractEnclosureID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/WkHtmlToPdf.php','libs/Upload.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!GetUploadedFileSize('ContractsEnclosures',$ContractEnclosureID))
	return new gException('DOCUMENT_NOT_BUILDED','Документ не сформирован');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array('ID','ContractID','Number','UserID','TypeID');
#-------------------------------------------------------------------------------
$ContractEnclosure = DB_Select('ContractsEnclosuresOwners',$Columns,Array('UNIQ','ID'=>$ContractEnclosureID));
#-------------------------------------------------------------------------------
switch(ValueOf($ContractEnclosure)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('CONTRACT_ENCLOSURE_NOT_FOUND','Приложение к договору не найдено');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Permission = Permission_Check('ContractEnclosureRead',(integer)$GLOBALS['__USER']['ID'],(integer)$ContractEnclosure['UserID']);
#-------------------------------------------------------------------------------
switch(ValueOf($Permission)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'false':
	return ERROR | @Trigger_Error(700);
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$File = GetUploadedFile('ContractsEnclosures',$ContractEnclosureID);
$PDF = WkHtmlToPdf_CreatePDF('ContractEnclosure',$File['Data']);
#-------------------------------------------------------------------------------
switch(ValueOf($PDF)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'string':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Contract/Number',$ContractEnclosure['ContractID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Number = $Comp;
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Contract/Enclosure/Number',$ContractEnclosure['Number']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Number = SPrintF('%s.%s',$Number,$Comp);
#-------------------------------------------------------------------------------
$Tmp = System_Element('tmp');
if(Is_Error($Tmp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$File = SPrintF('Contract%s.pdf',Md5($_SERVER['REMOTE_ADDR']));
#-------------------------------------------------------------------------------
$IsWrite = IO_Write(SPrintF('%s/files/%s',$Tmp,$File),$PDF,TRUE);
if(Is_Error($IsWrite))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','Location'=>SPrintF('/GetTemp?File=%s&Name=ContractEnclosure%s.pdf&Mime=application/pdf',$File,$Number));
#-------------------------------------------------------------------------------

?>
