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
$EdeskID   = (integer) @$Args['EdeskID'];
$MessageID = (integer) @$Args['MessageID'];
$Message   =  (string) @$Args['Message'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(IsSet($GLOBALS['__USER']['IsEmulate']))
	return new gException('DENY_WRITE_MESSAGE_FROM_ANOTHER_USER','Нельзя писать сообщения от имени другого пользователя');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Message)
  return new gException('MESSAGE_IS_EMPTY','Введите сообщение');
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Tree.php','libs/Upload.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
if($MessageID){
  #-----------------------------------------------------------------------------
  if($__USER['IsAdmin']){
      #-------------------------------------------------------------------------
      $UMessage = Array('Content'=>$Message);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('EdesksMessages',$UMessage,Array('ID'=>$MessageID));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      return Array('Status'=>'Ok');
  }
}else{
  #-----------------------------------------------------------------------------
  $Edesk = DB_Select('Edesks',Array('ID','TargetGroupID','Theme'),Array('UNIQ','ID'=>$EdeskID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Edesk)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return ERROR | @Trigger_Error(400);
    case 'array':
      #-------------------------------------------------------------------------
      $Entrance = Tree_Entrance('Groups',(integer)$Edesk['TargetGroupID']);
      #-------------------------------------------------------------------------
      switch(ValueOf($Entrance)){
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
      #-------------------------------------------------------------------------
      if(!In_Array($__USER['GroupID'],$Entrance))
        return ERROR | @Trigger_Error(700);
      #-------------------------------------------------------------------------
      $IEdeskMessage = Array(
        #-----------------------------------------------------------------------
        'UserID'  => $__USER['ID'],
        'EdeskID' => $Edesk['ID'],
        'Content' => $Message
      );
      #-------------------------------------------------------------------------
      $Upload = Upload_Get('EdesksMessageFile');
      #-------------------------------------------------------------------------
      switch(ValueOf($Upload)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          # No more...
        break;
        case 'array':
          #---------------------------------------------------------------------
          $IEdeskMessage['FileName'] = $Upload['Name'];
        break;
        default:
          return ERROR | @Trigger_Error(101);
      }
      #-------------------------------------------------------------------------
      $Users = DB_Select('Users','ID',Array('Where'=>SPrintF('`ID` IN (SELECT `UserID` FROM `EdesksMessages` WHERE `EdeskID` = %u) AND `ID` != %u AND `ID` > 50',$Edesk['ID'],$__USER['ID'])));
      #-------------------------------------------------------------------------
      switch(ValueOf($Users)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          # No more...
        break;
        case 'array':
          #---------------------------------------------------------------------
          foreach($Users as $User){
            #-------------------------------------------------------------------
            $IsSend = NotificationManager::sendMsg('EdeskMessageCreate',(integer)$User['ID'],Array('EdeskID'=>$Edesk['ID'],'Theme'=>$Edesk['Theme'],'Message'=>$Message));
            #-------------------------------------------------------------------
            switch(ValueOf($IsSend)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                # No more...
              case 'true':
                # No more...
              break;
              default:
                return ERROR | @Trigger_Error(101);
            }
          }
        break;
        default:
          return ERROR | @Trigger_Error(101);
      }
      #-------------------------------------------------------------------------
      $MessageID = DB_Insert('EdesksMessages',$IEdeskMessage);
      if(Is_Error($MessageID))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      if(IsSet($IEdeskMessage['FileName']))
        if(!SaveUploadedFile('EdesksMessages', $MessageID, $Upload['Data']))
          return new gException('CANNOT_SAVE_UPLOADED_FILE','Не удалось сохранить загруженный файл');
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Edesks',Array('UpdateDate'=>Time()),Array('ID'=>$Edesk['ID']));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      return Array('Status'=>'Ok');
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------

?>
