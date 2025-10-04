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
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{others/jQuery/autosize.js}')));
#-------------------------------------------------------------------------------
//$DOM->AddChild('Head',new Tag('LINK',Array('rel'=>'stylesheet','type'=>'text/css','href'=>'SRC:{others/jQuery/smoothness/jquery-ui-custom.css}')));
$DOM->AddChild('Head',new Tag('LINK',Array('rel'=>'stylesheet','type'=>'text/css','href'=>'SRC:{others/jQuery/jquery-ui.css}')));
#-------------------------------------------------------------------------------
// мобильный стиль. настрйоку про max-width:900px возможно стоит в конфиг вынести
$DOM->AddChild('Head',new Tag('LINK',Array('rel'=>'stylesheet','href'=>'SRC:{Css/Mobile.css}','media'=>'only screen and (max-width:600px)')));
#-------------------------------------------------------------------------------
// для мобильных устройств, чтоб не умничали и присылали реально разрешение браузера
//$DOM->AddChild('Head',new Tag('META',Array('name'=>'viewport','content'=>'width=device-width, initial-scale=1, shrink-to-fit=no')));
$DOM->AddChild('Head',new Tag('META',Array('name'=>'viewport','content'=>'width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, shrink-to-fit=no')));
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
	if(!SetCookie('Eval',$Eval,Time() - 86400,'/',SPrintF('.%s',HOST_ID)))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
