<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('LinkID');
/******************************************************************************/
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Links = &Links();
# Коллекция ссылок
$DOM = &$Links[$LinkID];
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Css',Array('Standard','TableSuper'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
foreach($Comp as $Css)
	$DOM->AddChild('Head',$Css);
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
/*
$Comp = Comp_Load('Css',Array(BROWSER_ID));
if(!Is_Error($Comp)){
	#-------------------------------------------------------------------------------
	foreach($Comp as $Css)
		$DOM->AddChild('Head',$Css);
	#-------------------------------------------------------------------------------
}
*/
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Standard.js}')));
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/DOM.js}')));
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/HTTP.js}')));
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/CheckBox.js}')));
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/FormEdit.js}')));
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/CtrlEnterEvent.js}')));
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Ajax/Window.js}')));
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Ajax/AutoComplite.js}')));
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{others/jQuery/core.js}')));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',new Tag('LINK',Array('rel'=>'stylesheet','type'=>'text/css','href'=>'SRC:{others/jQuery/smoothness/jquery-ui-custom.css}')));
#-------------------------------------------------------------------------------
$DOM->AddHTML('Floating',TemplateReplace('Triggers.DOM.Before.Base.TABLE'));
#-------------------------------------------------------------------------------
$DOM->AddHTML('Floating',TemplateReplace('Triggers.DOM.Before.Base.DIV'));
#-------------------------------------------------------------------------------
if(IsSet($_COOKIE['Eval'])){
	#-------------------------------------------------------------------------------
	$Eval = $_COOKIE['Eval'];
	#-------------------------------------------------------------------------------
	$DOM->AddAttribs('Body',Array('onload'=>$Eval));
	#-------------------------------------------------------------------------------
	if(!SetCookie('Eval',$Eval,Time() - 86400,'/'))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
