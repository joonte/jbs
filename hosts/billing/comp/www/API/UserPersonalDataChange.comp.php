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
$Name    =  (string) @$Args['Name'];
$Sign    =  (string) @$Args['Sign'];
$Email   =  (string) @$Args['Email'];
$ICQ     =  (string) @$Args['ICQ'];
$JabberID=  (string) @$Args['JabberID'];
$Mobile  =  (string) @$Args['Mobile'];
$IsClear = (boolean) @$Args['IsClear'];
$CacheID2 = Md5('mobileconfirm'.$GLOBALS['__USER']['ID']);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Upload.php','libs/Image.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Char'],$Name))
  return new gException('WRONG_NAME','Вы ввели неверное имя');
#-------------------------------------------------------------------------------
if(!$Sign)
  return new gException('SIGN_IS_EMPTY','Укажите Вашу подпись');
#-------------------------------------------------------------------------------
$Email = StrToLower($Email);
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Email'],$Email))
  return new gException('WRONG_EMAIL','Неверно указан электронный адрес');
#-------------------------------------------------------------------------------
$User = DB_Select('Users',Array('ID','Email','Mobile'),Array('UNIQ','ID'=>$GLOBALS['__USER']['ID'])); 
#-------------------------------------------------------------------------------
switch(ValueOf($User)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if($User['Email'] != $Email){
      #-------------------------------------------------------------------------
      $Count = DB_Count('Users',Array('Where'=>SPrintF("`Email` = '%s'",$Email)));
      if(Is_Error($Count)){
        return ERROR | @Trigger_Error(500);
      }
      #-------------------------------------------------------------------------
      if($Count)
        return new gException('USER_EXISTS','Пользователь с таким электронным адресом уже существует в системе');
      #-------------------------------------------------------------------------
      if($GLOBALS['__USER']['EmailConfirmed'] + 3600 < Time())
        return new gException('EMAIL_CONFIRM_TOO_OLD','Вы слишком давно подтверждали ваш почтовый адрес. Смена адреса возможна в течение часа после его подтверждения.');
      #-------------------------------------------------------------------------
    }
    #---------------------------------------------------------------------------
    if($ICQ){
      #-------------------------------------------------------------------------
      if(!Preg_Match($Regulars['ICQ'],$ICQ))
        return new gException('WRONG_ICQ','Неверно указан ICQ-номер');
    }
    #---------------------------------------------------------------------------
    if($JabberID){
      $JabberID = StrToLower($JabberID);
      if(!Preg_Match($Regulars['Email'],$JabberID))
        return new gException('WRONG_JabberID','Неверно указан Jabber ID');
    }
    #---------------------------------------------------------------------------
    if($Mobile){
      #-------------------------------------------------------------------------
      if(!Preg_Match($Regulars['Mobile'],$Mobile))
        return new gException('WRONG_MOBILE','Номер мобильного телефона указан неверно');
    }
    #---------------------------------------------------------------------------
    $UUser = Array(
      #-------------------------------------------------------------------------
      'Name'	=> $Name,
      'Sign'	=> $Sign,
      'Email'	=> $Email,
      'ICQ'	=> $ICQ,
      'JabberID'=> $JabberID,
      'Mobile'	=> $Mobile
    );
    #---------------------------------------------------------------------------
    if($User['Email'] != $Email)
      $UUser['EmailConfirmed'] = 0;
    #---------------------------------------------------------------------------
	if ($User['Mobile'] != $Mobile) {
	    $UUser['MobileConfirmed'] = 0;
	    CacheManager::add($CacheID2, '0');
	}
	#---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    $Upload = Upload_Get('UserFoto');
    #---------------------------------------------------------------------------
    switch(ValueOf($Upload)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        # No more...
      break;
      case 'array':
        #-----------------------------------------------------------------------
        $Foto = $Upload['Data'];
        #-----------------------------------------------------------------------
        $Foto = Image_Resize($Foto,90,110);
        if(Is_Error($Foto))
          return new gException('FOTO_RESIZE_ERROR','Ошибка изменения размеров персональной фотографии');
        #-----------------------------------------------------------------------
        if(!SaveUploadedFile('Users', $User['ID'], $Foto))
          return new gException('CANNOT_SAVE_UPLOADED_FILE','Не удалось сохранить загруженный файл');
        #-----------------------------------------------------------------------
      break;
      default:
        return ERROR | @Trigger_Error(101);
    }
    #---------------------------------------------------------------------------
    if($IsClear){
      if(!DeleteUploadedFile('Users',$User['ID']))
        return new gException('CANNOT_DELETE_FILE','Не удалось удалить связанный файл');
    }
    #---------------------------------------------------------------------------
    $IsUpdate = DB_Update('Users',$UUser,Array('ID'=>$User['ID']));
    if(Is_Error($IsUpdate))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    if($User['Email'] != $Email){
      #---------------------------------------------------------------------------
      $Event = Array(
                    'UserID'        => $GLOBALS['__USER']['ID'],
                    'PriorityID'    => 'Billing',
                    'Text'          => SPrintF('Пользователь сменил свой почтовый адрес с (%s) на (%s)',$User['Email'],$Email)
                    );
      $Event = Comp_Load('Events/EventInsert',$Event);
      if(!$Event)
        return ERROR | @Trigger_Error(500);
      }
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
	if ($User['Mobile'] != $Mobile) {
	    #-----------------------------------------------------------------------
	    $Event = Array(
		'UserID' => $GLOBALS['__USER']['ID'],
		'PriorityID' => 'Billing',
		'Text' => SPrintF('Пользователь сменил свой мобильный телефон с (%s) на (%s)', $User['Mobile'], $Mobile)
	    );
	    $Event = Comp_Load('Events/EventInsert', $Event);
	    if (!$Event)
		return ERROR | @Trigger_Error(500);
	}
	#---------------------------------------------------------------------------
	#---------------------------------------------------------------------------
    return Array('Status'=>'Ok');
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
