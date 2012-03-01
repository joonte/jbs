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
if(Is_Null($Args)){
	#-----------------------------------------------------------------------------
	if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
		return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$VPSOrderID = (integer) @$Args['VPSOrderID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array(
			'*',
			'(SELECT `Name` FROM `VPSSchemes` WHERE `VPSSchemes`.`ID` = `VPSOrdersOwners`.`SchemeID`) as `Scheme`',
			'(SELECT `Name` FROM `VPSServersGroups` WHERE `VPSServersGroups`.`ID` = (SELECT `ServersGroupID` FROM `VPSSchemes` WHERE `VPSSchemes`.`ID` = `VPSOrdersOwners`.`SchemeID`)) as `ServersGroupName`',
			'(SELECT `Ns1Name` FROM `VPSServers` WHERE `VPSServers`.`ID` = `VPSOrdersOwners`.`ServerID`) as `Ns1Name`',
			'(SELECT `Ns2Name` FROM `VPSServers` WHERE `VPSServers`.`ID` = `VPSOrdersOwners`.`ServerID`) as `Ns2Name`',
			'(SELECT `Ns3Name` FROM `VPSServers` WHERE `VPSServers`.`ID` = `VPSOrdersOwners`.`ServerID`) as `Ns3Name`',
			'(SELECT `Ns4Name` FROM `VPSServers` WHERE `VPSServers`.`ID` = `VPSOrdersOwners`.`ServerID`) as `Ns4Name`',
			'(SELECT `IsAutoProlong` FROM `Orders` WHERE `VPSOrdersOwners`.`OrderID`=`Orders`.`ID`) AS `IsAutoProlong`',
			'(SELECT (SELECT `Code` FROM `Services` WHERE `Orders`.`ServiceID` = `Services`.`ID`) FROM `Orders` WHERE `VPSOrdersOwners`.`OrderID` = `Orders`.`ID`) AS `Code`'
		);
#-------------------------------------------------------------------------------
$VPSOrder = DB_Select('VPSOrdersOwners',$Columns,Array('UNIQ','ID'=>$VPSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('VPSOrdersRead',(integer)$__USER['ID'],(integer)$VPSOrder['UserID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsPermission)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'false':
        return ERROR | @Trigger_Error(700);
      case 'true':
        #-----------------------------------------------------------------------
        $DOM = new DOM();
        #-----------------------------------------------------------------------
        $Links = &Links();
        # Коллекция ссылок
        $Links['DOM'] = &$DOM;
        #-----------------------------------------------------------------------
        if(Is_Error($DOM->Load('Window')))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $DOM->AddText('Title','Заказ виртуального сервера');
        #-----------------------------------------------------------------------
        $Table = Array('Общая информация');
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Order/Number',$VPSOrder['OrderID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Номер',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Date/Extended',$VPSOrder['OrderDate']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Дата заказа',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Contract/Number',$VPSOrder['ContractID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Договор №',$Comp);
        #-----------------------------------------------------------------------
        $Table[] = Array('Тарифный план',SPrintF('%s (%s)',$VPSOrder['Scheme'],$VPSOrder['ServersGroupName']));
        #-----------------------------------------------------------------------
        #-----------------------------------------------------------------------
	$Table = Comp_Load('OrderConsiderInfo',Array('OrderID'=>$VPSOrder['OrderID'],'Table'=>$Table,'Code'=>$VPSOrder['Code'],'UserID'=>$VPSOrder['UserID']));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-----------------------------------------------------------------------
        #-----------------------------------------------------------------------
        $Table[] = 'Параметры доступа';
        #-----------------------------------------------------------------------
        $Server = DB_Select('VPSServers',Array('Url','Address'),Array('UNIQ','ID'=>$VPSOrder['ServerID']));
        if(!Is_Array($Server))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'type'    => 'button',
            'onclick' => SPrintF('VPSManage(%u);',$VPSOrder['ID']),
            'value'   => 'Вход'
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Div = new Tag('DIV',new Tag('SPAN',Array('class'=>'Standard'),$Server['Url']),$Comp);
        #-----------------------------------------------------------------------
        $Table[] = Array('Адрес панели управления',$Div);
        #-----------------------------------------------------------------------
        $Table[] = Array('Логин',$VPSOrder['Login']);
        #-----------------------------------------------------------------------
        $Table[] = Array('Пароль',$VPSOrder['Password']);
        #-----------------------------------------------------------------------
        $Table[] = Array('FTP, POP3, SMTP, IMAP',$Server['Address']);
        #-----------------------------------------------------------------------
        $Table[] = 'Именные сервера';
        #-----------------------------------------------------------------------
        $Table[] = Array('Первичный сервер',$VPSOrder['Ns1Name']);
        #-----------------------------------------------------------------------
        $Table[] = Array('Вторичный сервер',$VPSOrder['Ns2Name']);
        #-----------------------------------------------------------------------
        $Ns3Name = $VPSOrder['Ns3Name'];
        #-----------------------------------------------------------------------
        if($Ns3Name)
          $Table[] = Array('Дополнительный сервер',$Ns3Name);
        #-----------------------------------------------------------------------
        $Ns4Name = $VPSOrder['Ns4Name'];
        #-----------------------------------------------------------------------
        if($Ns4Name)
          $Table[] = Array('Расширенный сервер',$Ns4Name);
        #-----------------------------------------------------------------------
#        $Parked = $VPSOrder['Parked'];
#        #-----------------------------------------------------------------------
#        if($Parked){
#          #---------------------------------------------------------------------
#          $Parked = Explode(',',$Parked);
#          #---------------------------------------------------------------------
#          $Table[] = 'Опрос сервера';
#          #---------------------------------------------------------------------
#          $Table[] = Array('Паркованные домены',new Tag('PRE',Array('class'=>'Standard'),Implode("\n",$Parked)));
#        }
        #-----------------------------------------------------------------------
	$Table[] = 'Прочее';
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	if($VPSOrder['IsAutoProlong']){
		$Button = "Отключить";
		$msg = "[включено]";
	}else{
		$Button = "Включить";
		$msg = "[выключено]";
	}
	#-----------------------------------------------------------------------
	$Params = Array('type'=>'hidden','name'=>'IsAutoProlong','value'=>$VPSOrder['IsAutoProlong']?'0':'1');
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
	#-----------------------------------------------------------------------
        $Comp = Comp_Load('Statuses/State','VPSOrders',$VPSOrder);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table = Array_Merge($Table,$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Tables/Standard',$Table);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form = new Tag('FORM',Array('method'=>'POST'),$Comp);
	#-----------------------------------------------------------------------
	$Form->AddChild($IsAutoProlong);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'type'  => 'hidden',
            'name'  => 'VPSOrderID',
            'value' => $VPSOrder['ID']
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form->AddChild($Comp);
        #-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'type'  => 'hidden',
            'name'  => 'OrderID',
            'value' => $VPSOrder['OrderID']
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form->AddChild($Comp);
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
        $DOM->AddChild('Into',$Form);
        #-----------------------------------------------------------------------
        if(Is_Error($DOM->Build(FALSE)))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        return Array('Status'=>'Ok','DOM'=>$DOM->Object);
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
