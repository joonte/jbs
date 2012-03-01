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
$Ticket = DB_Select('Edesks',Array('ID','UserID','Flags'),Array('UNIQ','ID'=>$TicketID));
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
        if(Is_Error($DOM->Load('Standard')))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $DOM->Delete('Title');
        #-----------------------------------------------------------------------
        $IsQuery = DB_Query(SPrintF('SET @local.EdeskID = %u',$Ticket['ID']));
        if(Is_Error($IsQuery))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Tables/Super','TicketMessages');
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $DOM->AddChild('Into',$Comp);
        #-----------------------------------------------------------------------
        $Out = $DOM->Build();
        #-----------------------------------------------------------------------
        if(Is_Error($Out))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	if($__USER['ID'] == $Ticket['UserID']){
		if($Ticket['Flags'] == "CloseOnSee"){
        		$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Edesks','StatusID'=>'Closed','RowsIDs'=>$TicketID,'Comment'=>'Автоматическое закрытие после просмотра пользователем'));
			#---------------------------------------------------------------------
			switch(ValueOf($Comp)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				# No more...
				$IsUpdate = DB_Update('Edesks',Array('Flags'=>'No','StatusDate'=>time(),'StatusID'=>'Closed'),Array('ID'=>$TicketID));
				if(Is_Error($IsUpdate))
					return ERROR | @Trigger_Error(500);
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
		}
	}
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
        return $Out;
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
