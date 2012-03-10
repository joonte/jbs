<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('UserID','IsUpdate');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/Tree.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$User = DB_Select('Users',Array('ID','GroupID','Name','Sign','Email','EmailConfirmed','ICQ','JabberID','Mobile','UniqID','LENGTH(`Foto`) as `Foto`'),Array('UNIQ','ID'=>$UserID));
#-------------------------------------------------------------------------------
switch(ValueOf($User)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $Permission = Permission_Check($GLOBALS['__URI'],(integer)$User['ID']);
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
        #if($UserID != 100){
        if(FALSE){
          #---------------------------------------------------------------------
          $LockID = SPrintF('Semaphore[%s]',$UserID);
          #---------------------------------------------------------------------
          for($Waiting=1;$Waiting<=5;$Waiting++){
            #-------------------------------------------------------------------
            $Free = DB_Query(SPrintF("SELECT IS_FREE_LOCK('%s') as `IsFree`",$LockID));
            if(Is_Error($Free))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Rows = MySQL::Result($Free);
            if(Is_Error($Rows))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            if(Count($Rows) < 1)
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Row = Current($Rows);
            #-------------------------------------------------------------------
            if(!$Row['IsFree']){
              #-----------------------------------------------------------------
              Sleep(1);
              #-----------------------------------------------------------------
              continue;
            }
            #-------------------------------------------------------------------
            $Lock = DB_Query(SPrintF("SELECT GET_LOCK('%s',0) as `IsLocked`",$LockID));
            if(Is_Error($Lock))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Rows = MySQL::Result($Lock);
            if(Is_Error($Rows))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            if(Count($Rows) < 1)
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Row = Current($Rows);
            #-------------------------------------------------------------------
            if($Row['IsLocked'])
              break;
            #-------------------------------------------------------------------
            Usleep(500);
          }
          #---------------------------------------------------------------------
          if($Waiting >= 5)
            return ERROR | @Trigger_Error('Пользователь уже работает в данный момент');
        }
        #-----------------------------------------------------------------------
        $IsQuery = DB_Query(SPrintF('SET @local.__USER_ID = %u',$User['ID']));
        if(Is_Error($IsQuery))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Path = Tree_Path('Groups',(integer)$User['GroupID'],'ID');
        #-----------------------------------------------------------------------
        switch(ValueOf($Path)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            $User['Path'] = $Path;
            #-------------------------------------------------------------------
            $CacheID = SPrintF('Groups/Interface[%s]',Md5(Implode(':',$Path)));
            #-------------------------------------------------------------------
            $InterfaceID = CacheManager::get($CacheID);
            if(Is_Error($InterfaceID)){
              #-----------------------------------------------------------------
              $InterfaceID = '';
              #-----------------------------------------------------------------
              foreach($Path as $GroupID){
                #---------------------------------------------------------------
                $Group = DB_Select('Groups','InterfaceID',Array('UNIQ','ID'=>$GroupID));
                #---------------------------------------------------------------
                switch(ValueOf($Group)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
                    if($Group['InterfaceID']){
                      #---------------------------------------------------------
                      $InterfaceID = $Group['InterfaceID'];
                      #---------------------------------------------------------
                      break 2;
                    }
                    #-----------------------------------------------------------
                  break;
                  default:
                    return ERROR | @Trigger_Error(101);
                }
              }
              #-----------------------------------------------------------------
              CacheManager::add($CacheID,$InterfaceID);
            }
            #-------------------------------------------------------------------
            $User['InterfaceID'] = $InterfaceID;
            #-------------------------------------------------------------------
            $IsQuery = DB_Query(SPrintF("SET @local.__USER_GROUPS_PATH = '%s'",Implode(',',Array_Reverse($Path))));
            if(Is_Error($IsQuery))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Entrance = Tree_Entrance('Groups',(integer)$User['GroupID']);
            #-------------------------------------------------------------------
            switch(ValueOf($Entrance)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return ERROR | @Trigger_Error(400);
              case 'array':
                #---------------------------------------------------------------
                $IsQuery = DB_Query(SPrintF("SET @local.__USER_GROUPS_ENTRANCE = '%s'",Implode(',',$Entrance)));
                if(Is_Error($IsQuery))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                if($IsUpdate){
                  #-------------------------------------------------------------
                  #-----------------------------------------------------------------------
                  # проверяем, если это админ шарится под юзером - пихаем в запрос его ID, а не юзера
                  $Session = new Session((string)@$_COOKIE['SessionID']);
                  #-----------------------------------------------------------------------------
                  $IsLoad = $Session->Load();
                  if(Is_Error($IsLoad))
                    return ERROR | @Trigger_Error(500);
                  #-----------------------------------------------------------------------
                  Debug("[Users/Init]: visible UserID = $UserID; RootID = " . @$Session->Data['RootID']);
		  if(IsSet($Session->Data['RootID'])){
                    if($UserID != @$Session->Data['RootID']){
                      # юзер шарится не под самим собой
                      $UserID = @$Session->Data['RootID'];
		      # added by lissyara 2011-12-28 in 09:06 MSK, for JBS-248
		      $User['IsEmulate'] = TRUE;
                    }else{
		      $UserID = $User['ID'];
		    }
		  }else{
		    $UserID = $User['ID'];
		  }
		  Debug("[Users/Init]: real UserID = $UserID; RootID = " . @$Session->Data['RootID']);
                  $IsUpdated = DB_Update('Users',Array('EnterDate'=>Time(),'EnterIP'=>$_SERVER['REMOTE_ADDR']),Array('ID'=>$UserID));
                  if(Is_Error($IsUpdated))
                    return ERROR | @Trigger_Error(500);
                }
                #---------------------------------------------------------------
                $GLOBALS['__USER'] = $User;
                #---------------------------------------------------------------
                return $User;
              default:
                return ERROR | @Trigger_Error(101);
            }
          default:
            return ERROR | @Trigger_Error(101);
        }
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------


?>
