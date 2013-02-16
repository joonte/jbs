<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#-------------------------------------------------------------------------------
if(!IsSet($Args)){
	if(Is_Error(System_Load('modules/Authorisation.mod')))
		return ERROR | @Trigger_Error(500);
	$Args = Args();
}
#-------------------------------------------------------------------------------
$TicketID = (integer) @$Args['TicketID'];
$Message  =  (string) @$Args['Message'];
//$IsClose= (boolean) @$Args['IsClose'];
$Flags    =  (string) @$Args['Flags'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Upload.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(IsSet($GLOBALS['__USER']['IsEmulate']))
	return new gException('DENY_WRITE_MESSAGE_FROM_ANOTHER_USER','Нельзя писать сообщения от имени другого пользователя');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# truncate $Theme & $Message
$Message	= substr($Message, 0, 62000);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Flags == "Closed" || $Flags == "DenyAddMessage"){$IsClose = TRUE;}else{$IsClose = FALSE;}
if(StrLen($Flags) < 2){$Flags = "No";}
Debug("[comp/www/API/TicketMessageEdit]: Flags = '" . $Flags . "'");
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Ticket = DB_Select('Edesks',Array('ID','UserID','TargetUserID','Theme','StatusID','Flags'),Array('UNIQ','ID'=>$TicketID));
#-------------------------------------------------------------------------------
switch(ValueOf($Ticket)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('TicketEdit',(integer)$__USER['ID'],(integer)$Ticket['UserID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsPermission)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'false':
        return ERROR | @Trigger_Error(700);
      case 'true':
        #-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
        if($Flags == "No" && !$Message)
          return new gException('MESSAGE_IS_EMPTY','Введите сообщение');
        #-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	if($Ticket['UserID'] == $__USER['ID'] && $Ticket['Flags'] == "DenyAddMessage")
	  return new gException('DENY_ADD_MESSAGE','Тема содержит очень большое количество сообщений. У сотрудников технической поддержки возникают затруднения с перечитыванием истории переписки. Пожалуйста, опишите вашу проблему и создайте новый запрос.');
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
        if($Message){
          #---------------------------------------------------------------------
          $ITicketMessage = Array(
            #-------------------------------------------------------------------
            'UserID'  => $__USER['ID'],
            'EdeskID' => $Ticket['ID'],
            'Content' => $Message
          );
          #---------------------------------------------------------------------
	  $Upload = Upload_Get('TicketMessageFile',(IsSet($Args['TicketMessageFile'])?$Args['TicketMessageFile']:FALSE));
          #---------------------------------------------------------------------
          switch(ValueOf($Upload)){
            case 'error':
              return ERROR | @Trigger_Error(500);
            case 'exception':
              # No more...
            break;
            case 'array':
              #-----------------------------------------------------------------
              $ITicketMessage['FileName'] = $Upload['Name'];
            break;
            default:
              return ERROR | @Trigger_Error(101);
          }
          #---------------------------------------------------------------------
          $MessageID = DB_Insert('EdesksMessages',$ITicketMessage);
          if(Is_Error($MessageID))
            return ERROR | @Trigger_Error(500);
	  #---------------------------------------------------------------------
	  if(IsSet($ITicketMessage['FileName']))
	    if(!SaveUploadedFile('EdesksMessages', $MessageID, $Upload['Data']))
	      return new gException('CANNOT_SAVE_UPLOADED_FILE','Не удалось сохранить загруженный файл');
          #---------------------------------------------------------------------
          if ($__USER['ID'] != (integer)$Ticket['UserID']) {
              $Count = DB_Count('EdesksMessages',Array('Where'=>SPrintF('`EdeskID` = %d AND `UserID` != %u', $Ticket['ID'], (integer)$Ticket['UserID'])));
              if(Is_Error($Count))
                return ERROR | Trigger_Error(500);
              #-----------------------------------------------------------------
              // First message from support and ticket hasn't assigned to somebody yet.
              if ($Count == 1 && $Ticket['TargetUserID'] == 100) {
                  #-------------------------------------------------------------
                  $IsUpdate = DB_Update('Edesks',
		  	Array(	'UpdateDate'	=> Time(),
                      		'TargetUserID'	=> (integer)$__USER['ID']),
                      Array('ID'=>$Ticket['ID']));

                  if(Is_Error($IsUpdate))
                    return ERROR | @Trigger_Error(500);
              }
          }
          #---------------------------------------------------------------------
	  if ($__USER['ID'] != (integer)$Ticket['UserID']) {
	  	$u_array = Array('UpdateDate'=>Time(),'Flags'=>$Flags);
	  }else{
	  	$u_array = Array('UpdateDate'=>Time());
	  }
          $IsUpdate = DB_Update('Edesks',$u_array,Array('ID'=>$Ticket['ID']));
          if(Is_Error($IsUpdate))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
	  $Event = Array(
	  			'UserID'	=> $__USER['ID'],
				'PriorityID'	=> 'Billing',
				'Text'		=> SPrintF('Добавлено новое сообщение к запросу в службу поддержки с темой (%s)',$Ticket['Theme'])
	                );
	  $Event = Comp_Load('Events/EventInsert',$Event);
          if(!$Event)
            return ERROR | @Trigger_Error(500);
	  #---------------------------------------------------------------------
        }else{
		// no message, but, may be need change flag?
		if ($__USER['ID'] != (integer)$Ticket['UserID']) {
			$u_array = Array('UpdateDate'=>Time(),'Flags'=>$Flags);
		}else{
			$u_array = Array('UpdateDate'=>Time());
		}
		$IsUpdate = DB_Update('Edesks',$u_array,Array('ID'=>$Ticket['ID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
	}
        #-----------------------------------------------------------------------
        $StatusID = $Ticket['StatusID'];
        #-----------------------------------------------------------------------
        $StatusID = ($IsClose?'Closed':($Ticket['UserID'] != $__USER['ID']?'Opened':($StatusID != 'Newest'?'Working':'Newest')));
        #-----------------------------------------------------------------------
        if($StatusID != $Ticket['StatusID']){
          #---------------------------------------------------------------------
          $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Edesks','StatusID'=>$StatusID,'IsNotNotify'=>TRUE,'RowsIDs'=>$Ticket['ID']));
          #---------------------------------------------------------------------
          switch(ValueOf($Comp)){
            case 'error':
              return ERROR | @Trigger_Error(500);
            case 'exception':
              #return ERROR | @Trigger_Error(400);
	      return $Comp;
            case 'array':
              # No more...
            break;
            default:
              return ERROR | @Trigger_Error(101);
          }
        }
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
