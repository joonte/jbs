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
$ServiceID = (integer) @$Args['ServiceID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$CacheID = Md5($__FILE__ . $ServiceID);
$Result = CacheManager::get($CacheID);
if($Result){
	Header('Content-Type: image');
	Header('Cache-Control: private, max-age=86400');
	return $Result;
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/Upload.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем наличие файла
$Files = GetUploadedFiles('Services',$ServiceID);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!SizeOf($Files)){
	#-------------------------------------------------------------------------------
	# нету файла по стандартным путям хранения файлов
	$Service = DB_Select('Services',Array('ID','Code'),Array('UNIQ','ID'=>$ServiceID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Service)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		$Emblem = Styles_Url(SPrintF('Images/Icons/%s.png',$Service['Code']));
		if(Is_Error($Emblem))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/www/ServiceEmblem]: %s',$Emblem));
		#-------------------------------------------------------------------------------
		Header(SPrintF('Location: %s',$Emblem));
		#-------------------------------------------------------------------------------
		return NULL;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// старый вариант, оно не кэшируется, переделал для кого можно на прямые линки (см. выше)
		#$Emblem = Styles_Element(SPrintF('Images/Icons/%s.png',$Service['Code']));
		#if(Is_Error($Emblem))
		#	return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#$Emblem = IO_Read($Emblem);
		#if(Is_Error($Emblem))
		#	return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
        $File = End($Files);
	#-------------------------------------------------------------------------------
	$Emblem = $File['Data'];
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
CacheManager::add($CacheID, $Emblem, 24 * 3600);
#-------------------------------------------------------------------------------
Header('Content-Type: image');
Header('Cache-Control: private, max-age=86400');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Emblem;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
