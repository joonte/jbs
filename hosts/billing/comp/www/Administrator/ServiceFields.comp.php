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
$ServiceID = (integer) @$Args['ServiceID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Standard')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/ServiceFields.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$DOM->Delete('Title');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Buttons/Standard',Array('onclick'=>SPrintF("GetURL('/Administrator/ServiceFieldEdit?ServiceID=%s');",$ServiceID)),'Добавить новое поле','Add.gif');
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Buttons/Panel',Array('Comp'=>$Comp,'Name'=>'Добавить новое поле'));
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Comp);
#-------------------------------------------------------------------------------
$Template = Array('Source'=>Array('Conditions'=>Array('Where'=>Array(UniqID()=>SPrintF('`ServiceID` = %u',$ServiceID)))));
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Super','ServicesFields',$Template);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Comp);
#-------------------------------------------------------------------------------
$Out = $DOM->Build();
#-------------------------------------------------------------------------------
if(Is_Error($Out))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------

?>
