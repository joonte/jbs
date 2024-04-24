<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.)
	rewritten by Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$DomainName	= (string) @$Args['DomainName'];
$JSON		= (boolean)@$Args['JSON'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DomainName = Trim($DomainName);
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($DomainName){
	#-------------------------------------------------------------------------------
	// вырезаем начальные http:// и https://
	$DomainName = Str_iReplace('http://','',$DomainName);
	$DomainName = Str_iReplace('https://','',$DomainName);
	#-------------------------------------------------------------------------------
	// убираем завершающий слэш
	$DomainName = Preg_Replace('#/$#','',$DomainName);
	#-------------------------------------------------------------------------------
	// заменяем мусор на дефисы
	$DomainName = Preg_Replace('/[^a-zа-яё0-9-\.]/u','-',Mb_StrToLower($DomainName,'UTF-8'));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/www/DomainWhoIs]: DomainName = %s',$DomainName));
#-------------------------------------------------------------------------------
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
$DOM->AddText('Title','Услуги → Домены → Проверка домена');
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/DomainWhoIs.js}')));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
$Messages = Messages();
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'DomainName','value'=>$DomainName,'onkeydown'=>'if(IsEnter(event)) document.location = "/DomainWhoIs?DomainName=" + document.forms.WhoIsForm.DomainName.value;','prompt'=>$Messages['Prompts']['Domain']['Name'],'type'=>'text'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Span = new Tag('SPAN',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>'document.location = "/DomainWhoIs?DomainName=" + document.forms.WhoIsForm.DomainName.value','value'=>'Проверить'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Span->AddChild($Comp);
#-------------------------------------------------------------------------------
$Table[] = Array('Доменное имя',$Span);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($DomainName){
	#-------------------------------------------------------------------------------
	$DomainName = SPrintF('%s.',$DomainName);
	#-------------------------------------------------------------------------------
	$SubDomains = Explode('.',$DomainName);
	#-------------------------------------------------------------------------------
	$DomainName = $SubDomains[0];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Settings = $Config['Interface']['User']['Orders']['Domain']['DomainWhoIs'];
	#-------------------------------------------------------------------------------
	$Zones = Array();
	#-------------------------------------------------------------------------------
	$Table[] = 'Результаты проверки';
	#-------------------------------------------------------------------------------
	if($Settings['IsSchemesOnly']){
		#-------------------------------------------------------------------------------
		$__USER = $GLOBALS['__USER'];
		#-------------------------------------------------------------------------------
		$UniqID = UniqID('DomainSchemes');
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Services/Schemes','DomainSchemes',$__USER['ID'],Array('ID'),$UniqID,"`IsActive` = 'yes'");
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		// пересортировываем домены по цене
		$IsQuery = DB_Query(SPrintF('UPDATE `%s` SET `SortID` = CONVERT(`CostOrder`,SIGNED INTEGER)',$UniqID));
		if(Is_Error($IsQuery))
			return ERROR | @Trigger_Error('[comp/www/DomainWhoIs]: не удалось пересортировать домены по стоимости регистрации');
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// удаляем дубликаты доменных зон, выбираем все тарифы с минимальными ценами, с подсчётом числа тарифоф
		$DomainSchemes = DB_Select($UniqID,Array('ID','Name','CostOrder','COUNT(*) AS `Counter`'),Array('Where'=>Array("`IsActive` = 'yes'"),'GroupBy'=>'Name'));
		#-------------------------------------------------------------------------------
		switch(ValueOf($DomainSchemes)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			break;
		case 'array':
			#-------------------------------------------------------------------------------
			foreach($DomainSchemes as $DomainScheme){
				#-------------------------------------------------------------------------------
				// зоны которых по одной - не интересны
				if($DomainScheme['Counter'] < 2)
					continue;
				#-------------------------------------------------------------------------------
				$IsDelete = DB_Delete($UniqID,Array('Where'=>SPrintF('`Name` = "%s" AND `ID` != %u',$DomainScheme['Name'],$DomainScheme['ID'])));
				if(Is_Error($IsDelete))
					return ERROR | @Trigger_Error(500);
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
		// JBS-1105: проставляем максимальный коэффициент сортировки для указаной доменной зоны
		if(IsSet($SubDomains[1]) && $SubDomains[1]){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/www/DomainWhoIs]: DomainName = %s; DomainZone = %s',$SubDomains[0],$SubDomains[1]));
			#-------------------------------------------------------------------------------
			$IsUpdate = DB_Update($UniqID,Array('SortID'=>'-1'),Array('Where'=>SPrintF("`Name` = '%s'",$SubDomains[1])));
			if(Is_Error($IsUpdate))
				return ERROR | @Trigger_Error('[comp/www/DomainWhoIs]: не удалось прописать коэффициент сортировки');
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$Columns = Array('ID','Name','CostOrder');
		#-------------------------------------------------------------------------------
		$DomainSchemes = DB_Select($UniqID,$Columns,Array('SortOn'=>Array('SortID','CostOrder'),'Where'=>Array("`IsActive` = 'yes'")));
		#-------------------------------------------------------------------------------
		switch(ValueOf($DomainSchemes)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			break;
		case 'array':
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		foreach($DomainSchemes as $DomainScheme)
			if(IsSet($DomainScheme['ID']))
				$Zones[$DomainScheme['ID']] = Array('Name'=>$DomainScheme['Name'],'CostOrder'=>$DomainScheme['CostOrder']);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(!$Settings['IsSchemesOnly'] || SizeOf($Zones) == 0){
		#-------------------------------------------------------------------------------
		$DomainZones = Comp_Load('Formats/DomainOrder/DomainZones',FALSE,FALSE);
		if(Is_Error($DomainZones))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		foreach($DomainZones as $DomainZone)
			$Zones[$DomainZone['Name']] = Array('Name'=>$DomainZone['Name'],'CostOrder'=>'-');
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Row = $Scripts = $JsonOut = Array();
	#-------------------------------------------------------------------------------
	foreach(Array('Название домена','Статус','Цена в год','Сделать заказ') as $Text)
		$Row[] = new Tag('TD',Array('class'=>'Head'),$Text);
	#-------------------------------------------------------------------------------
	$Rows = Array($Row);
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Zones) as $Key){
		#-------------------------------------------------------------------------------
		// проверяем, допустимо ли имя для доменной зоны (кириллица запрещена в .ru, латиница в .рф)
		#---------------------------------------------------------------------------
		$ZoneData = Comp_Load('Formats/DomainOrder/DomainZones',$Zones[$Key]['Name'],FALSE);
		if(Is_Error($ZoneData))
			return ERROR | @Trigger_Error(500);
		#---------------------------------------------------------------------------
		if(!Preg_Match($ZoneData['Regular'],$DomainName))
			continue;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# если нам нужен только список на выходе - строим его в отдельный массив 
		if($JSON){
			#-------------------------------------------------------------------------------
			$JsonOut[] = $Zones[$Key]['Name'];
			#-------------------------------------------------------------------------------
			continue;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Row = Array();
		#-------------------------------------------------------------------------------
		// удаляем символы ломающие JS
		$ID = Str_Replace('.','-',$Zones[$Key]['Name']);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# имя домена
		$Comp = Comp_Load('Formats/String',SPrintF('%s.%s',$DomainName,$Zones[$Key]['Name']),40);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Row[] = new Tag('TD',Array('class'=>'Standard'),$Comp);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# статус домена
		$Row[] = new Tag('TD',Array('class'=>'Standard'),new Tag('SPAN',Array('id'=>$ID),new Tag('IMG',Array('src'=>'SRC:{Images/load.gif}'))));
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# цена
		$Summ = Comp_Load('Formats/Currency',$Zones[$Key]['CostOrder']);
		if(Is_Error($Summ))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Row[] = $Summ;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# кнопка заказа
		$Row[] = new Tag('TD',Array('class'=>'Standard'),new Tag('SPAN',Array('id'=>SPrintF('%sOrder',$ID))));
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Rows[] = $Row;
		#-------------------------------------------------------------------------------
		# объединённые ячейки, для отображения WhoIs
		$Rows[] = Array(new Tag('TD',Array('colspan'=>4),new Tag('DIV',Array('style'=>'display:none;','id'=>SPrintF('%sInfo',$ID)))));
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// добавляем запуск проверки на onload, со случайным таймаутом
		$Scripts[] = SPrintF("setTimeout(() => { WhoIs('%s','%s',%u,'%s'); }, %s); ",$DomainName,$Zones[$Key]['Name'],$Key,$ID,Rand(0,3000));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# выдаём массив, если нужен именно он
	if($JSON)
		return Array('DomainName'=>$DomainName,'Zones'=>$JsonOut);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$DOM->AddAttribs('Body',Array('onload'=>Implode(' ',$Scripts)));
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Tables/Extended',$Rows,Array('align'=>'center','width'=>'600px;'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = $Comp;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'WhoIsForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY',$Form,new Tag('DIV',Array('id'=>'WhoIsInfo')));
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tab','User/Domain',$NoBody);
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
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
