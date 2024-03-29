<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Upload.php','libs/Image.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$ServiceID		= (integer) @$Args['ServiceID'];
$GroupID		= (integer) @$Args['GroupID'];
$UserID			= (integer) @$Args['UserID'];
$ServicesGroupID	= (integer) @$Args['ServicesGroupID'];
$Name			=  (string) @$Args['Name'];
$NameShort		=  (string) @$Args['NameShort'];
$Item			=  (string) @$Args['Item'];
$Measure		=  (string) @$Args['Measure'];
$ConsiderTypeID		=  (string) @$Args['ConsiderTypeID'];
$CostOn			=   (float) @$Args['CostOn'];
$Cost			=   (float) @$Args['Cost'];
$PartnersRewardPercent	=  (double) @$Args['PartnersRewardPercent'];
$IsActive		= (boolean) @$Args['IsActive'];
$IsProlong		= (boolean) @$Args['IsProlong'];
$IsConditionally	= (boolean) @$Args['IsConditionally'];
$Statuses		=   (array) @$Args['Statuses'];
$Tags			=  (string) @$Args['Tags'];
$IsAutoInvoicing	= (boolean) @$Args['IsAutoInvoicing'];
$Priority		= (integer) @$Args['Priority'];
$SortID			= (integer) @$Args['SortID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = Array(SPrintF("`Name` = '%s'",$Name));
#-------------------------------------------------------------------------------
if($ServiceID)
	$Where[] = SPrintF("`ID` != %u",$ServiceID);
#-------------------------------------------------------------------------------
$Count = DB_Count('Services',Array('Where'=>$Where));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count)
	return new gException('DUPLICATE_SERVICE_NAME',SPrintF('Услуга с именем "%s" уже есть. Выберите другое имя услуги',$Name));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = Array(SPrintF("`NameShort` = '%s'",$NameShort));
#-------------------------------------------------------------------------------
if($ServiceID)
	$Where[] = SPrintF("`ID` != %u",$ServiceID);
#-------------------------------------------------------------------------------
$Count = DB_Count('Services',Array('Where'=>$Where));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count)
	return new gException('DUPLICATE_SERVICE_SHORT_NAME',SPrintF('Услуга с коротким именем "%s" уже есть. Выберите другое сокращённое имя услуги',$NameShort));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = Array(SPrintF("`Item` = '%s'",$Item));
#-------------------------------------------------------------------------------
if($ServiceID)
	$Where[] = SPrintF("`ID` != %u",$ServiceID);
#-------------------------------------------------------------------------------
$Count = DB_Count('Services',Array('Where'=>$Where));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count)
	return new gException('DUPLICATE_SERVICE_ITEM',SPrintF('Услуга с именем "%s" для раздела меню уже есть. Выберите другое имя услуги для меню',$Item));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// статусы
#Debug(SPrintF('[comp/www/Administrator/API/ServiceEdit]: Statuses = %s',print_r($Statuses,true)));
$Params = Array('Statuses'=>$Statuses);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// теги
foreach(Explode("\n",$Tags) as $Line){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/Administrator/API/ServiceEdit]: Line = %s',$Line));
	$Line = Explode(':',Trim($Line));
	#-------------------------------------------------------------------------------
	$Params['Tags'][Current($Line)] = Explode(',',Next($Line));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IService = Array(
		#-------------------------------------------------------------------------------
		'GroupID'		=> $GroupID,
		'UserID'		=> $UserID,
		'ServicesGroupID'	=> $ServicesGroupID,
		'Name'			=> $Name,
		'NameShort'		=> $NameShort,
		'Item'			=> $Item,
		'Measure'		=> $Measure,
		'ConsiderTypeID'	=> $ConsiderTypeID,
		'CostOn'		=> $CostOn,
		'Cost'			=> $Cost,
		'PartnersRewardPercent'	=> $PartnersRewardPercent,
		'IsActive'		=> $IsActive,
		'IsProlong'		=> $IsProlong,
		'IsConditionally'	=> $IsConditionally,
		'IsAutoInvoicing'	=> $IsAutoInvoicing,
		'Params'		=> $Params,
		'Priority'		=> $Priority,
		'SortID'		=> $SortID
		#-------------------------------------------------------------------------------
		);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Answer = Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
if($ServiceID){
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('Services',$IService,Array('ID'=>$ServiceID));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$ServiceID = DB_Insert('Services',$IService);
	if(Is_Error($ServiceID))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Answer['ServiceID'] = $ServiceID;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Files = Upload_Get('Emblem');
#-------------------------------------------------------------------------------
switch(ValueOf($Files)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	// достаём все старые файлы, их надо будет удалить, иначе так и будут болтаться
	$FilesOld = GetUploadedFilesInfo('Services',$ServiceID);
	#-------------------------------------------------------------------------------
	// работаем только с первым файлом
	$Emblem = $Files[0]['Data'];
	#-------------------------------------------------------------------------------
	$Emblem = Image_Resize($Emblem,72,72);
	#-------------------------------------------------------------------------------
	if(Is_Error($Emblem))
		return new gException('EMBLEM_RESIZE_ERROR','Ошибка изменения размеров эмблемы. Приложите изображение в формате: jpeg, gif, png');
	#-------------------------------------------------------------------------------
	// заново строим массив с одним файлом с преобразованной фоткой
	$Files = Array(
			Array(
				'Name' => SPrintF('Emblem%s.jpeg',$ServiceID),
				'Data' => $Emblem,
				'Size' => Mb_StrLen($Emblem,'8bit'),
				'Mime' => 'image/jpeg'
				)
			);
	#-------------------------------------------------------------------------------
	// сохраняем файлы в таблицу
	if(Is_Error(SaveUploadedFile($Files,'Services',$ServiceID)))
		return new gException('CANNOT_SAVE_UPLOADED_FILES','Не удалось сохранить загруженный файл');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// удаляем все старые файлы
	if(SizeOf($FilesOld) > 0)
		foreach($FilesOld as $File)
			if(!DeleteUploadedFile($File['ID']))
				return new gException('CANNOT_DELETE_FILE','Не удалось удалить связанный файл');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsFlush = CacheManager::flush();
if(!$IsFlush)
	@Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Answer;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
