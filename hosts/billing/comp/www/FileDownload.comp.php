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
$FileID = (integer) @$Args['FileID'];
$TypeID =  (string) @$Args['TypeID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/HTMLDoc.php','libs/Upload.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$TypeID = DB_Escape($TypeID);
#-------------------------------------------------------------------------------
$FileData = DB_Select($TypeID,'*',Array('UNIQ','ID'=>$FileID));
#-------------------------------------------------------------------------------
switch(ValueOf($FileData)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $Permission = Permission_Check('EdeskRead',(integer)$GLOBALS['__USER']['ID'],(integer)$FileData['UserID']);
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
        $Length = GetUploadedFileSize($TypeID, $FileID);
        #-----------------------------------------------------------------------
        if(!$Length)
          return new gException('CANNOT_GET_FILE_SIZE','Не удалось получить размер файла');
        #-----------------------------------------------------------------------
        $Data = GetUploadedFile($TypeID, $FileID);
	#-----------------------------------------------------------------------
	$FileName = SPrintF('%s.bin',$FileData['ID']);
	if($TypeID == 'EdesksMessages')	{$FileName = $FileData['FileName'];}
	if($TypeID == 'Profiles')	{$FileName = SPrintF('document_%s.%s',$FileData['ID'],$FileData['Format']);}
        #-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
        Header(SPrintF('Content-Type: %s; charset=utf-8',GetFileMimeType($TypeID,$FileID)));
        Header(SPrintF('Content-Length: %u',$Length));
        Header(SPrintF('Content-Disposition: attachment; filename="%s";',$FileName));
        Header('Pragma: nocache');
        #-----------------------------------------------------------------------
        return $Data['Data'];
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------


?>
