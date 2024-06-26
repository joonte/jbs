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
if(Is_Error(System_Load('libs/Mobile_Detect.php')))
        return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Detect = new Mobile_Detect();
#-------------------------------------------------------------------------------
$GLOBALS['IsMobile'] = $Detect->isMobile();
#-------------------------------------------------------------------------------
Debug(SprintF('[Users/Init]: проверка мобильного устройства, IP = %s; $IsMobile = %s; wScreen = %s; hScreen = %s',@$_SERVER['REMOTE_ADDR'],($GLOBALS['IsMobile'])?'TRUE':'FALSE',@$_COOKIE['wScreen'],@$_COOKIE['hScreen']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/Tree.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём даныне пользователя
$Columns = Array(
		'ID','GroupID','RegisterDate','Name','Sign','Email','UniqID','IsActive','LockReason','LayPayMaxSumm','LayPayThreshold','EnterIP','EnterDate','Params','ConfirmedWas',
		'(SELECT SUM(`Summ`) FROM `InvoicesOwners` WHERE `InvoicesOwners`.`UserID` = `Users`.`ID` AND `InvoicesOwners`.`IsPosted` = "yes") AS `InvoicesAmount`',
		);
#-------------------------------------------------------------------------------
$User = DB_Select('Users',$Columns,Array('UNIQ','ID'=>$UserID));
#-------------------------------------------------------------------------------
switch(ValueOf($User)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// чопик для ConfirmedWas
if(!Is_Array($User['ConfirmedWas']))
	$User['ConfirmedWas'] = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Permission = Permission_Check($GLOBALS['__URI'],(integer)$User['ID']);
#-------------------------------------------------------------------------------
switch(ValueOf($Permission)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'false':
	return ERROR | @Trigger_Error(700);
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// выбираем контакты пользователя, нет-нет да нужны
$Contacts = DB_Select('Contacts','*',Array('Where'=>Array(SPrintF('`UserID` = %u',$User['ID']),'`IsHidden` = "no"'),'SortOn'=>Array('MethodID','Address')));
#-------------------------------------------------------------------------------
switch(ValueOf($Contacts)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$User['Contacts'] = $Contacts;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#if($UserID != 100){
if(FALSE){
	#-------------------------------------------------------------------------------
	$LockID = SPrintF('Semaphore[%s]',$UserID);
	#-------------------------------------------------------------------------------
	for($Waiting=1;$Waiting<=5;$Waiting++){
		#-------------------------------------------------------------------------------
		$Free = DB_Query(SPrintF("SELECT IS_FREE_LOCK('%s') as `IsFree`",$LockID));
		if(Is_Error($Free))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Rows = MySQL::Result($Free);
		if(Is_Error($Rows))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if(Count($Rows) < 1)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Row = Current($Rows);
		#-------------------------------------------------------------------------------
		if(!$Row['IsFree']){
			#-------------------------------------------------------------------------------
			Sleep(1);
			#-------------------------------------------------------------------------------
			continue;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Lock = DB_Query(SPrintF("SELECT GET_LOCK('%s',0) as `IsLocked`",$LockID));
		if(Is_Error($Lock))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Rows = MySQL::Result($Lock);
		if(Is_Error($Rows))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if(Count($Rows) < 1)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Row = Current($Rows);
		#-------------------------------------------------------------------------------
		if($Row['IsLocked'])
			break;
		#-------------------------------------------------------------------------------
		Usleep(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if($Waiting >= 5)
		return ERROR | @Trigger_Error('Пользователь уже работает в данный момент');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsQuery = DB_Query(SPrintF('SET @local.__USER_ID = %u',$User['ID']));
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Path = Tree_Path('Groups',(integer)$User['GroupID'],'ID');
#-------------------------------------------------------------------------------
switch(ValueOf($Path)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$User['Path'] = $Path;
#-------------------------------------------------------------------------------
$CacheID = SPrintF('Groups/Interface[%s]',Md5(Implode(':',$Path)));
#-------------------------------------------------------------------------------
$InterfaceID = CacheManager::get($CacheID);
#-------------------------------------------------------------------------------
if(!$InterfaceID){
	#-------------------------------------------------------------------------------
	$InterfaceID = 'v2';
	#-------------------------------------------------------------------------------
	foreach($Path as $GroupID){
		#-------------------------------------------------------------------------------
		$Group = DB_Select('Groups','InterfaceID',Array('UNIQ','ID'=>$GroupID));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Group)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		if($Group['InterfaceID']){
			#-------------------------------------------------------------------------------
			$InterfaceID = $Group['InterfaceID'];
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	CacheManager::add($CacheID,$InterfaceID);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$User['InterfaceID'] = $InterfaceID;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsQuery = DB_Query(SPrintF("SET @local.__USER_GROUPS_PATH = '%s'",Implode(',',Array_Reverse($Path))));
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Entrance = Tree_Entrance('Groups',(integer)$User['GroupID']);
#-------------------------------------------------------------------------------
switch(ValueOf($Entrance)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsQuery = DB_Query(SPrintF("SET @local.__USER_GROUPS_ENTRANCE = '%s'",Implode(',',$Entrance)));
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($IsUpdate){
	#-------------------------------------------------------------------------------
	# проверяем, если это админ шарится под юзером - пихаем в запрос его ID, а не юзера
	$Session = new Session((string)@$_COOKIE['SessionID']);
	#-------------------------------------------------------------------------------
	$IsLoad = $Session->Load();
	if(Is_Error($IsLoad))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($UserID != @$Session->Data['RootID'])
		Debug(SPrintF('[comp/Users/Init]: visible UserID = %s; RootID = %s',$UserID,@$Session->Data['RootID']));
	#-------------------------------------------------------------------------------
	if(IsSet($Session->Data['RootID'])){
		#-------------------------------------------------------------------------------
		if($UserID != @$Session->Data['RootID']){
			#-------------------------------------------------------------------------------
			# юзер шарится не под самим собой
			$UserID = @$Session->Data['RootID'];
			#-------------------------------------------------------------------------------
			# added by lissyara 2011-12-28 in 09:06 MSK, for JBS-248
			$User['IsEmulate'] = TRUE;
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$UserID = $User['ID'];
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$UserID = $User['ID'];
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if($UserID != @$Session->Data['RootID'])
		Debug(SPrintF('[comp/Users/Init]: real UserID = %s; RootID = %s',$UserID,@$Session->Data['RootID']));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// логгируем IP
	$Comp = Comp_Load('Users/LogIP',$UserID,IsSet($GLOBALS['_SERVER']['REMOTE_ADDR'])?$GLOBALS['_SERVER']['REMOTE_ADDR']:'127.0.0.124',IsSet($GLOBALS['_SERVER']['HTTP_USER_AGENT'])?$GLOBALS['_SERVER']['HTTP_USER_AGENT']:'');
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsUpdated = DB_Update('Users',Array('EnterDate'=>Time(),'EnterIP'=>$_SERVER['REMOTE_ADDR']),Array('ID'=>$UserID));
	if(Is_Error($IsUpdated))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$GLOBALS['__USER'] = $User;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsAdmin = Permission_Check('/Administrator/',(integer)$User['ID']);
#-------------------------------------------------------------------------------
switch(ValueOf($IsAdmin)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'false':
	break;
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$GLOBALS['__USER']['IsAdmin'] = $IsAdmin;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!IsSet($GLOBALS['__USER']['IsEmulate'])){
	#-------------------------------------------------------------------------------
	# если юзер задисаблен, не пускаем его дальше
	// TODO - разобраться, а чё админа тоже не пускает-то..
	// неактивен и не гость (guest@system.com).
	if(!$User['IsActive'] && $GLOBALS['__USER']['ID'] != 10){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('www/API/Logout');
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Cached = CacheManager::get(Md5(SPrintF('LastLogon_%s',$User['Email'])));
	#-------------------------------------------------------------------------------
	if(!$Cached)
		CacheManager::add(Md5(SPrintF('LastLogon_%s',$User['Email'])),Array('EnterIP'=>$User['EnterIP'],'EnterDate'=>$User['EnterDate']),15*60);
	#-------------------------------------------------------------------------------
	#Debug(SPrintF('[comp/Users/Init]: User[EnterIP] = %s',$User['EnterIP']));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $User;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
