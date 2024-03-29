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
$DomainOrderID = (integer) @$Args['DomainOrderID'];
$Ns1Name       =  (string) @$Args['Ns1Name'];
$Ns1IP         =  (string) @$Args['Ns1IP'];
$Ns2Name       =  (string) @$Args['Ns2Name'];
$Ns2IP         =  (string) @$Args['Ns2IP'];
$Ns3Name       =  (string) @$Args['Ns3Name'];
$Ns3IP         =  (string) @$Args['Ns3IP'];
$Ns4Name       =  (string) @$Args['Ns4Name'];
$Ns4IP         =  (string) @$Args['Ns4IP'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Ns1Name)
	if(Mb_StrToLower($Ns1Name,'UTF-8') == Mb_StrToLower($Ns2Name,'UTF-8'))
		return new gException('DNS_SERVERS_CANNOT_BE_EQUAL','Имена DNS серверов должны быть разными');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Ns1IP)
	if($Ns1IP == $Ns2IP)
		return new gException('IP_DNS_SERVERS_CANNOT_BE_EQUAL','IP адреса DNS серверов должны быть разными');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Ns1Name || !$Ns2Name)
	return new gException('NEED_MORE_ONE_SERVER','Должны быть указаны два первых сервера, как минимум');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DomainOrder = DB_Select('DomainOrdersOwners','*',Array('UNIQ','ID'=>$DomainOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('DOMAIN_ORDER_NOT_FOUND','Выбранный заказ не найден');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('DomainOrdersNsChange',(integer)$__USER['ID'],(integer)$DomainOrder['UserID']);
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
$DomainOrderID = (integer)$DomainOrder['ID'];
#-------------------------------------------------------------------------------
if($DomainOrder['StatusID'] != 'Active')
	return new gException('ORDER_IS_NOT_ACTIVE','Смена именных серверов не доступна');
#-------------------------------------------------------------------------------
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
$Regulars = Regulars();
#-------------------------------------------------------------------------------
$Domain = SPrintF('%s.%s',$DomainOrder['DomainName'],$DomainScheme['Name']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Ns1Name = Trim(Mb_StrToLower($Ns1Name,'UTF-8'),'.');
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['DnsDomain'],$Ns1Name))
	return new gException('WRONG_NAME_NS1','Неверное имя первого сервера имен');
#-------------------------------------------------------------------------------
if(Mb_SubStr($Ns1Name,-Mb_StrLen($Domain)) == $Domain){
	#-------------------------------------------------------------------------------
	if(!Preg_Match($Regulars['IP'],$Ns1IP))
		if(!Preg_Match($Regulars['IPv6'],$Ns1IP))
			return new gException('WRONG_IP_NS1','Неверный IP адрес первого сервера имен');
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	if($Ns1IP)
		$Ns1IP = '';
	#-------------------------------------------------------------------------------
	#return new gException('IP_NS1_CAN_NOT_FILL','IP адрес первого сервера имен не может быть указан');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Ns2Name = Trim(Mb_StrToLower($Ns2Name,'UTF-8'),'.');
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['DnsDomain'],$Ns1Name))
	return new gException('WRONG_NAME_NS2','Неверное имя второго сервера имен');
#-------------------------------------------------------------------------------
if(Mb_SubStr($Ns2Name,-Mb_StrLen($Domain)) == $Domain){
	#-------------------------------------------------------------------------------
	if(!Preg_Match($Regulars['IP'],$Ns2IP))
		if(!Preg_Match($Regulars['IPv6'],$Ns2IP))
			return new gException('WRONG_IP_NS2','Неверный IP адрес второго сервера имен');
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	if($Ns2IP)
		$Ns2IP = '';
	#-------------------------------------------------------------------------------
	#return new gException('IP_NS2_CAN_NOT_FILL','IP адрес второго сервера имен не может быть указан');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Ns3Name = Trim(Mb_StrToLower($Ns3Name,'UTF-8'),'.');
#-------------------------------------------------------------------------------
if($Ns3Name){
	#-------------------------------------------------------------------------------
	if(!Preg_Match($Regulars['DnsDomain'],$Ns3Name))
		return new gException('WRONG_NAME_NS3','Неверное имя дополнительного сервера имен');
	#-------------------------------------------------------------------------------
	if(Mb_SubStr($Ns3Name,-Mb_StrLen($Domain)) == $Domain){
		#-------------------------------------------------------------------------------
		if(!Preg_Match($Regulars['IP'],$Ns3IP))
			if(!Preg_Match($Regulars['IPv6'],$Ns3IP))
				return new gException('WRONG_IP_NS3','Неверный IP адрес дополнительного сервера имен');
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		if($Ns3IP)
			$Ns3IP = '';
		#-------------------------------------------------------------------------------
		#return new gException('IP_NS3_CAN_NOT_FILL','IP адрес дополнительного сервера имен не может быть указан');
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	if($Ns3IP)
		$Ns3IP = '';
	#-------------------------------------------------------------------------------
	#return new gException('NAME_NS3_NOT_FILL','Укажите имя дополнительного сервера имен');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Ns4Name = Trim(Mb_StrToLower($Ns4Name,'UTF-8'),'.');
#-------------------------------------------------------------------------------
if($Ns4Name){
	#-------------------------------------------------------------------------------
	if(!Preg_Match($Regulars['DnsDomain'],$Ns4Name))
		return new gException('WRONG_NAME_NS4','Неверное имя расширенного сервера имен');
	#-------------------------------------------------------------------------------
	if(Mb_SubStr($Ns4Name,-Mb_StrLen($Domain)) == $Domain){
		#-------------------------------------------------------------------------------
		if(!Preg_Match($Regulars['IP'],$Ns4IP))
			if(!Preg_Match($Regulars['IPv6'],$Ns4IP))
				return new gException('WRONG_IP_NS4','Неверный IP адрес расширенного сервера имен');
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		if($Ns4IP)
			$Ns4IP = '';
		#-------------------------------------------------------------------------------
		#return new gException('IP_NS4_CAN_NOT_FILL','IP адрес расширенного сервера имен не может быть указан');
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	if($Ns4IP)
		$Ns4IP = '';
	#-------------------------------------------------------------------------------
	#return new gException('NAME_NS4_NOT_FILL','Укажите имя расширенного сервера имен');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Mb_StrToLower($Ns1Name,'UTF-8') == $Domain || Mb_StrToLower($Ns2Name,'UTF-8') == $Domain || Mb_StrToLower($Ns3Name,'UTF-8') == $Domain || Mb_StrToLower($Ns4Name,'UTF-8') == $Domain)
	return new gException('NS_HOSTNAME_CANT_BE_EQUAL_TO_DOMAIN','Имя DNS сервера не может совпадать с именем домена');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Implode(';',Array($DomainOrder['Ns1Name'],$DomainOrder['Ns1IP'],$DomainOrder['Ns2Name'],$DomainOrder['Ns2IP'],$DomainOrder['Ns3Name'],$DomainOrder['Ns3IP'],$DomainOrder['Ns4Name'],$DomainOrder['Ns4IP'])) == Implode(';',Array($Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP,$Ns3Name,$Ns3IP,$Ns4Name,$Ns4IP)))
	return new gException('NO_DATA_FOR_CHANGE','Нет данных для изменения');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$UDomainOrder = Array('Ns1Name'=>$Ns1Name,'Ns1IP'=>$Ns1IP,'Ns2Name'=>$Ns2Name,'Ns2IP'=>$Ns2IP,'Ns3Name'=>$Ns3Name,'Ns3IP'=>$Ns3IP,'Ns4Name'=>$Ns4Name,'Ns4IP'=>$Ns4IP);
#-------------------------------------------------------------------------------
$IsUpdate = DB_Update('DomainOrders',$UDomainOrder,Array('ID'=>$DomainOrderID));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$DomainOrder['UserID'],'TypeID'=>'DomainNsChange','Params'=>Array($DomainOrderID,$DomainOrder['Ns1Name'],$DomainOrder['Ns1IP'],$DomainOrder['Ns2Name'],$DomainOrder['Ns2IP'],$DomainOrder['Ns3Name'],$DomainOrder['Ns3IP'],$DomainOrder['Ns4Name'],$DomainOrder['Ns4IP'])));
#-------------------------------------------------------------------------------
switch(ValueOf($Comp)){
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
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainOrders','StatusID'=>'ForNsChange','RowsIDs'=>$DomainOrderID,'Comment'=>'Поступила заявка на изменение именных серверов'));
#-------------------------------------------------------------------------------
switch(ValueOf($Comp)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	return Array('Status'=>'Ok','DomainOrderID'=>$DomainOrderID,'OrderID'=>$DomainOrder['OrderID']);
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
