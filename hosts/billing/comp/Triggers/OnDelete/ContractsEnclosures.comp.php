<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('ContractEnclosure');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/Upload.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsDelete = DB_Delete('MotionDocuments',Array('Where'=>SPrintF("`UniqID` = 'Enclosure:%s'",$ContractEnclosure['ID'])));
if(Is_Error($IsDelete))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём все файлы и удаляем
$Files = GetUploadedFilesInfo('ContractsEnclosures',$ContractEnclosure['ID']);
#-------------------------------------------------------------------------------
foreach($Files as $File)
	if(!DeleteUploadedFile($File['ID']))
		return new gException('CANNOT_DELETE_FILE','Не удалось удалить связанный файл');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
