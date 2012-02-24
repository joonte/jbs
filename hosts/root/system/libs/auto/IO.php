<?php
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
function IO_Read($Path,$IsUseLinks = TRUE){
  /****************************************************************************/
  $__args_types = Array('string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  Debug(SPrintF('[IO_Read]: открытие файла (%s)',$Path));
  #-----------------------------------------------------------------------------
  if(!File_Exists($Path))
    return ERROR | @Trigger_Error('[IO_Read]: файл не существует');
  #-----------------------------------------------------------------------------
  if(!$File = @Fopen($Path,'r'))
    return ERROR | @Trigger_Error('[IO_Read]: ошибка открытия файла');
  #-----------------------------------------------------------------------------
  $Size = @FileSize($Path);
  if(!$Size)
    return '';
  #-----------------------------------------------------------------------------
  if(!$Result = @Fread($File,$Size))
    return ERROR | @Trigger_Error('[IO_Read]: ошибка чтения файла');
  #-----------------------------------------------------------------------------
  if(!Fclose($File))
    return ERROR | @Trigger_Error('[IO_Read]: ошибка закрытия файла');
  #-----------------------------------------------------------------------------
  if(Mb_StrLen($Result) > 4){
    #---------------------------------------------------------------------------
    if(Mb_SubStr($Result,1,4) == 'link' && $IsUseLinks){
      #-------------------------------------------------------------------------
      $LinkPath = Mb_SubStr($Result,Mb_StrPos($Result,':') + 1);
      #-------------------------------------------------------------------------
      switch($Result{0}){
        case '#': # Абсолюная ссылка
          # No more...
        break;
        case '@': # Относительная ссылка
          #---------------------------------------------------------------------
          $Folder = Mb_SubStr($Path,0,Mb_StrrPos($Path,'/'));
          #---------------------------------------------------------------------
          $LinkPath = SPrintF('%s/%s',$Folder,$LinkPath);
        break;
        default:
          return ERROR | @Trigger_Error('[IO_Read]: тип ссылки не определён');
      }
      #-------------------------------------------------------------------------
      if($Path == $LinkPath)
        return ERROR | @Trigger_Error(SPrintF('[IO_Read]: ссылка сама на себя в файле(%s)',$Path));
      #-------------------------------------------------------------------------
      Debug(SPrintF('[IO_Read]: символическая ссылка (%s) на (%s)',$Path,$LinkPath));
      #-------------------------------------------------------------------------
      $Result = IO_Read($LinkPath);
      if(Is_Error($Result))
        return ERROR | @Trigger_Error('[IO_Read]: ошибка рекурсивного вызова');
    }
  }
  #-----------------------------------------------------------------------------
  return $Result;
}
#-------------------------------------------------------------------------------
function IO_Write($Path,$Data,$IsRewrite = FALSE,$Wait = 3){
  /****************************************************************************/
  $__args_types = Array('string','string','boolean','integer');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  Debug(SPrintF('[IO_Write]: запись в файл (%s)',$Path));
  #-----------------------------------------------------------------------------
  if(File_Exists($Path)){
    #---------------------------------------------------------------------------
    $Start = Time() + $Wait;
    #---------------------------------------------------------------------------
    do{ }while(!Is_Writable($Path) && Time() < $Start);
  }else{
    #---------------------------------------------------------------------------
    $Folder = DirName($Path);
    #---------------------------------------------------------------------------
    if(!File_Exists($Folder)){
      #-------------------------------------------------------------------------
      Debug(SPrintF('[IO_Write]: создание директории (%s)',$Folder));
      #-------------------------------------------------------------------------
      if(!@MkDir($Folder,0777,TRUE))
        return ERROR | @Trigger_Error(SPrintF('[IO_Write]: не возможно создать директорию (%s)',$Folder));
    }
  }
  #-----------------------------------------------------------------------------
  $File = @Fopen($Path,$IsRewrite?'w':'a');
  if(!$File)
    return ERROR | @Trigger_Error('[IO_Write]: ошибка открытия файла');
  #-----------------------------------------------------------------------------
  if(!@Fwrite($File,$Data))
    return ERROR | @Trigger_Error('[IO_Write]: ошибка записи в файл');
  #-----------------------------------------------------------------------------
  if(!@Fclose($File))
    return ERROR | @Trigger_Error('[IO_Write]: ошибка закрытия файла');
  #-----------------------------------------------------------------------------
  //if(Preg_Match('/\/tmp\//',$Path)){
    #---------------------------------------------------------------------------
    //if(!@ChMod($Path,0666))
    //  @Trigger_Error('[IO_Write]: ошибка установки прав на файл');
  //}
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
function IO_Scan($Path,$IsHidden = TRUE){
  /****************************************************************************/
  $__args_types = Array('string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Result = Array();
  #-----------------------------------------------------------------------------
  $Folder = @OpenDir($Path);
  if(!$Folder)
    return ERROR | @Trigger_Error(SPrintF('[IO_Scan]: не возможно открыть директорию (%s)',$Path));
  #-----------------------------------------------------------------------------
  $Ignored = Array('.','..');
  #-----------------------------------------------------------------------------
  if($IsHidden)
    $Ignored = Array_Merge($Ignored,Array('.svn'));
  #-----------------------------------------------------------------------------
  # ReadDir changed to ScanDir by lissyara, for JBS-335
  $Files = ScanDir($Path);
  #while($File = ReadDir($Folder)){
  foreach($Files as $File){
    #---------------------------------------------------------------------------
    if(In_Array($File,$Ignored))
      continue;
    #---------------------------------------------------------------------------
    $Result[] = $File;
  }
  #-----------------------------------------------------------------------------
  CloseDir($Folder);
  #-----------------------------------------------------------------------------
  return $Result;
}
#-------------------------------------------------------------------------------
function IO_Files($Path,&$Result = Array()){
  /****************************************************************************/
  $__args_types = Array('string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Files = IO_Scan($Path);
  if(Is_Error($Files))
    return ERROR | @Trigger_Error('[IO_Files]: не удалось получить содержимое папки');
  #-----------------------------------------------------------------------------
  foreach($Files as $File){
    #---------------------------------------------------------------------------
    $File = SPrintF('%s/%s',$Path,$File);
    #---------------------------------------------------------------------------
    if(Is_Dir($File)){
      #-------------------------------------------------------------------------
      if(Is_Error(IO_Files($File,$Result)))
        return ERROR | @Trigger_Error('[IO_Files]: не удалось осуществить рекурсивный вызов');
    }else{
      #-------------------------------------------------------------------------
      if(Is_File($File))
        $Result[] = $File;
    }
  }
  #-----------------------------------------------------------------------------
  return $Result;
}
#-------------------------------------------------------------------------------
function IO_RmDir($Folder){
  #-----------------------------------------------------------------------------
  $Folder = Preg_Replace('/\/{2,}/','/',rTrim($Folder,'/'));
  #-----------------------------------------------------------------------------
  if(StrPos(SPrintF('/%s/',$Folder),SYSTEM_PATH) === FALSE)
    return ERROR | @Trigger_Error(SPrintF('[IO_RmDir]: ошибка безопасности при удалении (%s)',$Folder));
  #-----------------------------------------------------------------------------
  $Entities = IO_Scan($Folder,FALSE);
  if(Is_Error($Entities))
    return ERROR | @Trigger_Error('[IO_RmDir]: не удалось получить содержимое папки');
  #-----------------------------------------------------------------------------
  if(Count($Entities)){
    #---------------------------------------------------------------------------
    foreach($Entities as $Entity){
      #-------------------------------------------------------------------------
      $Entity = SPrintF('%s/%s',$Folder,$Entity);
      #-------------------------------------------------------------------------
      if(Is_Dir($Entity)){
        #-----------------------------------------------------------------------
        if(Is_Error(IO_RmDir($Entity)))
          return ERROR | @Trigger_Error(SPrintF('[IO_RmDir]: ошибка рекурсивного вызова при удалении (%s)',$Entity));
      }else{
        #-----------------------------------------------------------------------
        if(!UnLink($Entity))
          return ERROR | @Trigger_Error(SPrintF('[IO_RmDir]: ошибка при удалении файла (%s)',$Entity));
      }
    }
  }
  #-----------------------------------------------------------------------------
  if(!@RmDir($Folder))
    return ERROR | @Trigger_Error(SPrintF('[IO_RmDir]: ошибка при удалении директории (%s)',$Folder));
  #-----------------------------------------------------------------------------
  return TRUE;
}
//****************************************************************************//
?>
