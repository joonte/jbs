<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/Tree.php','libs/Upload.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['TicketsMessages'];
#-------------------------------------------------------------------------------
$ExecuteTime = Comp_Load('Formats/Task/ExecuteTime',Array('ExecutePeriod'=>$Settings['ExecutePeriod']));
if(Is_Error($ExecuteTime))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Settings['IsActive'])
	return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$MessagesCount = 0;
#-------------------------------------------------------------------------------
$Columns = Array(
		'ID','UserID','EdeskID','FileName','SUBSTR(`Content`,1,4096) AS `Content`',
		SPrintF('CONCAT("[%s-",`EdeskID`,"] ",(SELECT `Theme` FROM `Edesks` WHERE `Edesks`.`ID` = `EdeskID`)) as `Theme`',$Settings['KeyPrefix']),
		'(SELECT `TargetGroupID` FROM `Edesks` WHERE `Edesks`.`ID` = `EdeskID`) as `TargetGroupID`',
		'(SELECT `TargetUserID` FROM `Edesks` WHERE `Edesks`.`ID` = `EdeskID`) as `TargetUserID`',
		'(SELECT `UserID` FROM `Edesks` WHERE `Edesks`.`ID` = `EdeskID`) as `OwnerID`',
		'(SELECT `StatusID` FROM `Edesks` WHERE `Edesks`.`ID` = `EdeskID`) as `StatusID`',
		'(SELECT `NotifyEmail` FROM `Edesks` WHERE `Edesks`.`ID` = `EdeskID`) as `NotifyEmail`'
		);
#-------------------------------------------------------------------------------
$Where = Array(
		"`IsNotify` = 'no'",
		"`IsVisible` = 'yes'"
		);
#-------------------------------------------------------------------------------
$Messages = DB_Select('EdesksMessages',$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($Messages)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	return $ExecuteTime;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($Messages as $Message){
	#-------------------------------------------------------------------------------
	$TargetUserID = (integer)$Message['TargetUserID'];
	$TargetGroupID = (integer)$Message['TargetGroupID'];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// если файл существует, собираем массив вложений
	if(GetUploadedFileSize('EdesksMessages',$Message['ID'])){
		#-------------------------------------------------------------------------------
		// достаём сам файл
		$File = GetUploadedFile('EdesksMessages',$Message['ID']);
		#-------------------------------------------------------------------------------
		$Attachments = Array(
					Array(
						'Name'	=> $Message['FileName'],
						'Size'	=> GetUploadedFileSize('EdesksMessages',$Message['ID']),
						'Mime'	=> GetFileMimeType('EdesksMessages',$Message['ID']),
						'Data'	=> Chunk_Split(Base64_Encode($File['Data']))
						)
					);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if($TargetGroupID != 1){
		#-------------------------------------------------------------------------------
		$IsOwner = ($Message['UserID'] == ($OwnerID = $Message['OwnerID']));
		#-------------------------------------------------------------------------------
		if($IsOwner){
			#-------------------------------------------------------------------------------
			if($TargetUserID != 100){
				#-------------------------------------------------------------------------------
				$msgParams = Array(
							'TicketID'		=> $Message['EdeskID'],
							'Theme'			=> $Message['Theme'],
							'Message'		=> $Message['Content'],
							'MessageID'		=> $Message['ID'],
							'Attachments'		=> (IsSet($Attachments)?$Attachments:Array())
							);
				#-------------------------------------------------------------------------------
				$msg = new Message('ToTicketsMessages', $TargetUserID, $msgParams);
				$IsSend = NotificationManager::sendMsg($msg);
				#-------------------------------------------------------------------------------
				switch(ValueOf($IsSend)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					# No more...
				case 'true':
					#-------------------------------------------------------------------------------
					$MessagesCount++;
					# Update -> `IsNotify`='yes'
					$IsUpdate = DB_Update('EdesksMessages',Array('IsNotify'=>'yes'),Array('ID'=>$Message['ID']));
					if(Is_Error($IsUpdate))
						return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
					break;
					#-------------------------------------------------------------------------------
				default:
					return ERROR | @Trigger_Error(101);
				}
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				$Entrance = Tree_Entrance('Groups',$TargetGroupID);
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
				$String = Implode(',',$Entrance);
				#-------------------------------------------------------------------------------
				$Employers = DB_Select('Users','ID',Array('Where'=>SPrintF('`GroupID` IN (%s)',$String)));
				#-------------------------------------------------------------------------------
				switch(ValueOf($Employers)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					# No more...
					continue 2;
				case 'array':
					break;
				default:
					return ERROR | @Trigger_Error(101);
				}
				#-------------------------------------------------------------------------------
				foreach($Employers as $Employer){
					#-------------------------------------------------------------------------------
					$msgParams = Array(
								'TicketID'		=> $Message['EdeskID'],
								'Theme'			=> $Message['Theme'],
								'Message'		=> $Message['Content'],
								'MessageID'		=> $Message['ID'],
								'Attachments'		=> (IsSet($Attachments)?$Attachments:Array())
								);
					#-------------------------------------------------------------------------------
					$msg = new Message('ToTicketsMessages',(integer)$Employer['ID'], $msgParams);
					$IsSend = NotificationManager::sendMsg($msg);
					#-------------------------------------------------------------------------------
					switch(ValueOf($IsSend)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						# No more...
					case 'true':
						#-------------------------------------------------------------------------------
						$MessagesCount++;
						# Update -> `IsNotify`='yes'
						$IsUpdate = DB_Update('EdesksMessages',Array('IsNotify'=>'yes'),Array('ID'=>$Message['ID']));
						if(Is_Error($IsUpdate))
							return ERROR | @Trigger_Error(500);
						#-------------------------------------------------------------------------------
						break;
						#-------------------------------------------------------------------------------
					default:
						return ERROR | @Trigger_Error(101);
					}
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$String = $Message['Content'];
			#-------------------------------------------------------------------------------
			switch($Message['StatusID']){
			case 'Closed':
				#-------------------------------------------------------------------------------
				$String = SPrintF("%s\n\nЕсли потребуется какая-либо помощь, пожалуйста, откройте новый запрос.\nСпасибо.",$String);
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			case 'Working':
				#-------------------------------------------------------------------------------
				$String = SPrintF("%s\n\nНа данный момент мы решаем Ваш вопрос.\nСпасибо.",$String);
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			default:
				# No more...
			}
			#-------------------------------------------------------------------------------
			$Message['Content'] = $String;
			#-------------------------------------------------------------------------------
			$msgParams = Array(
						'TicketID'		=> $Message['EdeskID'],
						'Theme'			=> $Message['Theme'],
						'Message'		=> $Message['Content'],
						'MessageID'		=> $Message['ID'],
						'Attachments'		=> (IsSet($Attachments)?$Attachments:Array())
						);
			#Debug(SPrintF('[comp/Tasks/TicketsMessages]: msgParams = %s',print_r($msgParams,true)));
			#-------------------------------------------------------------------------------
			if(StrLen($Message['NotifyEmail']) > 5)
				$msgParams['Recipient'] = $Message['NotifyEmail'];
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$msg = new FromTicketsMessagesMsg($msgParams, (integer)$OwnerID);
			$IsSend = NotificationManager::sendMsg($msg);
			#-------------------------------------------------------------------------------
			switch(ValueOf($IsSend)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				# No more...
			case 'true':
				#-------------------------------------------------------------------------------
				$MessagesCount++;
				# Update -> `IsNotify`='yes'
				$IsUpdate = DB_Update('EdesksMessages',Array('IsNotify'=>'yes'),Array('ID'=>$Message['ID']));
				if(Is_Error($IsUpdate))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		# No more...
		continue;
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($MessagesCount > 0 && !IsSet($GLOBALS['TaskReturnInfo']))
	$GLOBALS['TaskReturnInfo'] = SPrintF('%u new messages',$MessagesCount);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
