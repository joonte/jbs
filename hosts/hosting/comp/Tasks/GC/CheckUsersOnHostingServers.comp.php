<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/Server.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
$ExcludeAccounts = $Config['Tasks']['Types']['GC']['ExcludeServerAccounts'];
#-------------------------------------------------------------------------------
$EAs = Array();
if(StrLen($ExcludeAccounts) < 1){
	$EAs[] = Md5(time());
}else{
	$ExcludeAccounts = Explode(',',$ExcludeAccounts);
	foreach ($ExcludeAccounts as &$value){
		$EAs[] = Trim($value);
	}
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$HostingServers = DB_Select('HostingServers',Array('ID','Address'));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingServers)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#---------------------------------------------------------------------------
	foreach($HostingServers as $HostingServer){
		#-------------------------------------------------------------------------
		$Server = new Server();
		#-------------------------------------------------------------------------
		$IsSelected = $Server->Select((integer)$HostingServer['ID']);
		#-------------------------------------------------------------------------
		switch(ValueOf($IsSelected)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'true':
			#---------------------------------------------------------------------
			$Users = $Server->GetUsers();
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
								SPrintF('`ServerID`=%u',$HostingServer['ID']),
								"`StatusID` = 'Active' OR `StatusID` = 'Suspended'"
							);
					$ServerUsers = DB_Select('HostingOrdersOwners',Array('UserID','Login'),Array('Where'=>$Where));
					switch(ValueOf($ServerUsers)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						# надо событие вешать. ахтунг какой-то - нет юзеров. а на сервере есть.
						# ненадо ничё вешать, так как сервер может быть новый, и кроме технических аккаунтов там ничего нет
						#$Event = Array(
						#		'UserID'        => 1,
						#		'PriorityID'    => 'Hosting',
						#		'Text'          => SPrintF('В биллинге, на сервере (%s) не обнаружено пользователей; на самом сервере обнаружено %u пользователей',$HostingServer['Address'],SizeOf($Users)),
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
										'PriorityID'    => 'Hosting',
										'Text'          => SPrintF('Пользователь (%s) не найден на сервере (%s)',$ServerUser['Login'],$HostingServer['Address']),
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
									'PriorityID'    => 'Hosting',
									'Text'          => SPrintF('На сервере (%s) найден пользователь (%s) отсутствующий в биллинге',$HostingServer['Address'],$UserID),
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
