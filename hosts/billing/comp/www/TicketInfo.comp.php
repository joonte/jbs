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
$TicketID = (integer) @$Args['TicketID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('*','(SELECT `Name` FROM `Users` WHERE `Users`.`ID` = `Edesks`.`TargetUserID`) as `TargetUser`','(SELECT `Name` FROM `Groups` WHERE `Groups`.`ID` = `Edesks`.`TargetGroupID`) as `TargetGroup`','(SELECT `Name` FROM `Users` WHERE `Users`.`ID` = `Edesks`.`LastSeenBy`) as `LastSeenByPersonal`');
#-------------------------------------------------------------------------------
$Ticket = DB_Select('Edesks',$Columns,Array('UNIQ','ID'=>$TicketID));
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
    $IsPermission = Permission_Check('TicketRead',(integer)$__USER['ID'],(integer)$Ticket['UserID']);
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
        $DOM = new DOM();
        #-----------------------------------------------------------------------
        $Links = &Links();
        # Коллекция ссылок
        $Links['DOM'] = &$DOM;
        #-----------------------------------------------------------------------
        if(Is_Error($DOM->Load('Window')))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $DOM->AddText('Title','Информация по запросу');
        #-----------------------------------------------------------------------
        $Table = Array('Общая информация');
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Edesk/Number',$Ticket['ID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Номер',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Date/Extended',$Ticket['CreateDate']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Дата обращения',$Comp);
        #-----------------------------------------------------------------------
        $Theme = Comp_Load('Formats/String',$Ticket['Theme'],25);
        if(Is_Error($Theme))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Тема',new Tag('TD',Array('class'=>'Standard'),$Theme));
        #-----------------------------------------------------------------------
        $Table[] = Array('Отдел',$Ticket['TargetGroup']);
        #-----------------------------------------------------------------------
        $Table[] = Array('Сотрудник',$Ticket['TargetUser']);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Date/Extended',$Ticket['UpdateDate']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Обновлен',$Comp);
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Date/Extended',$Ticket['SeenByUser']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	$Table[] = Array('Просмотрен пользователем',$Comp);
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Date/Extended',$Ticket['SeenByPersonal']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	$Table[] = Array('Просмотрен сотрудником',$Comp);
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	if(Is_Null($Ticket['LastSeenByPersonal'])){
		$Table[] = Array('Последний сотрудник',"-");
	}else{
		$Table[] = Array('Последний сотрудник',$Ticket['LastSeenByPersonal']);
	}
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	$Config = Config();
	$FlagsConfig = $Config['Edesks']['Flags'];
	#Debug("[comp/www/TicketInfo]: Flags = " . print_r($FlagsConfig,true));
	#-----------------------------------------------------------------------
	if($Ticket['Flags']){
		$Flags = $FlagsConfig[$Ticket['Flags']];
	}else{
		$Flags = "не установлен";
	}
	#-----------------------------------------------------------------------
	$Table[] = Array('Флаг тикета',$Flags);
	#-----------------------------------------------------------------------
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Statuses/State','Edesks',$Ticket);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table = Array_Merge($Table,$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Tables/Standard',$Table);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $DOM->AddChild('Into',$Comp);
        #-----------------------------------------------------------------------
        if(Is_Error($DOM->Build(FALSE)))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        return Array('Status'=>'Ok','DOM'=>$DOM->Object);
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
