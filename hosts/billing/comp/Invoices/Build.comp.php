<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('InvoiceID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/DOM.class.php','libs/Wizard.php','libs/Upload.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Invoice = DB_Select('Invoices',Array('ID','CreateDate','ContractID','PaymentSystemID','Summ'),Array('UNIQ','ID'=>$InvoiceID));
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
$InvoiceID = $Invoice['ID'];
#-------------------------------------------------------------------------------
$Contract = DB_Select('Contracts',Array('ID','CreateDate','ProfileID'),Array('UNIQ','ID'=>$Invoice['ContractID']));
#-------------------------------------------------------------------------------
switch(ValueOf($Contract)){
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
$ContractID = (integer)$Contract['ID'];
#-------------------------------------------------------------------------------
$IsQuery = DB_Query(SPrintF('UPDATE `Invoices` SET `CreateDate` = IF(`CreateDate` < %u,%u,`CreateDate`) WHERE `ContractID` = %u',$Contract['CreateDate'],$Contract['CreateDate'],$ContractID));
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Contract/Number',$ContractID);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Contract['Number'] = $Comp;
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Date/Standard',$Contract['CreateDate']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Contract['CreateDate'] = $Comp;
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$PaymentSystem = $Config['Invoices']['PaymentSystems'][$Invoice['PaymentSystemID']];
#-------------------------------------------------------------------------------
$Replace = Array('Contract'=>$Contract,'PaymentSystem'=>$PaymentSystem);
#-------------------------------------------------------------------------------
$ProfileID = (integer)$Contract['ProfileID'];
#-------------------------------------------------------------------------------
if($ProfileID){
	#-------------------------------------------------------------------------------
	$Profile = Comp_Load('www/Administrator/API/ProfileCompile',Array('ProfileID'=>$ProfileID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Profile)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		$Replace['Customer'] = $Profile['Attribs'];
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Invoice/Number',$InvoiceID);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Invoice['Number'] = $Comp;
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Date/Standard',$Invoice['CreateDate']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Invoice['CreateDate'] = $Comp;
#-------------------------------------------------------------------------------
$Summ = $Invoice['Summ'];
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Currency',$Summ);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Invoice['Summ'] = $Comp;
#-------------------------------------------------------------------------------
$Invoice['Foreign'] = SPrintF('%01.2f',$Summ/$PaymentSystem['Course']);
#-------------------------------------------------------------------------------
$Wizard = Wizard_ToString((double)$Invoice['Summ']);
if(Is_Error($Wizard))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Nds = Comp_Load('Formats/Currency',($Summ*20)/120);
if(Is_Error($Nds))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Wizard = SPrintF('%s. %s',$Wizard,$Config['Executor']['IsNds']?SPrintF('(в том числе НДС %s)',$Nds):'(НДС не облагается)');
#-------------------------------------------------------------------------------
$Invoice['Wizard'] = $Wizard;
#-------------------------------------------------------------------------------
$Executor = Comp_Load('www/Administrator/API/ProfileCompile',Array('ProfileID'=>100));
#-------------------------------------------------------------------------------
switch(ValueOf($Executor)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'array':
	#-------------------------------------------------------------------------------
	// QR код на оплату
	Debug(print_r($Executor,true));
	/*
		ST00011|Name=ООО "НИКС Компьютерный Супермаркет"|PersonalAcc=40702810738090001511|BankName=ПАО СБЕРБАНК г МОСКВА|BIC=044525225|CorrespAcc=30101810400000000225|PayeeINN=5008040124|KPP=500801001|Sum=16141598.000000|LastName=ООО "ЭКСИМИУС"|Purpose=оплата по счету №580685/1155 от 05.04.2021, в том числе НДС 20% - 26902.66
	*/
	// массив с полями платёжки
	$QR = Array();
	#-------------------------------------------------------------------------------
	// имя конторы
	$QR ['Name'] = SPrintF('%s "%s"',$Executor['Attribs']['CompanyForm'],$Executor['Attribs']['CompanyName']);
	#-------------------------------------------------------------------------------
	// банковский счёт
	$QR['PersonalAcc'] = $Executor['Attribs']['BankAccount'];
	#-------------------------------------------------------------------------------
	// имя банка
	$QR['BankName'] = $Executor['Attribs']['BankName'];
	#-------------------------------------------------------------------------------
	// БИК
	$QR['BIC'] = $Executor['Attribs']['Bik'];
	#-------------------------------------------------------------------------------
	// Корреспондентский счёт
	$QR['CorrespAcc'] = $Executor['Attribs']['Kor'];
	#-------------------------------------------------------------------------------
	// ИНН
	$QR['PayeeINN'] = $Executor['Attribs']['Inn'];
	#-------------------------------------------------------------------------------
	// КПП
	$QR['KPP'] = $Executor['Attribs']['Kpp'];
	#-------------------------------------------------------------------------------
	// сумма платежа, в копейках
	$QR['Sum'] = $Summ*100;
	#-------------------------------------------------------------------------------
	// плательщик
	//$QR['LastName'] = $Executor['Attribs'][''];
	#-------------------------------------------------------------------------------
	// примечание к платежу
	$QR['Purpose'] = SPrintF('Оплата по счёту %s',$Invoice['Number']);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$QRText = 'ST00011';
	#-------------------------------------------------------------------------------
	// составляем текстовую строку
	foreach(Array_Keys($QR) as $Key)
		$QRText = SPrintF('%s|%s',$QRText,($Key)?SPrintF('%s=%s',$Key,$QR[$Key]):$QR[$Key]);
	#-------------------------------------------------------------------------------
	// делаем ссылку
	$QRLink = SPrintF('https://chart.googleapis.com/chart?cht=qr&chs=250x250&chld=M|0&chl=%s',UrlEncode($QRText));
	Debug(SPrintF('[comp/Invoices/Build]: QR: %s',$QRLink));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Replace['Executor'] = $Executor['Attribs'];
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Clauses/Load',SPrintF('Invoices/PaymentSystems/%s/%s',$Invoice['PaymentSystemID'],$Executor['TemplateID']));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($Comp['IsExists']){
		#-------------------------------------------------------------------------------
		$DOM = new DOM($Comp['DOM']);
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
case 'exception':
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Clauses/Load',SPrintF('Invoices/PaymentSystems/%s',$Invoice['PaymentSystemID']));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$DOM = new DOM($Comp['DOM']);
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}

if(IsSet($QRLink))
	$DOM->AddChild('QRCode',new Tag('IMG',Array('src'=>$QRLink,'style'=>'float:right;')));

#-------------------------------------------------------------------------------
$Comp = Comp_Load('Clauses/Load','Invoices/Services');
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Services',$Comp['DOM']);
#-------------------------------------------------------------------------------
$Replace['Invoice'] = $Invoice;
#-------------------------------------------------------------------------------
$InvoiceItems = DB_Select('InvoicesItems','*',Array('Where'=>SPrintF('`InvoiceID` = %u',$InvoiceID)));
#-------------------------------------------------------------------------------
switch(ValueOf($InvoiceItems)){
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
if(IsSet($DOM->Links['Item'])){
	#-------------------------------------------------------------------------------
	$Childs = $DOM->Links['Item']->Childs;
	#-------------------------------------------------------------------------------
	foreach($InvoiceItems as $Item){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Formats/Currency',$Item['Summ']);
		if(Is_Error($Summ))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Item['Summ'] = $Comp;
		#-------------------------------------------------------------------------------
		$Service = DB_Select('Services','*',Array('UNIQ','ID'=>$Item['ServiceID']));
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
		$Item['Service'] = $Service;
		#-------------------------------------------------------------------------------
		$OrderID = (integer)$Item['OrderID'];
		#-------------------------------------------------------------------------------
		if($OrderID){
			#-------------------------------------------------------------------------------
			$OrderID = Comp_Load('Formats/Order/Number',$Item['OrderID']);
			if(Is_Error($OrderID))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$Item['Order'] = Array('Number'=>$OrderID);
		#-------------------------------------------------------------------------------
		$Replacing = Array_ToLine($Item,'%');
		#-------------------------------------------------------------------------------
		$Tr = new Tag('TR');
		#-------------------------------------------------------------------------------
		foreach($Childs as $Child){
			#-------------------------------------------------------------------------------
			$Td = Clone($Child);
			#-------------------------------------------------------------------------------
			foreach(Array_Keys($Replacing) as $Pattern){
				#-------------------------------------------------------------------------------
				$String = ($Replacing[$Pattern]?$Replacing[$Pattern]:'-');
				#-------------------------------------------------------------------------------
				$Td->Text = Str_Replace($Pattern,$String,$Td->Text);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			$Tr->AddChild($Td);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$DOM->AddChild('Items',$Tr);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$DOM->Delete('Item');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Document = $DOM->Build();
if(Is_Error($Document))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Replace = Array_ToLine($Replace);
#-------------------------------------------------------------------------------
foreach(Array_Keys($Replace) as $LinkID){
	#-------------------------------------------------------------------------------
	$Text = (string)$Replace[$LinkID];
	#-------------------------------------------------------------------------------
	$Document = Str_Replace(SPrintF('%%%s%%',$LinkID),$Text?$Text:'-',$Document);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём все файлы и удаляем
$Files = GetUploadedFilesInfo('Invoices',$Invoice['ID']);
#-------------------------------------------------------------------------------
foreach($Files as $File)
	if(!DeleteUploadedFile($File['ID']))
		return new gException('CANNOT_DELETE_FILE','Не удалось удалить связанный файл');
#-------------------------------------------------------------------------------
// кладём новый файл
if(!SaveUploadedFile(Array(Array('Data'=>$Document,'Name'=>SPrintF('Invoice%s.html',$InvoiceID),'Size'=>Mb_StrLen($Document,'8bit'),'Mime'=>'text/html')),'Invoices',$InvoiceID))
	return new gException('CANNOT_SAVE_UPLOADED_FILE','Не удалось сохранить загруженный файл');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
