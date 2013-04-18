<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
# cache and other functions added 2011-12-30 in 19:41, by lissyara, as part of JBS-237
if(!IsSet($GLOBALS['__USER'])){
	#Debug("[comp/www/API/Events]: юзер не авторизован");
	return ERROR | @Trigger_Error(700);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$OutCacheID = Md5($__FILE__ . $GLOBALS['__USER']['ID']);
$TimeCacheID = Md5($__FILE__ . $GLOBALS['__USER']['ID'] . 'time');
$LastIDCacheID = Md5($__FILE__ . $GLOBALS['__USER']['ID'] . 'ID');
#-------------------------------------------------------------------------------
$TimeResult = CacheManager::get($TimeCacheID);
if($TimeResult){
	# проверяем не истекло ли время кэша
	if($TimeResult > Time() - 10){
		# проверяем, есть ли выхлоп в кэше
		$Out = CacheManager::get($OutCacheID);
		if($Out){
			# отдаём кэш
			Debug("[comp/www/API/Events]: UserID: " . $GLOBALS['__USER']['ID'] . ", результат найден в кэше");
			Return($Out);
		}
	}
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Tree.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#---------------------------------------------------------------------------
$Where = Array('UNIX_TIMESTAMP() - 10 <= `CreateDate`');
#---------------------------------------------------------------------------
$LastID = CacheManager::get($LastIDCacheID);
if($LastID){
  Debug("[comp/www/API/Events]: last selected ID, from cache = " . $LastID . "; user = " . $GLOBALS['__USER']['ID']);
  $Where[] = SPrintF('`ID` > %u',$LastID);
}
#---------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#---------------------------------------------------------------------------
if(!$__USER['IsAdmin'])
	$Where[] = SPrintF('`UserID` = %u',$__USER['ID']);
#---------------------------------------------------------------------------
$UserInfo = "(SELECT CONCAT(FROM_UNIXTIME(`CreateDate`,'%Y-%m-%d / %H:%i:%s / '),`Email`,' / ',`Name`) FROM `Users` WHERE `Users`.`ID` = `Events`.`UserID`)";
#---------------------------------------------------------------------------
$Events = DB_Select('Events',Array('ID','Text',SPrintF('%s AS `UserInfo`',$UserInfo),'PriorityID'),Array('SortOn'=>'ID','Where'=>$Where));
#---------------------------------------------------------------------------
switch(ValueOf($Events)){
case 'error':
  return ERROR | @Trigger_Error(500);
case 'exception':
  $Out = Array('Status'=>'Empty');
  break;
case 'array':
  #-----------------------------------------------------------------------
  $Result = Array();
  #-----------------------------------------------------------------------
  foreach($Events as $Event){
    $Event['Text'] = HtmlSpecialChars($Event['Text']);
    $Result[] = $Event;
    $LastID = $Event['ID'];
    #Debug("[comp/www/API/Events]: last selected ID = " . $LastID);
  }
  #-----------------------------------------------------------------------
  CacheManager::add($LastIDCacheID,$LastID,24 * 3600); /* на сутки в кэш */
  #-----------------------------------------------------------------------
  $Out = Array('Status'=>'Ok','Events'=>$Result);
  break;
default:
  return ERROR | @Trigger_Error(101);
}
#---------------------------------------------------------------------------
#---------------------------------------------------------------------------
CacheManager::add($TimeCacheID,Time(),10);
CacheManager::add($OutCacheID,$Out,10);
#Debug("[comp/www/API/Events]: результат добавлен в кэш");
#---------------------------------------------------------------------------
#---------------------------------------------------------------------------
return $Out;
#---------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
