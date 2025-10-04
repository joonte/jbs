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
$UserID = (integer) @$Args['UserID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/Session.class.php','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Session = new Session((string)@$_COOKIE['SessionID']);
#-------------------------------------------------------------------------------
if(Is_Error($Session->Load()))
  return ERROR | @Trigger_Error(400);
#-------------------------------------------------------------------------------
if(!IsSet($Session->Data['UsersIDs']))
  return ERROR | @Trigger_Error(400);
#-------------------------------------------------------------------------------
$UsersIDs = &$Session->Data['UsersIDs'];
if(Count($UsersIDs) < 1)
  return ERROR | @Trigger_Error(400);
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
if($UserID){
  #-----------------------------------------------------------------------------
  $User = DB_Select('Users',Array('ID','IsActive'),Array('UNIQ','ID'=>$UserID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($User)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return ERROR | @Trigger_Error(400);
    case 'array':
      #-------------------------------------------------------------------------
      $UserID = $User['ID'];
      #-------------------------------------------------------------------------
      if(!In_Array($UserID,$UsersIDs)){
        #-----------------------------------------------------------------------
        $IsPermission = Permission_Check('UsersSwitch',(integer)$__USER['ID'],(integer)$UserID);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsPermission)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'false':
            return new gException('USER_MANAGMENT_DISABLED','Управление пользователем запрещено');
          case 'true':
            Array_UnShift($UsersIDs,$UserID);
          break;
          default:
            return ERROR | @Trigger_Error(101);
        }
      }else{
        #-----------------------------------------------------------------------
        $Temp = $UsersIDs[$Index = Array_Search($UserID,$UsersIDs)];
        #-----------------------------------------------------------------------
        $UsersIDs[$Index] = Current($UsersIDs);
        $UsersIDs[0] = $Temp;
      }
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}else{
  #-----------------------------------------------------------------------------
  $UserID = $Session->Data['RootID'];
  #-----------------------------------------------------------------------------
  $UsersIDs = Array($UserID);
}
#-------------------------------------------------------------------------------
if(!SetCookie(Md5($__USER['ID']),@$_SERVER['HTTP_REFERER'],Time() + 86400,'/',SPrintF('.%s',HOST_ID)))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$User = Comp_Load('Users/Init',$UserID);
if(Is_Error($User))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error($Session->Save()))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Redirect = SPrintF('/%s/Home',$User['InterfaceID']);
#-------------------------------------------------------------------------------
if(IsSet($_COOKIE[$Key = Md5($UserID)]))
  $Redirect = $_COOKIE[$Key];
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','Redirect'=>$Redirect);
#-------------------------------------------------------------------------------

?>
