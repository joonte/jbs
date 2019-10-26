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
$ContractID = (integer) @$Args['ContractID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/WkHtmlToPdf.php','libs/Upload.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Contract = DB_Select('Contracts',Array('ID','UserID'),Array('UNIQ','ID'=>$ContractID));
#-------------------------------------------------------------------------------
# проверяем наличие договора
switch(ValueOf($Contract)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('CONTRACT_NOT_FOUND','Договор не найден');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем наличие файла
if(!GetUploadedFileSize('Contracts',$Contract['ID']))
	return new gException('DOCUMENT_NOT_BUILDED','Документ не сформирован');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем разрешения на скачивание файла
$Permission = Permission_Check('ContractRead',(integer)$GLOBALS['__USER']['ID'],(integer)$Contract['UserID']);
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
# генерим PDF
$File = GetUploadedFile('Contracts',$Contract['ID']);
$PDF = WkHtmlToPdf_CreatePDF('Contract',$File['Data']);
#-------------------------------------------------------------------------------
switch(ValueOf($PDF)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'string':
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Contract/Number',$Contract['ID']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Tmp = System_Element('tmp');
	if(Is_Error($Tmp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$File = SPrintF('Contract_%s.pdf',Md5(MicroTime()));
	#-------------------------------------------------------------------------------
	$IsWrite = IO_Write(SPrintF('%s/files/%s',$Tmp,$File),$PDF,TRUE);
	if(Is_Error($IsWrite))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	return Array('Status'=>'Ok','Location'=>SPrintF('/GetTemp?File=%s&Name=Contract%s.pdf&Mime=application/pdf',$File,$Comp));
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
