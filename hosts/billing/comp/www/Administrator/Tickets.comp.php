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
$TicketID = @$Args['TicketID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Base')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Поддержка → Обращения');
#-------------------------------------------------------------------------------
// прячем заголовок окна
$DOM->AddAttribs('Body',Array('onload'=>"window.document.getElementById('Title').style.display = 'none';"));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($TicketID){
	#-------------------------------------------------------------------------------
	$Count = DB_Count('Edesks',Array('ID'=>$TicketID));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($Count)
		$DOM->AddAttribs('Body',Array('onload'=>SPrintF("ShowWindow('/TicketRead',{TicketID:%u})",$TicketID)));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Super','Tickets');
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$NoBody);
#-------------------------------------------------------------------------------
$Out = $DOM->Build();
#-------------------------------------------------------------------------------
if(Is_Error($Out))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------

?>
