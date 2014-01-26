<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/VPSServer.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['GC']['CheckUsersOnVPSServersSettings'];
#-------------------------------------------------------------------------------
if(!$Settings['IsActive'])
	return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$EAs = Array();
#-------------------------------------------------------------------------------
if(StrLen($Settings['ExcludeServerAccounts']) < 1){
	#-------------------------------------------------------------------------------
	$EAs[] = Md5(time());
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$ExcludeAccounts = Explode(',',$Settings['ExcludeServerAccounts']);
	#-------------------------------------------------------------------------------
	foreach ($ExcludeAccounts as &$value)
		$EAs[] = Trim($value);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$VPSServers = DB_Select('VPSServers',Array('ID','Address'));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSServers)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#---------------------------------------------------------------------------
	foreach($VPSServers as $IVPSServer){
		#-------------------------------------------------------------------------
		$VPSServer = new VPSServer();
		#-------------------------------------------------------------------------
		$IsSelected = $VPSServer->Select((integer)$IVPSServer['ID']);
		#-------------------------------------------------------------------------
		switch(ValueOf($IsSelected)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'true':
			#---------------------------------------------------------------------
			$Users = $VPSServer->GetUsers();
			#---------------------------------------------------------------------
			switch(ValueOf($Users)){
			case 'error':
				# No more...
				break;
			case 'exception':
				# No more...
				break;
			case 'array':
				#-----------------------------------------------------------------
				if(Count($Users)){
					#-----------------------------------------------------------------
					$SUsers = Array();
					#-----------------------------------------------------------------
					$Where = Array(
								SPrintF('`ServerID`=%u',$IVPSServer['ID']),
								"`StatusID` = 'Active' OR `StatusID` = 'Suspended'"
							);
					$ServerUsers = DB_Select('VPSOrdersOwners',Array('UserID','Login'),Array('Where'=>$Where));
					switch(ValueOf($ServerUsers)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						# надо событие вешать. ахтунг какой-то - нет юзеров. а на сервере есть.
						# ненадо ничё вешать, так как сервер может быть новый, и кроме технических аккаунтов там ничего нет
						#$Event = Array(
						#		'UserID'        => 1,
						#		'PriorityID'    => 'VPS',
						#		'Text'          => SPrintF('В биллинге, на сервере (%s) не обнаружено пользователей; на самом сервере обнаружено %u пользователей',$IVPSServer['Address'],SizeOf($Users)),
						#		'IsReaded'      => FALSE
						#		);
						#$Event = Comp_Load('Events/EventInsert',$Event);
						#if(!$Event)
						#	return ERROR | @Trigger_Error(500);
						break;
					case 'array':
						foreach($ServerUsers as $ServerUser){
							# тут проверяем наличие аккаунта биллинга на сервере
							if(!In_Array($ServerUser['Login'], $Users)){
								$Event = Array(
										'UserID'        => $ServerUser['UserID'],
										'PriorityID'    => 'VPS',
										'Text'          => SPrintF('Пользователь (%s) не найден на сервере (%s)',$ServerUser['Login'],$IVPSServer['Address']),
										'IsReaded'      => FALSE
										);
								$Event = Comp_Load('Events/EventInsert',$Event);
								if(!$Event)
									return ERROR | @Trigger_Error(500);
							}
							# собираем массив для обратной проверки - наличие аккаунтов сервера в биллинге
							$SUsers[] = $ServerUser['Login'];
						}
						break;
					default:
						return ERROR | @Trigger_Error(101);
					}
					#-----------------------------------------------------------------
					# тут проверяем наличие аккаунтов сервера в биллинге
					foreach($Users as $UserID){
						# исключаем юзеров из списка исключаемых
						if(!In_Array($UserID, $SUsers)){
							# проверяем лишнего по массиву исключений
							foreach($EAs as $EA){
								if(Preg_Match(SPrintF("/%s/A",$EA),$UserID)){
									# совпало с исключениями. пропускаем.
									continue 2;
								}
							}
							#-----------------------------------------------------------------
							$Event = Array(
									'UserID'        => 1,
									'PriorityID'    => 'VPS',
									'Text'          => SPrintF('На сервере (%s) найден пользователь (%s) отсутствующий в биллинге',$IVPSServer['Address'],$UserID),
									'IsReaded'      => FALSE
									);
							$Event = Comp_Load('Events/EventInsert',$Event);
							if(!$Event)
								return ERROR | @Trigger_Error(500);
							#-----------------------------------------------------------------
						}
					}
				}
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
	}
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------

?>
