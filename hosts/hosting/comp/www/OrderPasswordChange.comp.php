<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$ServiceOrderID	= (integer) @$Args['ServiceOrderID'];
$ServiceID	= (integer) @$Args['ServiceID'];
$ServerID	= (integer) @$Args['ServerID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ServerID)
	return new gException('SERVER_ADMIN_PASSWORD_CHANGE_NOT_IMPLEMENTED','Смена пароля администратора сервера не реализована');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Service = DB_Select('ServicesOwners',Array('*'),Array('UNIQ','ID'=>$ServiceID));
#-------------------------------------------------------------------------------
switch(ValueOf($Service)){
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
#-------------------------------------------------------------------------------
$Order = DB_Select(SPrintF('%sOrdersOwners',$Service['Code']),Array('ID','UserID','StatusID','(SELECT `Params` FROM `Servers` WHERE `Servers`.`ID` = `ServerID`) AS `Params`'),Array('UNIQ','ID'=>$ServiceOrderID));
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
if($Order['StatusID'] != 'Active')
	return new gException('HOSTING_ORDER_NOT_ACTIVE','Заказ не активен');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check(SPrintF('%sManage',$Service['Code']),(integer)$__USER['ID'],(integer)$Order['UserID']);
#-------------------------------------------------------------------------------
switch(ValueOf($IsPermission)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'false':
	return ERROR | @Trigger_Error(700);
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
#-------------------------------------------------------------------------------
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Window')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddText('Title',SPrintF('Смена пароля для услуги "%s"',$Service['NameShort']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach(Array('PasswordCheck','Pages/OrderPasswordChange','OrderManage') as $Js){
	#-------------------------------------------------------------------------------
	$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>SPrintF('SRC:{Js/%s.js}',$Js)));
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Head',$Script);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# реализация JBS-913
if($Order['Params']['SystemID'] == 'VmManager5_KVM'){
	#-------------------------------------------------------------------------------
	$Rows = Array();
	#-------------------------------------------------------------------------------
	$NoBody = new Tag('NOBODY',new Tag('SPAN','Обращаем ваше внимание, что при смене пароля:'),new Tag('BR'),new Tag('SPAN','1. Виртуальный сервер будет перезагружен'),new Tag('BR'),new Tag('SPAN','2. Если есть техническая возможность, будет изменён пароль пользователя "root"'),new Tag('BR'),new Tag('BR'), new Tag('SPAN','В любом случае, будет изменён пароль для входа на сервер виртуализации'));
	#-------------------------------------------------------------------------------
	$Rows[] = Array(new Tag('TD',Array('colspan'=>2,'class'=>'Standard','style'=>'border:1px solid #F07D00;'),$NoBody));
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Tables/Extended',$Rows);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = $Comp;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Password = Comp_Load('Passwords/Generator');
if(Is_Error($Password))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'IsPasswordCreate','value'=>$Password,'type'=>'checkbox','onclick'=>'PasswordMode();'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY',new Tag('DIV',Array('style'=>'margin-bottom:5px;'),$Comp,new Tag('SPAN',Array('style'=>'font-size:10px; cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsPasswordCreate\'); PasswordMode(); return false;'),'Вставить из примера')));
#-------------------------------------------------------------------------------
$Messages = Messages();
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'Password','prompt'=>$Messages['Prompts']['User']['Password'],'type'=>'password'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$NoBody->AddChild($Comp);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Новый пароль'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),new Tag('SPAN',SPrintF('Например: %s',$Password)))),$NoBody);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'_Password','prompt'=>$Messages['Prompts']['User']['Password'],'type'=>'password'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('NOBODY',new Tag('SPAN','Подтверждение пароля'),new Tag('BR'),new Tag('SPAN',Array('class'=>'Comment'),'Аналогично полю [Новый пароль]')),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp1 = Comp_Load('Form/Input',Array('type'=>'button','prompt'=>'Сменить пароль от панели управления заказом','onclick'=>"if(PasswordCheck(this.form,'Password')){PasswordChange();}; var Button = document.getElementById('OrderManageButton'); Button.style.cursor = 'pointer'; Button.disabled = false;",'value'=>'Сменить пароль'));
if(Is_Error($Comp1))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp2 = Comp_Load('Form/Input',Array('type'=>'button','id'=>'OrderManageButton','disabled'=>'yes','style'=>'cursor: not-allowed;','prompt'=>'Перейти в панель управления заказом','onclick'=>SPrintF('OrderManage(%u,%u);',$Order['ID'],$Service['ID']),'value'=>'Войти в панель управления'));
if(Is_Error($Comp2))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = new Tag('DIV',Array('style'=>'width: 100%; align: right; text-align: right;'),$Comp2,$Comp1);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'PasswordChangeForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'hidden','name'=>'ServiceOrderID','value'=>$Order['ID']));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'hidden','name'=>'ServiceID','value'=>$Service['ID']));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
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
