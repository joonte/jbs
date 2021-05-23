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
$InvoiceID = (integer) @$Args['InvoiceID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Interface']['User']['InvoiceMake'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Invoice = DB_Select('InvoicesOwners',Array('ID','CreateDate','UserID','Summ','PaymentSystemID','IsPosted','IsCheckSent','StatusID','(SELECT `TypeID` FROM `Contracts` WHERE `Contracts`.`ID` = `InvoicesOwners`.`ContractID`) as `ContractTypeID`'),Array('UNIQ','ID'=>$InvoiceID));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('INVOICE_NOT_FOUND','Счёт не найден');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('InvoicesEdit',(integer)$__USER['ID'],(integer)$Invoice['UserID']);
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
if($Invoice['IsPosted'])
	if(!$__USER['IsAdmin'])
		return new gException('ACCOUNT_PAYED','Счёт оплачен и не может быть изменен');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'InvoiceEditForm','onsubmit'=>'return false;'));
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
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/InvoiceEdit.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Изменение счета');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($__USER['IsAdmin']){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('jQuery/DatePicker','CreateDate',$Invoice['CreateDate']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Дата создания',$Comp);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$PaymentSystems = $Config['Invoices']['PaymentSystems'];
#-------------------------------------------------------------------------------
$Options = $Array = Array();
#-------------------------------------------------------------------------------
foreach(Array_Keys($PaymentSystems) as $PaymentSystemID){
	#-------------------------------------------------------------------------------
	$PaymentSystem = $PaymentSystems[$PaymentSystemID];
	#-------------------------------------------------------------------------------
	if(!$PaymentSystem['IsActive'] || !$PaymentSystem['ContractsTypes'][$Invoice['ContractTypeID']])
		continue;
	#-------------------------------------------------------------------------------
	$Array[] = SPrintF("'%s'",$PaymentSystemID);
	#-------------------------------------------------------------------------------
	$Options[$PaymentSystemID] = $PaymentSystem['Name'];
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
if(!Count($Options))
	return new gException('PAYMENT_SYSTEMS_NOT_DEFINED','Платежные системы не определены');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Select',Array('name'=>'PaymentSystemID','size'=>SizeOf($Options),'style'=>'width: 100%;'),$Options);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Settings['PaymentSystemsByType'])
	$Table[] = Array('Платежная система',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Settings['PaymentSystemsByType']){
	#-------------------------------------------------------------------------------
	#------------------------------------------------------------------------------
	$Collations = DB_Select('PaymentSystemsCollation',Array('*'),Array('Where'=>Array('`IsActive` = "yes"',SPrintF('`Source` IN (%s)',Implode(',',$Array))),'SortOn'=>'SortID'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Collations)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('PaymentSystemsCollation_NOT_FOUND','Отсутствуют сопоставления платёжных систем');
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Div = new Tag('DIV',Array('style'=>SPrintF('width: %upx',$Settings['WindowWidth'])));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$List = new Tag('UL',Array('class'=>'pp-showcases'));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	foreach($Collations as $Collation){
		#-------------------------------------------------------------------------------
		$SRC = ($Collation['Image'])?$Collation['Image']:'Blank.png';
		#-------------------------------------------------------------------------------
		$JS = SPrintF("var form = document.forms.InvoiceEditForm; form.PaymentSystemID.value = '%s'; FormEdit('/API/InvoiceEdit','InvoiceEditForm','Изменение счета');;",$Collation['Source']);
		#-------------------------------------------------------------------------------
		$Image = new Tag('IMG',Array('src'=>SPrintF('SRC:{Images/PaymentSystems/%s}',$SRC),'style'=>'cursor: pointer;','vspace'=>5,'hspace'=>5,'width'=>$Settings['ImageWidth'],'height'=>$Settings['ImageHeight'],'onclick'=>$JS));
		#-------------------------------------------------------------------------------
		$LinkID = UniqID('IMG');
		#-------------------------------------------------------------------------------
		$Links[$LinkID] = &$Image;
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Prompt',$LinkID,$Collation['Prompt']);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		UnSet($Links[$LinkID]);
		#-------------------------------------------------------------------------------
		$Div1 = new Tag('DIV',$Image,new Tag('DIV',Array('style'=>'margin:0 0 0 3px; width:145px; white-space:nowrap; overflow:hidden;'),$Collation['Description']));
		#-------------------------------------------------------------------------------
		$List->AddChild(new Tag('LI',Array('class'=>'pp-rounded-5px'),$Div1));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Div->AddChild($List);
	#-------------------------------------------------------------------------------
	$Table[] = $Div;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'PaymentSystemID','type'=>'hidden','value'=>''));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($__USER['IsAdmin']){
	#-------------------------------------------------------------------------------
	// достаём поля счёта
	$InvoicesItems = DB_Select('InvoicesItems',Array('*'),Array('Where'=>SPrintF('`InvoiceID` = %u',$InvoiceID)));
	#-------------------------------------------------------------------------------
	switch(ValueOf($InvoicesItems)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('INVOICE_FIELDS_NOT_FOUND','Поля счёта не найден');
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// идентфикаторы элементов счёта
	$InvoicesItemsIDs = Array();
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	foreach($InvoicesItems as $InvoicesItem){
		#-------------------------------------------------------------------------------
		$InvoicesItemsIDs[] = $InvoicesItem['ID'];
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// сервис
		$Services = DB_Select('ServicesOwners',Array('ID','Code','Item','NameShort'),Array(/*'Where'=>'`IsActive` = "yes"',*/'SortOn'=>'SortID'));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Services)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			#-------------------------------------------------------------------------------
			$ServiceOptions = Array('Сервисы отсутствуют');
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		case 'array':
			#-------------------------------------------------------------------------------
			$ServiceOptions = Array();
			#---------------------------------------------------------------------------
			foreach($Services as $Service)
				$ServiceOptions[$Service['ID']] = SPrintF('%s (%s)',$Service['Code'],$Service['NameShort']);
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		$Services = Comp_Load('Form/Select',Array('name'=>SPrintF('ServiceIDs[%u]',$InvoicesItem['ID']),'OnChange'=>'InvoiceSumm();','style'=>'width:100%;','prompt'=>'Сервис, оплачиваемый этим пунктом счёта'),$ServiceOptions,$InvoicesItem['ServiceID']);
		if(Is_Error($Services))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array('Сервис',$Services);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// комментарий
		$Comp = Comp_Load('Form/Input',Array('type'=>'text','OnKeyPress'=>'InvoiceSumm();','name'=>SPrintF('Comments[%u]',$InvoicesItem['ID']),'style'=>'width:100%;','value'=>$InvoicesItem['Comment'],'prompt'=>'Комментарий к услуге'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array('Комментарий',$Comp);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// количество
		$Comp = Comp_Load('Form/Input',Array('type'=>'text','OnKeyPress'=>'InvoiceSumm();','name'=>SPrintF('Amounts[%u]',$InvoicesItem['ID']),'style'=>'width:100%;','value'=>$InvoicesItem['Amount'],'prompt'=>'Оплачиваемое количество: дни, годы, штуки...'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array('Количество',$Comp);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// сумма
		$Comp = Comp_Load('Form/Summ',Array('value'=>$InvoicesItem['Summ'],'onkeyup'=>'InvoiceSumm();','name'=>SPrintF('ItemSumms[%u]',$InvoicesItem['ID'])));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array('Сумма за услугу',$Comp);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// удалить
		$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','OnChange'=>'InvoiceSumm();','name'=>SPrintF('IsDeletes[%u]',$InvoicesItem['ID']),'id'=>SPrintF('IsDeletes[%u]',$InvoicesItem['ID']),'value'=>'yes'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array(new Tag('LABEL',Array('for'=>SPrintF('IsDeletes[%u]',$InvoicesItem['ID'])),'Удалить элемент'),$Comp);
		#-------------------------------------------------------------------------------
		// разделитель
		$Table[] = Array('',new Tag('HR'));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	// скрытое поле с идентфикаторами - используются при подсчёте итоговой суммы
	$Comp = Comp_Load('Form/Input',Array('name'=>'InvoicesItemsIDs','type'=>'hidden','value'=>Implode(',',$InvoicesItemsIDs)));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------

}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($__USER['IsAdmin']){
	#-------------------------------------------------------------------------------
	// Фискализация
	$Comp = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'IsCheckSent','id'=>'IsCheckSent','value'=>'yes'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($Invoice['IsCheckSent'])
		$Comp->AddAttribs(Array('checked'=>'yes'));
	#-------------------------------------------------------------------------------
	$Table[] = Array(new Tag('LABEL',Array('for'=>'IsCheckSent'),'Чек отослан'),$Comp);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// разделитель
	$Table[] = Array('',new Tag('HR'));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Summ',Array('value'=>$Invoice['Summ'],'name'=>'Summ','id'=>'Summ','disabled'=>TRUE,'prompt'=>'Итоговая сумма рассчитывается автоматически по входящим в счёт услугам'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Итого',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Settings['PaymentSystemsByType']){
	#-------------------------------------------------------------------------------
	// если по типу - то кнопка не нужна, по логотипам надо кликать
	$Comp = Comp_Load('Form/Input',Array('onclick'=>"FormEdit('/API/InvoiceEdit','InvoiceEditForm','Изменение счета');",'type'=>'button','value'=>'Изменить'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = $Comp;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'InvoiceID','type'=>'hidden','value'=>$Invoice['ID']));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Form);
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
