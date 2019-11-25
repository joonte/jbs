<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Service');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/Upload.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Service['IsProtected'])
	return new gException('SERVICE_IS_PROTECTED',SPrintF('Услуга (%s) защищена и не может быть удалена',$Service['Name']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Count = DB_Count('Orders',Array('Where'=>SPrintF('`ServiceID` = %u',$Service['ID'])));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count)
	return new gException('SERVICE_ORDERS_EXISTS',SPrintF('Услуга (%s) не может быть удалена, т.к. на нее существует %u заказов',$Service['Name'],$Count));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Count = DB_Count('InvoicesItems',Array('Where'=>SPrintF('`ServiceID` = %u',$Service['ID'])));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count)
	return new gException('SERVICE_INVOICES_EXISTS',SPrintF('Услуга (%s) не может быть удалена, т.к. на неё было выписано %u счетов',$Service['Name'],$Count));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Count = DB_Count('ServersGroups',Array('Where'=>SPrintF('`ServiceID` = %u',$Service['ID'])));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count)
	return new gException('SERVICE_SERVERS_EXISTS',SPrintF('Услуга (%s) не может быть удалена, т.к. для неё настроены %u групп серверов',$Service['Name'],$Count));
#-------------------------------------------------------------------------------
// достаём все файлы и удаляем
$Files = GetUploadedFilesInfo('Services',$Service['ID']);
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
