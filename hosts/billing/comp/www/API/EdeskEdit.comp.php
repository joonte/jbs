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
$Theme   = (string) @$Args['Theme'];
$Message = (string) @$Args['Message'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Upload.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Theme)
  return new gException('THEME_IS_EMPTY','Введите тему обсуждения');
#-------------------------------------------------------------------------------
if(!$Message)
  return new gException('MESSAGE_IS_EMPTY','Введите сообщение');
#-------------------------------------------------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('EdeskEdit'))))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$IEdesk = Array(
  #-----------------------------------------------------------------------------
  'UserID'        => $__USER['ID'],
  'TargetGroupID' => 1,
  'TargetUserID'  => 1,
  'PriorityID'    => 'Low',
  'Theme'         => $Theme,
  'UpdateDate'    =>  Time()
);
#-------------------------------------------------------------------------------
$EdeskID = DB_Insert('Edesks',$IEdesk);
if(Is_Error($EdeskID))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Edesks','StatusID'=>'Opened','RowsIDs'=>$EdeskID));
#-------------------------------------------------------------------------------
switch(ValueOf($Comp)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $IEdeskMessage = Array(
      #-------------------------------------------------------------------------
      'UserID'  => $__USER['ID'],
      'EdeskID' => $EdeskID,
      'Content' => $Message
    );
    #---------------------------------------------------------------------------
    $Upload = Upload_Get('EdeskMessageFile');
    #---------------------------------------------------------------------------
    switch(ValueOf($Upload)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        # No more...
      break;
      case 'array':
        #-----------------------------------------------------------------------
        $IEdeskMessage['FileName'] = $Upload['Name'];
      break;
      default:
        return ERROR | @Trigger_Error(101);
    }
    #---------------------------------------------------------------------------
    $MessageID = DB_Insert('EdesksMessages',$IEdeskMessage);
    if(Is_Error($MessageID))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if(IsSet($IEdeskMessage['FileName']))
      if(!SaveUploadedFile('EdesksMessages', $MessageID, $Upload['Data']))
        return new gException('CANNOT_SAVE_UPLOADED_FILE','Не удалось сохранить загруженный файл');
    #---------------------------------------------------------------------------
    $Users = DB_Select('Users','ID',Array('Where'=>SPrintF('`ID` != %u AND `ID` > 50',$__USER['ID'])));
    #---------------------------------------------------------------------------
    switch(ValueOf($Users)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        #-----------------------------------------------------------------------
        foreach($Users as $User){
          #---------------------------------------------------------------------
          $IsSend = NotificationManager::sendMsg('EdeskCreate',(integer)$User['ID'],Array('EdeskID'=>$EdeskID,'Theme'=>$Theme,'Message'=>$Message));
          #---------------------------------------------------------------------
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
        #-----------------------------------------------------------------------
        if(Is_Error(DB_Commit($TransactionID)))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        return Array('Status'=>'Ok');
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
