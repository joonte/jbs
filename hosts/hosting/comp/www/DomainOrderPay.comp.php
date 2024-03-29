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
$DomainOrderID  = (integer) @$Args['DomainOrderID'];
$OrderID        = (integer) @$Args['OrderID'];
$YearsPay       = (integer) @$Args['YearsPay'];
$IsChange       = (boolean) @$Args['IsChange'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array(
			'ID','ContractID','OrderID','UserID','DomainName','ExpirationDate','AuthInfo','StatusID','SchemeID','ProfileID',
			'CONCAT(`Ns1Name`,",",`Ns2Name`,",",`Ns3Name`,",",`Ns4Name`) AS `DNSs`',	// DNS for JBS-1337
			'(SELECT `GroupID` FROM `Users` WHERE `DomainOrdersOwners`.`UserID` = `Users`.`ID`) as `GroupID`',
			'(SELECT `IsPayed` FROM `Orders` WHERE `Orders`.`ID` = `DomainOrdersOwners`.`OrderID`) as `IsPayed`',
			'(SELECT `Balance` FROM `Contracts` WHERE `DomainOrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractBalance`',
			'(SELECT `TypeID` FROM `Contracts` WHERE `DomainOrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractTypeID`',
			'(SELECT `Name` FROM `DomainSchemes` WHERE `DomainSchemes`.`ID` = `SchemeID`) as `SchemeName`',
			'(SELECT `Params` FROM `Servers` WHERE `Servers`.`ID` = `DomainOrdersOwners`.`ServerID`) AS `Params`'
		);
#-------------------------------------------------------------------------------
$Where = ($DomainOrderID?SPrintF('`ID` = %u',$DomainOrderID):SPrintF('`OrderID` = %u',$OrderID));
#-------------------------------------------------------------------------------
$DomainOrder = DB_Select('DomainOrdersOwners',$Columns,Array('UNIQ','Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrder)){
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
$UserID = (integer)$DomainOrder['UserID'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('DomainOrdersRead',(integer)$GLOBALS['__USER']['ID'],$UserID);
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
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Interface']['User']['Orders']['Domain']['Prolong'];
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
$DOM->AddText('Title',SPrintF('Оплата заказа домена %s.%s',$DomainOrder['DomainName'],$DomainOrder['SchemeName']));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/DomainOrderPay.js}')));
#-------------------------------------------------------------------------------
$StatusID = $DomainOrder['StatusID'];
#-------------------------------------------------------------------------------
if(!In_Array($StatusID,Array('Waiting','Active','Suspended','ForTransfer')))
	return new gException('ORDER_CAN_NOT_PAY','Заказ домена не может быть оплачен');
#-------------------------------------------------------------------------------
// для не-советских доменов
if(!In_Array($DomainOrder['SchemeName'],Array('su'))){
	#-------------------------------------------------------------------------------
	if($StatusID == 'ForTransfer' && StrLen($DomainOrder['AuthInfo']) < 3)
		return new gException('NEED_AUTHINFO','До оплаты домена, введите его код AuthInfo (иногда его называют пароль/код переноса)');
	#-------------------------------------------------------------------------------
	if($StatusID == 'ForTransfer' && Is_Null($DomainOrder['ProfileID']))
		return new gException('NEED_OWNER','До оплаты домена, определите владельца для него (кнопка с "человечком" в строке заказа)');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$DomainScheme = DB_Select('DomainSchemes','*',Array('UNIQ','ID'=>$DomainOrder['SchemeID']));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainScheme)){
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
# JBS-1101
$IsPayed = $DomainOrder['IsPayed'];
#-------------------------------------------------------------------------------
if(!$IsPayed)
	$DomainScheme['MaxActionYears'] = 1;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Currency',$DomainScheme['CostOrder']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
// если это продление - показывать цену заказа не надо, иначе начинают задавать глупые вопросы
if(!$IsPayed)
	$Table[] = Array('Стоимость заказа (в год)',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Currency',$DomainScheme['CostProlong']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Стоимость продления (в год)',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($StatusID == 'ForTransfer'){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Currency',$DomainScheme['CostTransfer']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Стоимость переноса (разово)',$Comp);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('name'=>'DomainOrderID','type'=>'hidden','value'=>$DomainOrder['ID']));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ExpirationDate = $DomainOrder['ExpirationDate'];
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'DomainOrderPayForm','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
if($YearsPay){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('name'=>'YearsPay','type'=>'hidden','value'=>$YearsPay));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Form->AddChild($Comp);
	#-------------------------------------------------------------------------------
	if($IsPayed){
		#-------------------------------------------------------------------------------
		if(!$DomainScheme['IsProlong'])
			return new gException('SCHEME_NOT_ALLOW_PROLONG','Тарифный план заказа домена не позволяет продление');
		#-------------------------------------------------------------------------------
		$YearsRemainder = Date('Y',$ExpirationDate) - Date('Y') - 1;
		#-------------------------------------------------------------------------------
		if($YearsRemainder >= $DomainScheme['MaxActionYears'] && $StatusID != 'ForTransfer')
			return new gException('DOMAIN_ORDER_ON_MAX_YEARS_1','Доменное имя уже зарегистрировано на максимальное кол-во лет');
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		if($YearsPay < $DomainScheme['MinOrderYears'])
			return new gException('YEARS_PAY_MIN_ORDER_YEARS','Кол-во лет оплаты меньше, чем допустимое значение лет заказа, определённое в тарифном плане');
		#-------------------------------------------------------------------------------
		if($YearsPay > $DomainScheme['MaxActionYears'])
			return new gException('YEARS_PAY_MAX_ACTION_YEARS','Кол-во лет оплаты больше, чем допустимое значение, опредлёенное в тарифном плане');
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$DomainBonuses = Array();
	#-------------------------------------------------------------------------------
	if(Is_Error(DB_Transaction($TransactionID = UniqID('DomainOrderPay'))))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if(FALSE){
		#-------------------------------------------------------------------------------
		$Columns = Array('(SELECT `SchemeID` FROM `HostingOrders` WHERE `HostingOrders`.`OrderID` = `Basket`.`OrderID`) as `SchemeID`','Amount');
		#-------------------------------------------------------------------------------
		$Basket = DB_Select('Basket',$Columns,Array('Where'=>SPrintF('(SELECT `ServiceID` FROM `Orders` WHERE `Orders`.`ID` = `OrderID`) = 10000 AND (SELECT `ContractID` FROM `Orders` WHERE `Orders`.`ID` = `OrderID`) = %u',$DomainOrder['ContractID'])));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Basket)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			# No more...
	                break;
		case 'array':
			#-------------------------------------------------------------------------------
			$Entrance = Tree_Path('Groups',(integer)$DomainOrder['GroupID']);
			#-------------------------------------------------------------------------------
			switch(ValueOf($Entrance)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				#-------------------------------------------------------------------------------
				foreach($Basket as $Order){
					#-------------------------------------------------------------------------------
					# HostingDomainPolitics deleted
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
				break 2;
				#-------------------------------------------------------------------------------
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		$CostPay = 0.00;
		#-------------------------------------------------------------------------------
		$YearsRemainded = $YearsPay;
		#-------------------------------------------------------------------------------
		while($YearsRemainded){
			#-------------------------------------------------------------------------------
			if($StatusID == 'ForTransfer'){
				#-------------------------------------------------------------------------------
				$CurrentCost = $DomainScheme['CostTransfer'];
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				$CurrentCost = $DomainScheme[(!$IsPayed && $YearsPay - $YearsRemainded < $DomainScheme['MinOrderYears']?'CostOrder':'CostProlong')];
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			#Debug(SPrintF('[comp/www/DomainOrderPay]: CurrentCost = %s',$CurrentCost));
			#-------------------------------------------------------------------------------
			$Where = SPrintF("`UserID` = %u AND ((`SchemeID` = %u OR %u IN (SELECT `SchemeID` FROM `DomainSchemesGroupsItems` WHERE `DomainSchemesGroupsItems`.`DomainSchemesGroupID` = `DomainBonuses`.`DomainSchemesGroupID`)) OR ISNULL(`SchemeID`) AND ISNULL(`DomainSchemesGroupID`)) AND `YearsRemainded` > 0",$UserID,$DomainScheme['ID'],$DomainScheme['ID']);
			#-------------------------------------------------------------------------------
			$DomainBonus = DB_Select('DomainBonuses','*',Array('IsDesc'=>TRUE,'SortOn'=>'Discont','Where'=>$Where));
			#-------------------------------------------------------------------------------
			switch(ValueOf($DomainBonus)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				#-------------------------------------------------------------------------------
				$CostPay += $YearsRemainded*$CurrentCost;
				#-------------------------------------------------------------------------------
				$YearsRemainded = 0;
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			case 'array':
				#-------------------------------------------------------------------------------
				$DomainBonus = Current($DomainBonus);
				#-------------------------------------------------------------------------------
				$Discont = (1 - $DomainBonus['Discont']);
				#-------------------------------------------------------------------------------
				if($DomainBonus['YearsRemainded'] - $YearsRemainded < 0){
					#-------------------------------------------------------------------------------
					$CostPay += $DomainBonus['YearsRemainded']*$CurrentCost*$Discont;
					#-------------------------------------------------------------------------------
					$UDomainBonus = Array('YearsRemainded'=>0);
					#-------------------------------------------------------------------------------
					$YearsRemainded -= $DomainBonus['YearsRemainded'];
					#-------------------------------------------------------------------------------
					$Comp = Comp_Load('Formats/Percent',$DomainBonus['Discont']);
					if(Is_Error($Comp))
						return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
					$Tr = new Tag('TR');
					#-------------------------------------------------------------------------------
					foreach(Array($DomainBonus['YearsRemainded'],$Comp) as $Text)
						$Tr->AddChild(new Tag('TD',Array('class'=>'Standard','align'=>'right'),$Text));
					#-------------------------------------------------------------------------------
					$DomainBonuses[] = $Tr;
					#-------------------------------------------------------------------------------
				}else{
					#-------------------------------------------------------------------------------
					$CostPay += $YearsRemainded*$CurrentCost*$Discont;
					#-------------------------------------------------------------------------------
					$UDomainBonus = Array('YearsRemainded'=>$DomainBonus['YearsRemainded'] - $YearsRemainded);
					#-------------------------------------------------------------------------------
					$Comp = Comp_Load('Formats/Percent',$DomainBonus['Discont']);
					if(Is_Error($Comp))
						return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
					$Tr = new Tag('TR');
					#-------------------------------------------------------------------------------
					foreach(Array($YearsRemainded,$Comp) as $Text)
						$Tr->AddChild(new Tag('TD',Array('class'=>'Standard','align'=>'right'),$Text));
					#-------------------------------------------------------------------------------
					$DomainBonuses[] = $Tr;
					#-------------------------------------------------------------------------------
					$YearsRemainded = 0;
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
				$IsUpdate = DB_Update('DomainBonuses',$UDomainBonus,Array('ID'=>$DomainBonus['ID']));
				if(Is_Error($IsUpdate))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// начальная стоимость - либо ноль, либо наценка за использование не-наших ДНС серверов
	$CostPay = 0.00;
	#-------------------------------------------------------------------------------
	#Debug(SPrintF('[comp/www/DomainOrderPay]: JuridicalOnly = %s; ContractTypeID = %s',$Settings['JuridicalOnly'],$DomainOrder['ContractTypeID']));
	if($Settings['ExternalDnsMarkUp'] > 0 && (!$Settings['JuridicalOnly'] || In_Array($DomainOrder['ContractTypeID'],Array('Juridical','Individual')))){
		#-------------------------------------------------------------------------------
		// составляем список ДНС серверов, заданных в общих настройках
		$ExternalDnsList = Explode(',',$Settings['ExternalDnsList']);
		#-------------------------------------------------------------------------------
		if($DomainOrder['Params']['Ns1Name'])
			$ExternalDnsList[] = StrToLower($DomainOrder['Params']['Ns1Name']);
		#-------------------------------------------------------------------------------
		if($DomainOrder['Params']['Ns2Name'])
			$ExternalDnsList[] = StrToLower($DomainOrder['Params']['Ns2Name']);
		#-------------------------------------------------------------------------------
		if($DomainOrder['Params']['Ns3Name'])
			$ExternalDnsList[] = StrToLower($DomainOrder['Params']['Ns3Name']);
		#-------------------------------------------------------------------------------
		if($DomainOrder['Params']['Ns4Name'])
			$ExternalDnsList[] = StrToLower($DomainOrder['Params']['Ns4Name']);
		#-------------------------------------------------------------------------------
		// перебираем ДНС сервера установленные для этого домена
		foreach(Explode(',',StrToLower($DomainOrder['DNSs'])) as $DNS){
			#-------------------------------------------------------------------------------
			if(!$DNS)
				continue;
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/www/DomainOrderPay]: проверка DNS: %s',$DNS));
			#-------------------------------------------------------------------------------
			if(!In_Array($DNS,$ExternalDnsList)){
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/www/DomainOrderPay]: DNS (%s) not in list (%s)',$DNS,Implode(',',$ExternalDnsList)));
				#-------------------------------------------------------------------------------
				$CostPay = (double) $Settings['ExternalDnsMarkUp'];
				#-------------------------------------------------------------------------------
				$Message = SPrintF($Settings['ExternalDnsMessage'],$Settings['ExternalDnsMarkUp']);
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$YearsRemainded = $YearsPay;
	#-------------------------------------------------------------------------------
	while($YearsRemainded){
		#-------------------------------------------------------------------------------
		if($StatusID == 'ForTransfer'){
			#-------------------------------------------------------------------------------
			$CurrentCost = $DomainScheme['CostTransfer'];
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$CurrentCost = $DomainScheme[(!$IsPayed && $YearsPay - $YearsRemainded < $DomainScheme['MinOrderYears']?'CostOrder':'CostProlong')];
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$CostPay += $YearsRemainded*$CurrentCost;
		#-------------------------------------------------------------------------------
		$YearsRemainded = 0;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(Is_Error(DB_Roll($TransactionID)))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$CostPay = Round($CostPay,2);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Количество лет',$YearsPay);
	#-------------------------------------------------------------------------------
	if(Count($DomainBonuses)){
		#-------------------------------------------------------------------------------
		$Tr = new Tag('TR');
		#-------------------------------------------------------------------------------
		foreach(Array('Лет','Скидка') as $Text)
			$Tr->AddChild(new Tag('TD',Array('class'=>'Head'),$Text));
		#-------------------------------------------------------------------------------
		Array_UnShift($DomainBonuses,$Tr);
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Tables/Extended',$DomainBonuses,'Бонусы',Array('style'=>'100%'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = new Tag('DIV',Array('align'=>'center'),$Comp);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(IsSet($Message))
		$Table[] = new Tag('TR',new Tag('TD',Array('class'=>'Standard','colspan'=>2,'style'=>'background-color:#FDF6D3;max-width:350px;'),$Message));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Currency',$CostPay);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Всего к оплате',$Comp);
	#-------------------------------------------------------------------------------
	$Div = new Tag('DIV',Array('align'=>'right','class'=>'Standard'));
	#-------------------------------------------------------------------------------
	$Div->AddHTML(TemplateReplace('www.ServiceOrderPay',Array('ServiceCode'=>'Domain')));
	#-------------------------------------------------------------------------------
	$Table[] = $Div;
	#-------------------------------------------------------------------------------
	$Table[] = new Tag('DIV',Array('align'=>'right','style'=>'font-size:10px;'),$CostPay > $DomainOrder['ContractBalance']?'[заказ будет добавлен в корзину]':'[заказ будет оплачен с баланса договора]');
	#-------------------------------------------------------------------------------
	$Div = new Tag('DIV',Array('align'=>'right'));
	#-------------------------------------------------------------------------------
	if($IsChange){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>'WindowPrev();','value'=>'Изменить период'));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Div->AddChild($Comp);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>'DomainOrderPay();','value'=>'Продолжить'));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Div->AddChild($Comp);
	#-------------------------------------------------------------------------------
	$Table[] = $Div;
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Table = Array();
	#-------------------------------------------------------------------------------
	if($IsPayed){
		#-------------------------------------------------------------------------------
		if(!$DomainScheme['IsProlong'])
			return new gException('SCHEME_NOT_ALLOW_PROLONG','Тарифный план заказа домена не позволяет продление');
		#-------------------------------------------------------------------------------
		$DaysToProlong = $DomainScheme['DaysToProlong'];
		#-------------------------------------------------------------------------------
		if(($ExpirationDate - Time())/86400 > $DaysToProlong && $StatusID != 'ForTransfer')
			return new gException('PROLONG_IS_EARLY',SPrintF('Заказ домена может быть продлен только за %u дн. до окончания',$DaysToProlong));
		#-------------------------------------------------------------------------------
		$Options = Array();
		#-------------------------------------------------------------------------------
		if(($ExpirationDate - Time())/86400 > $DaysToProlong){
			#-------------------------------------------------------------------------------
			$YearsRemainder = Date('Y',$ExpirationDate) - Date('Y');
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$YearsRemainder = 0;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		if($YearsRemainder >= $DomainScheme['MaxActionYears'] && $StatusID != 'ForTransfer')
			return new gException('DOMAIN_ORDER_ON_MAX_YEARS_2','Доменное имя уже зарегистрировано на максимальное кол-во лет');
		#-------------------------------------------------------------------------------
		for($Years=1;$Years<=$DomainScheme['MaxActionYears'] - $YearsRemainder;$Years++)
			$Options[$Years] = $Years;
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$Options = Array();
		#-------------------------------------------------------------------------------
		for($Years=$DomainScheme['MinOrderYears'];$Years<=$DomainScheme['MaxActionYears'];$Years++)
			$Options[$Years] = $Years;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	if(Count($Options) < 2){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('www/DomainOrderPay',Array('DomainOrderID'=>$DomainOrder['ID'],'YearsPay'=>($StatusID == 'ForTransfer')?1:Current($Options)));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		return $Comp;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	# костыль для пеерноса на максимально продлённом домене
	#if(Count($Options) < 2 && $StatusID == 'ForTransfer')
	#	$Options[1] = 1;
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Select',Array('name'=>'YearsPay'),$Options);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Кол-во лет',$Comp);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>"ShowWindow('/DomainOrderPay',FormGet(form));",'value'=>'Продолжить'));
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
$Comp = Comp_Load('Form/Input',Array('type'=>'hidden','name'=>'IsChange','value'=>'true'));
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
