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
$DNSmanagerOrderID = (integer) @$Args['DNSmanagerOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/Tree.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','SchemeID','(SELECT `ServersGroupID` FROM `Servers` WHERE `Servers`.`ID` = (SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DNSmanagerOrdersOwners`.`OrderID`)) AS `ServersGroupID`','(SELECT `Params` FROM `Servers` WHERE `Servers`.`ID` = (SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DNSmanagerOrdersOwners`.`OrderID`)) AS `Params`','StatusID');
#-------------------------------------------------------------------------------
$DNSmanagerOrder = DB_Select('DNSmanagerOrdersOwners',$Columns,Array('UNIQ','ID'=>$DNSmanagerOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DNSmanagerOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#-------------------------------------------------------------------------------
	$__USER = $GLOBALS['__USER'];
	#-------------------------------------------------------------------------------
	$IsPermission = Permission_Check('DNSmanagerOrdersRead',(integer)$__USER['ID'],(integer)$DNSmanagerOrder['UserID']);
	#-------------------------------------------------------------------------------
	switch(ValueOf($IsPermission)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'false':
		return ERROR | @Trigger_Error(700);
	case 'true':
		#-------------------------------------------------------------------------------
		if(!In_Array($DNSmanagerOrder['StatusID'],Array('Active','Suspended')))
			return new gException('ORDER_NOT_ACTIVE','Тариф можно изменить только для активного или заблокированного заказа');
		#-------------------------------------------------------------------------------
		$OldScheme = DB_Select('DNSmanagerSchemes',Array('IsSchemeChange','IsReselling'),Array('UNIQ','ID'=>$DNSmanagerOrder['SchemeID']));
		#-------------------------------------------------------------------------------
		switch(ValueOf($OldScheme)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			#-------------------------------------------------------------------------------
			if(!$OldScheme['IsSchemeChange'])
				return new gException('SCHEME_NOT_ALLOW_SCHEME_CHANGE','Тарифный план заказа хостинга не позволяет смену тарифа');
			#-------------------------------------------------------------------------------
			$__USER = $GLOBALS['__USER'];
			#-------------------------------------------------------------------------------
			$UniqID = UniqID('DNSmanagerSchemes');
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Services/Schemes','DNSmanagerSchemes',$DNSmanagerOrder['UserID'],Array('Name','ServersGroupID'),$UniqID);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Where = SPrintF("`ServersGroupID` = %u /*AND `ID` != %u*/ AND `IsActive` = 'yes' AND `IsSchemeChangeable` = 'yes' AND `IsReselling` = '%s'",$DNSmanagerOrder['ServersGroupID'],$DNSmanagerOrder['SchemeID'],$OldScheme['IsReselling']?'yes':'no');
			#-------------------------------------------------------------------------------
			$DNSmanagerSchemes = DB_Select($UniqID,Array('ID','Name'),Array('SortOn'=>'SortID','Where'=>$Where));
			#-------------------------------------------------------------------------------
			switch(ValueOf($DNSmanagerSchemes)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return new gException('HOSTING_SCHEMES_NOT_FOUND','Нет тарифов для смены');
			case 'array':
				#-------------------------------------------------------------------------------
				if(SizeOf($DNSmanagerSchemes) == 1)
					return new gException('HOSTING_SCHEMES_NOT_FOUND','Нет тарифов для смены');
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
				$DOM->AddText('Title','Смена тарифного плана');
				#-------------------------------------------------------------------------------
				$Table = $Options = Array();
				#-------------------------------------------------------------------------------
				foreach($DNSmanagerSchemes as $DNSmanagerScheme)
					$Options[$DNSmanagerScheme['ID']] = $DNSmanagerScheme['Name'];
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load('Form/Select',Array('name'=>'NewSchemeID'),$Options,NULL,$DNSmanagerOrder['SchemeID']);
				if(Is_Error($Comp))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Table[] = Array('Новый тарифный план',$Comp);
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load(
							'Form/Input',
							Array(
								'type'		=> 'button',
								'onclick'	=> "FormEdit('/API/DNSmanagerOrderSchemeChange','DNSmanagerOrderSchemeChangeForm','Смена тарифного плана');",
								'value'		=> 'Сменить'
								)
						);
				#-------------------------------------------------------------------------------
				if(Is_Error($Comp))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Table[] = $Comp;
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load('Tables/Standard',$Table);
				if(Is_Error($Comp))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Form = new Tag('FORM',Array('name'=>'DNSmanagerOrderSchemeChangeForm','onsubmit'=>'return false;'),$Comp);
				#-------------------------------------------------------------------------------
				$Comp = Comp_Load(
							'Form/Input',
							Array(
								'name'		=> 'DNSmanagerOrderID',
								'type'		=> 'hidden',
								'value'		=> $DNSmanagerOrder['ID']
								)
						);
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
				return Array('Status'=>'Ok','DOM'=>$DOM->Object);
				#-------------------------------------------------------------------------------
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
