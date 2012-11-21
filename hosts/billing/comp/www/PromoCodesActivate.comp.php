<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/WhoIs.php')))
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
$DOM->AddAttribs('MenuLeft',Array('args'=>'User/Services'));
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Активация ПромоКода');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'PromoCodesActivateForm','onsubmit'=>'return false;'));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'name'  => 'Code',
			'type'  => 'text'
			)
		);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------
$Table[] = Array('Введите ПромоКод',$Comp);
#-------------------------------------------------------------------------
$Div = new Tag('DIV',Array('align'=>'right'));
#-------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'type'    => 'button',
			'onclick' => "FormEdit('/API/PromoCodesActivate','PromoCodesActivateForm','Погашение промокода');",
			'value'   => 'Активировать'
			)
		);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------
$Div->AddChild($Comp);
#-------------------------------------------------------------------------
$Table[] = $Div;
#-------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table,Array('width'=>400));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------
$DOM->AddChild('Into',$Form);
#-------------------------------------------------------------------------------
$Out = $DOM->Build(FALSE);
#-------------------------------------------------------------------------------
if(Is_Error($Out))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------

?>
