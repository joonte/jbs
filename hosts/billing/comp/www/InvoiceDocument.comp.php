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
$InvoiceID	= (integer) @$Args['InvoiceID'];
$IsRemote	= (boolean) @$Args['IsRemote'];
$Mobile		=  (string) @$Args['Mobile'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/Upload.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if($Mobile){
	#-------------------------------------------------------------------------------
	# удаляем из телефона всё кроме цифр
	$Mobile = Preg_Replace('/[^0-9]/','',$Mobile);
	#-------------------------------------------------------------------------------
	if(!Preg_Match($Regulars['SMS'],$Mobile)){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/www/InvoiceDocument]: WRONG_MOBILE = %s',$Mobile));
		#-------------------------------------------------------------------------------
		return new gException('WRONG_MOBILE','Номер мобильного телефона указан неверно');
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	if(!SetCookie('Mobile',$Mobile,Time() + 364*24*3600,'/'))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$InvoiceID)
	return new gException('NO_INVOICE_ID','Не указан номер счёта, невозможно отобразить документ');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Invoice = DB_Select('InvoicesOwners',Array('ID','UserID','PaymentSystemID','IsPosted','StatusID','Summ'),Array('UNIQ','ID'=>$InvoiceID));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
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
$PaymentSystemID = $Invoice['PaymentSystemID'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('InvoicesRead',(integer)$GLOBALS['__USER']['ID'],(integer)$Invoice['UserID']);
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
$IsPayed = ($Invoice['IsPosted'] && $Invoice['StatusID'] != 'Conditionally') || (!$Invoice['IsPosted'] && $Invoice['StatusID'] == 'NotConfirmed');
#-------------------------------------------------------------------------------
// это киви, не оплачен, телефон не введён
if($PaymentSystemID == 'QIWI' && !$IsPayed && !$Mobile){
	#-------------------------------------------------------------------------------
	# выбираем телефон юзера, первый из списка
	foreach($GLOBALS['__USER']['Contacts'] as $Contact)
		if($Contact['MethodID'] == 'SMS')
			$Address = $Contact['Address'];
	#-------------------------------------------------------------------------------
	$Mobile = IsSet($_COOKIE['Mobile'])?$_COOKIE['Mobile']:(IsSet($Address)?$Address:'');
	#-------------------------------------------------------------------------------
	$DOM->AddText('Title','Оплата QIWI требует телефонный номер');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Table = Array('Требуется телефонный номер');
	#-------------------------------------------------------------------------------
	$Messages = Messages();
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'text','style'=>'width: 100%;','name'=>'Mobile','value'=>$Mobile,'prompt'=>$Messages['Prompts']['SMS']));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array(new Tag('DIV',Array('id'=>'QiwiPhone'),'Телефонный номер'),$Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Tables/Standard',$Table,Array('width'=>'100%'));
	#-------------------------------------------------------------------------------
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Form = new Tag('FORM',Array('action'=>'/InvoiceDocument','method'=>'POST','name'=>'MobileInputForm'));
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load(
			'Form/Input',
			Array(
				'type'		=>'button',
				'name'		=>'Submit',
				'value'		=> 'Продолжить',
				'onclick'	=> "javascript: if(form.Mobile.value.charAt(0) == 8 || form.Mobile.value.charAt(0) == 9 || (form.Mobile.value.charAt(0) == \"+\" && (form.Mobile.value.charAt(1) == 8 || form.Mobile.value.charAt(1) == 9))){ ShowConfirm('С цифры 8 начинаются коды таких стран как Китай, Бангладеш и т.п. С цифры 9 начинаются телефонов в Афганистане, Монголии, Турции ... Вы уверены что ваш мобильный телефон относится именно к этой стране? Например код РФ: 7, Беларуси: 375, Украины: 380. Соответственно, обычный номер Российского мобильного телефона выглядит так: 79262223344. Вы всё ещё хотите сохранить свой телефонный номер в таком виде?','ShowWindow(\'/InvoiceDocument\',{Mobile:\'' + form.Mobile.value + '\',InvoiceID:' + form.InvoiceID.value + '})'); }else{ ShowWindow('/InvoiceDocument',FormGet(form)); }"
				)
			);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'InvoiceID','type'=>'hidden','value'=>$InvoiceID));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'IsRemote','type'=>'hidden','value'=>$IsRemote));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',$Form);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Out = $DOM->Build(FALSE);
	#-------------------------------------------------------------------------------
	if(Is_Error($Out))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	return Array('Status'=>'Ok','DOM'=>$DOM->Object);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp1 = Comp_Load('Buttons/Standard',Array('onclick'=>SPrintF("document.location = '/InvoiceDownload?InvoiceID=%u&IsStamp=yes';",$Invoice['ID'])),'Скачать счёт в формате PDF','PDF.gif');
if(Is_Error($Comp1))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp2 = Comp_Load('Buttons/Standard',Array('onclick'=>SPrintF("document.location = '/InvoiceDownload?InvoiceID=%u&IsTIFF=yes&IsStamp=yes';",$Invoice['ID'])),'Скачать счёт в формате TIFF','Image.gif');
if(Is_Error($Comp2))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$PDF = Array(
		'Comp'	=> $Comp1,
		'Name'	=> new Tag('A',Array('onclick'=>SPrintF("document.location = '/InvoiceDownload?InvoiceID=%u&IsStamp=yes';return FALSE;",$Invoice['ID'])),'Скачать счёт в формате PDF'),
		);
#-------------------------------------------------------------------------------
$TIFF = Array(
		'Comp'	=> $Comp2,
		'Name'	=> new Tag('A',Array('onclick'=>SPrintF("document.location = '/InvoiceDownload?InvoiceID=%u&IsTIFF=yes&IsStamp=yes';return FALSE;",$Invoice['ID'])),'Скачать счёт в формате TIFF'),
		);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Buttons/Panel',$PDF,$TIFF);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$PaymentSystem = $Config['Invoices']['PaymentSystems'][$PaymentSystemID];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// проверяем наличие файла
$Files = GetUploadedFiles('Invoices',$Invoice['ID']);
#-------------------------------------------------------------------------------
if(SizeOf($Files)){
	#-------------------------------------------------------------------------------
	// файл есть, используем последний элемент массива
	$File = End($Files);
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	return new gException('INVOICE_NOT_BUILDED','Счёт не сформирован');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Document = new DOM($File['Data']);
#-------------------------------------------------------------------------------
$Document->Delete('Logo');
#-------------------------------------------------------------------------------
$Document->Delete('Rubbish');
#-------------------------------------------------------------------------------
$Document->DeleteIDs();
#-------------------------------------------------------------------------------
$Div = new Tag('DIV',Array('class'=>'Standard','style'=>'max-width:700px;'),$Document->Object);
#-------------------------------------------------------------------------------
if($IsPayed){
	#-------------------------------------------------------------------------------
	$DOM->AddText('Title',' (Оплачен)');
	#-------------------------------------------------------------------------------
}elseif($PaymentSystem['IsContinuePaying']){	// неоплачен и требуется оплачивать на сайте ПС
	#-------------------------------------------------------------------------------
	# проверяем состав счёта, смотрим не требуется ли какой-то подтверждённый контакт
	$Services = DB_Select('InvoicesItems',Array('(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = `InvoicesItems`.`ServiceID`) AS `Code`'),Array('Where'=>SPrintF('`InvoiceID` = %u',$Invoice['ID'])));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Services)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		$Settings = $Config['Interface']['User']['Orders'];
		#-------------------------------------------------------------------------------
		foreach ($Services as $Service){
			#-------------------------------------------------------------------------------
			if(IsSet($Settings[$Service['Code']])){
				#-------------------------------------------------------------------------------
				$IsConfirmed = FALSE;
				#-------------------------------------------------------------------------------
				if(IsSet($Settings[$Service['Code']]['EmailConfirmRequire']) && $Settings[$Service['Code']]['EmailConfirmRequire']){
					#-------------------------------------------------------------------------------
					foreach($GLOBALS['__USER']['Contacts'] as $Contact)
						if($Contact['MethodID'] == 'Email')
							if($Contact['Confirmed'])
								$IsConfirmed = TRUE;
					#-------------------------------------------------------------------------------
				}else{
					#-------------------------------------------------------------------------------
					$IsConfirmed = TRUE;
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
				if(!$IsConfirmed)
					$Message = "Для оплаты и активации заказанной услуги требуется подтверждённый почтовый адрес. Нажмите на вашу аватару, \"Аккаунт\" и подтвердите ваш почтовый адрес";
				#-------------------------------------------------------------------------------
				#-------------------------------------------------------------------------------
				$IsConfirmed = FALSE;
				#-------------------------------------------------------------------------------
				if(IsSet($Settings[$Service['Code']]['MobileConfirmRequire']) && $Settings[$Service['Code']]['MobileConfirmRequire']){
					#-------------------------------------------------------------------------------
					foreach($GLOBALS['__USER']['Contacts'] as $Contact)
						if($Contact['MethodID'] == 'SMS')
							if($Contact['Confirmed'])
								$IsConfirmed = TRUE;
					#-------------------------------------------------------------------------------
				}else{
					#-------------------------------------------------------------------------------
					$IsConfirmed = TRUE;
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
				if(!$IsConfirmed)
					$Message = "Для оплаты и активации заказанной услуги требуется подтверждённый номер мобильного телефона (SMS). Нажмите на вашу аватару, \"Аккаунт\", добавьте контакт \"SMS-сообщения\" и подтвердите ваш телефон";
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(IsSet($Message)){
		#-------------------------------------------------------------------------------
		$OnClick = SPrintF("ShowAlert('%s','Warning')",$Message);
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		/* form.submit(); */
		$OnClick = "ShowProgress('Вход в платежную систему');document.InvoicePay.submit();";
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Button = Comp_Load('Form/Input',Array('onclick'=>$OnClick,'type'=>'button','style'=>'font-size:25px;color:#F07D00;','value'=>'Оплатить →'));
	if(Is_Error($Button))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	# если тестовый режим - то другой URL
	$Cpp = (IsSet($PaymentSystem['TestMode']) && $PaymentSystem['TestMode'] && IsSet($PaymentSystem['TestModeCpp']))?$PaymentSystem['TestModeCpp']:$PaymentSystem['Cpp'];
	#-------------------------------------------------------------------------------
	$Form = new Tag('FORM',Array('action'=>$Cpp,'method'=>'POST','name'=>'InvoicePay'),new Tag('BR'),new Tag('DIV',$Button));
	#-------------------------------------------------------------------------------
	$Send = Comp_Load(SPrintF('Invoices/PaymentSystems/%s',$PaymentSystem['Comp']),$PaymentSystemID,$Invoice['ID'],$Invoice['Summ']);
	if(Is_Error($Send))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($Mobile)
		$Send['to'] = SPrintF('+%s',$Mobile);
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Send) as $ParamID){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('name'=>$ParamID,'type'=>'hidden','value'=>$Send[$ParamID]));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Form->AddChild($Comp);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	if($IsRemote){
		#-------------------------------------------------------------------------------
		$Out = $Document->Build();
		#-------------------------------------------------------------------------------
		if(Is_Error($Out))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		return Array('Status'=>'Ok','Document'=>$Out,'Cpp'=>$Cpp,'Send'=>$Send);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Div->AddChild($Form);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// изменение платёжной системы, ссылка
	$A = new Tag('A',Array('style'=>'font-size:12px;','href'=>SPrintF("javascript:ShowWindow('/InvoiceEdit',{InvoiceID:%u});",$Invoice['ID'])),'[изменить]');
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',new Tag('DIV',Array('class'=>'Title'),new Tag('CDATA',$PaymentSystem['Name']),$A));
	#-------------------------------------------------------------------------------
	// кнопка вверху страницы оплаты, для мобильной версии
	$DOM->AddChild('Into',new Tag('DIV',Array('id'=>'MobilePaymentButton'),$Button));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Div);
#-------------------------------------------------------------------------------
$Out = $DOM->Build(FALSE);
#-------------------------------------------------------------------------------
if(Is_Error($Out))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
