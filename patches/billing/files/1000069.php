<?php
#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/

#-------------------------------------------------------------------------------
# перебираем всех юзеров, переносим их контакты в таблицу контактов
$Users = DB_Select('Users','*');
#-------------------------------------------------------------------------------
switch(ValueOf($Users)){
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
foreach($Users as $User){
	#-------------------------------------------------------------------------------
	# данные для вставки, почта
	$Array = Array(
				'CreateDate'	=> ($User['EmailConfirmed'])?$User['EmailConfirmed']:$User['RegisterDate'],
				'UserID'	=> $User['ID'],
				'MethodID'	=> 'Email',
				'Address'	=> $User['Email'],
				'Confirmed'	=> $User['EmailConfirmed'],
				'IsPrimary'	=> TRUE,
				'IsActive'	=> TRUE
			);
	#-------------------------------------------------------------------------------
	$IsInsert = DB_Insert('Contacts',$Array);
	if(Is_Error($IsInsert))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# SMS
	if($User['Params']['NotificationMethods']['SMS']['Address']){
		#-------------------------------------------------------------------------------
		$TimeBegin = Explode('_',$User['Params']['Settings']['SMSBeginTime']);
		$TimeEnd = Explode('_',$User['Params']['Settings']['SMSEndTime']);
		#-------------------------------------------------------------------------------
		$Array = Array(
				'CreateDate'	=> ($User['Params']['NotificationMethods']['SMS']['Confirmed'])?$User['Params']['NotificationMethods']['SMS']['Confirmed']:$User['RegisterDate'],
				'UserID'	=> $User['ID'],
				'MethodID'	=> 'SMS',
				'Address'	=> $User['Params']['NotificationMethods']['SMS']['Address'],
				'Confirmed'	=> $User['Params']['NotificationMethods']['SMS']['Confirmed'],
				'IsPrimary'	=> FALSE,
				'IsActive'	=> ($User['Params']['NotificationMethods']['SMS']['Confirmed'])?TRUE:FALSE,
				'TimeBegin'	=> $TimeBegin[1],
				'TimeEnd'	=> $TimeEnd[1],
			);
		#-------------------------------------------------------------------------------
		$IsInsert = DB_Insert('Contacts',$Array);
		if(Is_Error($IsInsert))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# Jabber
	if($User['Params']['NotificationMethods']['Jabber']['Address']){
		#-------------------------------------------------------------------------------
		$Array = Array(
				'CreateDate'	=> ($User['Params']['NotificationMethods']['Jabber']['Confirmed'])?$User['Params']['NotificationMethods']['Jabber']['Confirmed']:$User['RegisterDate'],
				'UserID'	=> $User['ID'],
				'MethodID'	=> 'Jabber',
				'Address'	=> $User['Params']['NotificationMethods']['Jabber']['Address'],
				'Confirmed'	=> $User['Params']['NotificationMethods']['Jabber']['Confirmed'],
				'IsPrimary'	=> FALSE,
				'IsActive'	=> ($User['Params']['NotificationMethods']['Jabber']['Confirmed'])?TRUE:FALSE
			);
		#-------------------------------------------------------------------------------
		$IsInsert = DB_Insert('Contacts',$Array);
		if(Is_Error($IsInsert))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# ICQ
	if($User['Params']['NotificationMethods']['ICQ']['Address']){
		#-------------------------------------------------------------------------------
		$Array = Array(
				'CreateDate'	=> ($User['Params']['NotificationMethods']['ICQ']['Confirmed'])?$User['Params']['NotificationMethods']['ICQ']['Confirmed']:$User['RegisterDate'],
				'UserID'	=> $User['ID'],
				'MethodID'	=> 'ICQ',
				'Address'	=> $User['Params']['NotificationMethods']['ICQ']['Address'],
				'Confirmed'	=> $User['Params']['NotificationMethods']['ICQ']['Confirmed'],
				'IsPrimary'	=> FALSE,
				'IsActive'	=> ($User['Params']['NotificationMethods']['ICQ']['Confirmed'])?TRUE:FALSE
			);
		#-------------------------------------------------------------------------------
		$IsInsert = DB_Insert('Contacts',$Array);
		if(Is_Error($IsInsert))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# WhatsApp
	if($User['Params']['NotificationMethods']['WhatsApp']['Address']){
		#-------------------------------------------------------------------------------
		$Array = Array(
				'CreateDate'	=> ($User['Params']['NotificationMethods']['WhatsApp']['Confirmed'])?$User['Params']['NotificationMethods']['WhatsApp']['Confirmed']:$User['RegisterDate'],
				'UserID'	=> $User['ID'],
				'MethodID'	=> 'WhatsApp',
				'Address'	=> $User['Params']['NotificationMethods']['WhatsApp']['Address'],
				'Confirmed'	=> $User['Params']['NotificationMethods']['WhatsApp']['Confirmed'],
				'IsPrimary'	=> FALSE,
				'IsActive'	=> ($User['Params']['NotificationMethods']['WhatsApp']['Confirmed'])?TRUE:FALSE
			);
		#-------------------------------------------------------------------------------
		$IsInsert = DB_Insert('Contacts',$Array);
		if(Is_Error($IsInsert))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# правим массив параметров юзера и вписываем обратно
	UnSet($User['Params']['NotificationMethods']);
	UnSet($User['Params']['Settings']['SMSBeginTime']);
	UnSet($User['Params']['Settings']['SMSEndTime']);
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('Users',Array('Params'=>$User['Params']),Array('ID'=>$User['ID']));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Result = DB_Query('ALTER TABLE `Users` DROP `EmailConfirmed`');
if(Is_Error($Result))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
