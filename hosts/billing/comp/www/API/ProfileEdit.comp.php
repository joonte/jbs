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
$ProfileID	= (integer) @$Args['ProfileID'];
$TemplateID	=  (string) @$Args['TemplateID'];
$Simple		=  (string) @$Args['Simple'];
$Window		=  (string) @$Args['Window'];
$Agree		= (boolean) @$Args['Agree'];
$AgreeHandle	= (boolean) @$Args['AgreeHandle'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Upload.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Agree)
	return new gException('NOT_AGREE','Вы не дали согласия на передачу информации');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$AgreeHandle)
	return new gException('NOT_AGREE_HANDLE','Вы не дали согласия на обработку ваших персональных данных');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
if($ProfileID){
  #-----------------------------------------------------------------------------
  $Profile = DB_Select('Profiles',Array('UserID','TemplateID'),Array('UNIQ','ID'=>$ProfileID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Profile)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return ERROR | @Trigger_Error(400);
    case 'array':
      #-------------------------------------------------------------------------
      $IsPermission = Permission_Check('ProfileEdit',(integer)$__USER['ID'],(integer)$Profile['UserID']);
      #-------------------------------------------------------------------------
      switch(ValueOf($IsPermission)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return ERROR | @Trigger_Error(400);
        case 'false':
          return ERROR | @Trigger_Error(700);
        case 'true':
          $TemplateID = $Profile['TemplateID'];
        break 2;
        default:
          return ERROR | @Trigger_Error(101);
      }
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
$Template = System_XML(SPrintF('profiles/%s.xml',$TemplateID));
if(Is_Error($Template))
  return new gException('ERROR_TEMPLATE_LOAD','Ошибка загрузки шаблона');
#-------------------------------------------------------------------------------
$Params = $Template['Attribs'];
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
$Errors = $Attribs = Array();
#-------------------------------------------------------------------------------
if($Simple){
  #-----------------------------------------------------------------------------
  $Simple = @JSON_Decode(Base64_Decode($Simple),TRUE);
  if(!$Simple)
    return ERROR | @Trigger_Error(500);
}else
  $Simple = Array();
#-------------------------------------------------------------------------------
foreach(Array_Keys($Params) as $AttribID){
  #-----------------------------------------------------------------------------
  $Attrib = $Params[$AttribID];
  #-----------------------------------------------------------------------------
  $Value = (IsSet($Args[$AttribID])?$Args[$AttribID]:$Params[$AttribID]['Value']);
  #-----------------------------------------------------------------------------
  $Attribs[$AttribID] = $Value;
  #-----------------------------------------------------------------------------
  if(Count($Simple)){
    #---------------------------------------------------------------------------
    if(IsSet($Simple[$AttribID]))
      $Attrib['IsDuty'] = $Simple[$AttribID];
    else
      continue;
  }
  #-----------------------------------------------------------------------------
  switch($Attrib['Type']){
    case 'Input':
      # No more...
    case 'TextArea':
      #-------------------------------------------------------------------------
      if($Value){
        #-----------------------------------------------------------------------
        $Check = $Attrib['Check'];
        #-----------------------------------------------------------------------
        if(IsSet($Regulars[$Check]))
          $Check = $Regulars[$Check];
        #-----------------------------------------------------------------------
        if(!Preg_Match($Check,$Value))
          $Errors[] = $AttribID;
      }else{
        #-----------------------------------------------------------------------
        if($Attrib['IsDuty'])
          $Errors[] = $AttribID;
      }
    break;
    case 'Select':
      #-------------------------------------------------------------------------
      if(!IsSet($Attrib['Options'][$Value]))
        $Errors[] = $AttribID;
    break;
    default:
      return ERROR | @Trigger_Error(100);
  }
}
#-------------------------------------------------------------------------------
if(Count($Errors)){
  #-----------------------------------------------------------------------------
  $Attribs = $Template['Attribs'];
  #-----------------------------------------------------------------------------
  $Parent = NULL;
  #-----------------------------------------------------------------------------
  $Errors = Array_Reverse($Errors);
  #-----------------------------------------------------------------------------
  foreach($Errors as $AttribID){
    #---------------------------------------------------------------------------
    $Attrib = $Attribs[$AttribID];
    #---------------------------------------------------------------------------
    $Exception = new gException(StrToUpper($AttribID),$Attrib['Comment'],$Parent);
    #---------------------------------------------------------------------------
    $Parent = $Exception;
  }
  #-----------------------------------------------------------------------------
  return new gException('FIELDS_WRONG_FILLED','Не верно заполнены поля',$Exception);
}
#-------------------------------------------------------------------------------
$Replace = Array_ToLine($Attribs,'%');
#-------------------------------------------------------------------------------
$ProfileName = $Template['ProfileName'];
#-------------------------------------------------------------------------------
foreach(Array_Keys($Replace) as $Key)
  $ProfileName = Str_Replace($Key,$Replace[$Key],$ProfileName);
#-----------------------------TRANSACTION---------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('ProfileEdit'))))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$UProfile = Array('Name'=>$ProfileName,'Attribs'=>$Attribs);
#-------------------------------------------------------------------------------
$Upload = Upload_Get('Document');
#-------------------------------------------------------------------------------
switch(ValueOf($Upload)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $Format = SubStr($Upload['Name'],StrrPos($Upload['Name'],'.')+1);
    #---------------------------------------------------------------------------
    $UProfile = Array_Merge($UProfile,Array('Format'=>StrToLower($Format)));
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Answer = Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
if($ProfileID){
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('Profiles',$UProfile,Array('ID'=>$ProfileID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Contracts = DB_Select('Contracts',Array('ID','StatusID'),Array('Where'=>SPrintF('`ProfileID` = %u',$ProfileID)));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Contracts)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      # No more...
    break;
    case 'array':
      #-------------------------------------------------------------------------
      foreach($Contracts as $Contract){
        #-----------------------------------------------------------------------
        $ContractID = (integer)$Contract['ID'];
        #-----------------------------------------------------------------------
        if(!Count($Simple)){
          #---------------------------------------------------------------------
          if($Contract['StatusID'] == 'OnForming'){
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Contracts/Build',$ContractID);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Contracts','StatusID'=>'Public','RowsIDs'=>$ContractID,'Comment'=>'Заполнение профиля'));
            #-------------------------------------------------------------------
            switch(ValueOf($Comp)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return ERROR | @Trigger_Error(400);
              case 'array':
                # No more...
              break;
              default:
                return ERROR | @Trigger_Error(101);
            }
          }
        }
      }
    break;
    default:
      return ERROR | @Trigger_Error(100);
  }
}else{
  #-----------------------------------------------------------------------------
  $UProfile = Array_Merge($UProfile,Array('TemplateID'=>$TemplateID,'UserID'=>$__USER['ID']));
  #-----------------------------------------------------------------------------
  $ProfileID = DB_Insert('Profiles',$UProfile);
  if(Is_Error($ProfileID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  if($__USER['ID'] == 100){
    #---------------------------------------------------------------------------
    $Count = DB_Count('Profiles',Array('ID'=>100));
    if(Is_Error($Count))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if(!$Count){
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Profiles',Array('ID'=>100),Array('ID'=>$ProfileID));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $ProfileID = 100;
    }
  }
  #-----------------------------------------------------------------------------
  $Answer['ProfileID'] = $ProfileID;
}
#-----------------------------------------------------------------------------
#-----------------------------------------------------------------------------
if(IsSet($UProfile['Format']))
  if(!SaveUploadedFile('Profiles', $ProfileID, $Upload['Data']))
    return new gException('CANNOT_SAVE_UPLOADED_FILE','Не удалось сохранить загруженный файл');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Profiles','StatusID'=>(Count($Simple)?'OnFilling':'Filled'),'RowsIDs'=>$ProfileID,'Comment'=>'Правка уже заполненного профиля'));
#-------------------------------------------------------------------------------
switch(ValueOf($Comp)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    # No more...
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
if(Is_Error(DB_Commit($TransactionID)))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Window){
  #-----------------------------------------------------------------------------
  $Window = JSON_Decode(Base64_Decode($Window),TRUE);
  #-----------------------------------------------------------------------------
  $Window['Args']['ProfileID'] = $ProfileID;
  #-----------------------------------------------------------------------------
  $Answer = Array('Status'=>'Window','Window'=>$Window);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# реализация JBS-539 - активизируем задачи по регистрации доменов этого юзера
$Where = Array(
		"`IsExecuted` = 'no'",
		"`IsActive` = 'no'",
		"`TypeID` = 'DomainRegister'",
		SPrintF('`UserID` = %u',$__USER['ID'])
		);
$Tasks = DB_Select('Tasks',Array('ID'),Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($Tasks)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# no tasks
	break;
case 'array':
	# активируем задачи
	foreach($Tasks as $Task){
		$IsUpdate = DB_Update('Tasks',Array('ExecuteDate'=>Time(),'Errors'=>0,'Result'=>'','IsActive'=>TRUE),Array('ID'=>$Task['ID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
	}
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Answer;
#-------------------------------------------------------------------------------

?>
