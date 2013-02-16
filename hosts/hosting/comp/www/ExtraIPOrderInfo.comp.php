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
$ExtraIPOrderID = (integer) @$Args['ExtraIPOrderID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array(
			'*',
			'(SELECT `Name` FROM `ExtraIPSchemes` WHERE `ExtraIPSchemes`.`ID` = `ExtraIPOrdersOwners`.`SchemeID`) as `Scheme`',
			'(SELECT `IsAutoProlong` FROM `Orders` WHERE `ExtraIPOrdersOwners`.`OrderID`=`Orders`.`ID`) AS `IsAutoProlong`',
			'(SELECT (SELECT `Code` FROM `Services` WHERE `Orders`.`ServiceID` = `Services`.`ID`) FROM `Orders` WHERE `ExtraIPOrdersOwners`.`OrderID` = `Orders`.`ID`) AS `Code`'
		);
#-------------------------------------------------------------------------------
$ExtraIPOrder = DB_Select('ExtraIPOrdersOwners',$Columns,Array('UNIQ','ID'=>$ExtraIPOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    # Select Depend Order Info
    if($ExtraIPOrder['OrderType'] == "Hosting" || $ExtraIPOrder['OrderType'] == "VPS"){
	$Columns = Array('*','(SELECT `Address` FROM `' . $ExtraIPOrder['OrderType'] . 'Servers` WHERE `' . $ExtraIPOrder['OrderType'] . 'OrdersOwners`.`ServerID` = `' . $ExtraIPOrder['OrderType'] . 'Servers`.`ID`) AS Address');
	$ExtraIPDepend = DB_Select($ExtraIPOrder['OrderType'] . 'OrdersOwners',$Columns,Array('UNIQ','ID'=>$ExtraIPOrder['DependOrderID']));
	switch(ValueOf($ExtraIPDepend)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
    }else{
   	$ExtraIPDepend = Array(
		'Login'		=> '',
		'Address'	=> ''
	);
    }
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('ExtraIPOrdersRead',(integer)$__USER['ID'],(integer)$ExtraIPOrder['UserID']);
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
        $DOM->AddText('Title','Заказ выделенного IP адреса');
        #-----------------------------------------------------------------------
        $Table = Array('Общая информация');
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Order/Number',$ExtraIPOrder['OrderID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Номер',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Date/Extended',$ExtraIPOrder['OrderDate']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Дата заказа',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Contract/Number',$ExtraIPOrder['ContractID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Договор №',$Comp);
        #-----------------------------------------------------------------------
        #-----------------------------------------------------------------------
        $Table[] = 'Информация';
        #-----------------------------------------------------------------------
        #-----------------------------------------------------------------------
        $Table[] = Array('IP адрес',$ExtraIPOrder['Login']);
	#-----------------------------------------------------------------------
	if($ExtraIPOrder['OrderType'] == "Hosting"){
		$OrderType = "Хостинг";
	}elseif($ExtraIPOrder['OrderType'] == "VPS"){
		$OrderType = $ExtraIPOrder['OrderType'];
	}elseif($ExtraIPOrder['OrderType'] == "DS"){
		$OrderType = "Выделенный сервер";
	}elseif($ExtraIPOrder['OrderType'] == "Manual"){
		$OrderType = "Без сервисов";
	}else{
		return new gException('UNKNOWN_IP_DESTINATION','Не удалось определить с каким сервисом связан IP адрес.');
	}
	$Table[] = Array('Тип заказа',$OrderType);
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	$Table[] = Array('Сервер',$ExtraIPDepend['Address']);
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	$Table[] = Array('Аккаунт',$ExtraIPDepend['Login']);

#        #-----------------------------------------------------------------------
#        $Table[] = Array('FTP, POP3, SMTP, IMAP',$Server['Address']);
#        #-----------------------------------------------------------------------
#        $Table[] = 'Именные сервера';
#        #-----------------------------------------------------------------------
#        $Table[] = Array('Первичный сервер',$ExtraIPOrder['Ns1Name']);
#        #-----------------------------------------------------------------------
#        $Table[] = Array('Вторичный сервер',$ExtraIPOrder['Ns2Name']);
#        #-----------------------------------------------------------------------
#        $Ns3Name = $ExtraIPOrder['Ns3Name'];
#        #-----------------------------------------------------------------------
#        if($Ns3Name)
#          $Table[] = Array('Дополнительный сервер',$Ns3Name);
#        #-----------------------------------------------------------------------
#        $Ns4Name = $ExtraIPOrder['Ns4Name'];
#        #-----------------------------------------------------------------------
#        if($Ns4Name)
#          $Table[] = Array('Расширенный сервер',$Ns4Name);
#        #-----------------------------------------------------------------------
#        $Parked = $ExtraIPOrder['Parked'];
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
	if($ExtraIPOrder['IsAutoProlong']){
		$Button = "Отключить";
		$msg = "[включено]";
	}else{
		$Button = "Включить";
		$msg = "[выключено]";
	}
	#------------------------------------------------------------------------
	$Params = Array('type'=>'hidden','name'=>'IsAutoProlong','value'=>$ExtraIPOrder['IsAutoProlong']?'0':'1');
	$IsAutoProlong = Comp_Load('Form/Input',$Params);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#------------------------------------------------------------------------
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
	#-----------------------------------------------------------------------
        $Comp = Comp_Load('Statuses/State','ExtraIPOrders',$ExtraIPOrder);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table = Array_Merge($Table,$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Tables/Standard',$Table);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form = new Tag('FORM',Array('method'=>'POST','name'=>'OrderInfo'),$Comp);
	#-----------------------------------------------------------------------
	$Form->AddChild($IsAutoProlong);
        #-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'type'  => 'hidden',
            'name'  => 'ExtraIPOrderID',
            'value' => $ExtraIPOrder['ID']
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
            'value' => $ExtraIPOrder['OrderID']
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
