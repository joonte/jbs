<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$ServiceOrderID	= (integer) @$Args['ServiceOrderID'];
$ServiceID	= (integer) @$Args['ServiceID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# достаём сервис
$Service = DB_Select('ServicesOwners',Array('ID','Code','NameShort','Name'),Array('UNIQ','ID'=>$ServiceID));
#-------------------------------------------------------------------------------
switch(ValueOf($Service)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SERVICE_NOT_FOUND','Указанный сервис не найден');
case 'array':
	# No more...
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Order = DB_Select(SPrintF('%sOrdersOwners',$Service['Code']),Array('*'),Array('UNIQ','ID'=>$ServiceOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($Order)){
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
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check(SPrintF('%sOrdersConsider',$Service['Code']),(integer)$GLOBALS['__USER']['ID'],(integer)$Order['UserID']);
#-------------------------------------------------------------------------------
switch(ValueOf($IsPermission)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'false':
	return new gException('NO_PERMISSION','У вас отсутствуют права на изменение настроек автопродления');
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Window')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddText('Title',SPrintF('Настройки автопродления, услуга "%s"',$Service['Name']));
#-------------------------------------------------------------------------------
$Table = Array(SPrintF('Настройки автопродления, услуга "%s", заказ #%u',$Service['NameShort'],$Order['OrderID']));
#-------------------------------------------------------------------------------
if($Order['IsAutoProlong']){
	$Button = "Отключить";
	$msg = "[включено]";
}else{
	$Button = "Включить";
	$msg = "[выключено]";
}
#-----------------------------------------------------------------------
$Params = Array('type'=>'hidden','name'=>'IsAutoProlong','value'=>$Order['IsAutoProlong']?'0':'1');
$IsAutoProlong = Comp_Load('Form/Input',$Params);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-----------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'type'    => 'button',
			'onclick' => "AjaxCall('/API/ServiceAutoProlongation',FormGet(form),'Сохрание настроек','GetURL(document.location);');",
			'value'   => $Button
			)
		);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-----------------------------------------------------------------------
$Table[] = Array('Автопродление ' . $msg, $Comp);




#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('method'=>'POST','name'=>'OrderConsiderInfoForm'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'type'  => 'hidden',
			'name'  => 'OrderID',
			'value' => $Order['OrderID']
			)
		);
#-------------------------------------------------------------------------------
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Form);
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Build(FALSE)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
