<?php
#-------------------------------------------------------------------------------
/** @author Бреславский А.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
function Upload_Get($Name){
  /****************************************************************************/
  $__args_types = Array('string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Args = Args();
  #-----------------------------------------------------------------------------
  $Hash = @$Args[$Name];
  #-----------------------------------------------------------------------------
  if(!$Hash)
    return new gException('HASH_IS_EMPTY','Хешь файла загрузки пуст');
  #-----------------------------------------------------------------------------
  $Tmp = System_Element('tmp');
  if(Is_Error($Tmp))
    return ERROR | @Trigger_Error('[Upload_Get]: не удалось получить путь до временной директории');
  #-----------------------------------------------------------------------------
  $Uploads = SPrintF('%s/uploads',$Tmp);
  #-----------------------------------------------------------------------------
  if(!File_Exists($Uploads))
    return new gException('HASH_IS_EMPTY','Директория файлов загрузки не создана');
  #-----------------------------------------------------------------------------
  $Path = SPrintF('%s/%s',$Uploads,$Hash);
  #-----------------------------------------------------------------------------
  if(!File_Exists($Path))
    return new gException('FILE_NOT_FOUND','Файл не неайден на сервере');
  #-----------------------------------------------------------------------------
  $Data = IO_Read($Path);
  if(Is_Error($Data))
    return ERROR | @Trigger_Error('[Upload_Get]: не удалось прочитать файл');
  #-----------------------------------------------------------------------------
  $Names = IO_Read(SPrintF('%s/names.txt',$Uploads));
  if(Is_Error($Names))
    return ERROR | @Trigger_Error('[Upload_Get]: не удалось прочитать файл имен');
  #-----------------------------------------------------------------------------
  $Names = UnSerialize($Names);
  #-----------------------------------------------------------------------------
  $Name = (IsSet($Names[$Hash])?$Names[$Hash]:'Default');
  #-----------------------------------------------------------------------------
  return Array('Name'=>$Name,'Data'=>$Data);
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# added by lissyara, 2011-12-02 in 13:41 MSK, for JBS-210
function SaveUploadedFile($Table, $ID, $File){
        $FilePaths = GetFilePath($Table, $ID);
        # создаём директорию
        if(!file_exists($FilePaths['FileDir'])){
                if(!mkdir($FilePaths['FileDir'], 0700, true)){
                        return new gException('CANNOT_CREATE_DIRECTORY','Не удалось создать директорию для сохранения файла');
                }
        }
        # сохраняем файл
        $fp = fopen($FilePaths['FilePath'], 'w');
        fwrite($fp, $File);
        fclose($fp);

        return TRUE;
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# added by lissyara, 2011-12-02 in 14:10 MSK, for JBS-210
function GetUploadedFile($Table, $ID){
        $FilePaths = GetFilePath($Table, $ID);
        # проверяем наличие файла
        if(file_exists($FilePaths['FilePath'])){
                $Data = IO_Read($FilePaths['FilePath']);
                if(Is_Error($Data))
                        return ERROR | @Trigger_Error('[Upload_Get]: не удалось прочитать файл');
                return  Array('Data'=>$Data);
        }else{
                return FALSE;
        }
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# added by lissyara, 2011-12-02 in 15:15 MSK, for JBS-210
function GetFilePath($Table, $ID){
        # директория файлов
        $DirPath = SPrintF('%s/hosts/%s/files/%s',SYSTEM_PATH,HOST_ID,$Table);
        Debug("[root/system/libs/Upload]: DirPath = " . $DirPath);
        # путь к файлу
	$SubDirPath = '';
	$IDa = $ID;
	while ($IDa > 0) {
		$SubDirPath = '/' . ($IDa % 100) . $SubDirPath;
		$IDa = floor($IDa / 100);
	}
        $FileDirPath = $DirPath . $SubDirPath;
        $FilePath = $FileDirPath . "/" . $ID . ".bin";
        Debug("[root/system/libs/Upload]: FilePath = " . $FilePath);

        return Array('FileDir'=>$FileDirPath, 'FilePath'=>$FilePath);
}


#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# added by lissyara, 2011-12-02 in 19:58 MSK, for JBS-210
function GetUploadedFileSize($Table, $ID){
        $FilePaths = GetFilePath($Table, $ID);
        # проверяем наличие файла
        if(file_exists($FilePaths['FilePath'])){
                $st = Stat($FilePaths['FilePath']);
                Debug("[root/system/libs/Upload]: FilePath = " . $FilePaths['FilePath'] . " size = " . $st['size']);
                return $st['size'];
        }
        # файла нет - размера нет, возвращаем FALSE
	Debug("[root/system/libs/Upload]: File not found, FilePath = " . $FilePaths['FilePath']);
        return FALSE;
}



?>
