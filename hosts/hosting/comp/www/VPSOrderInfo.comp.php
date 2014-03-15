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
			'(SELECT `Name` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = (SELECT `ServersGroupID` FROM `VPSSchemes` WHERE `VPSSchemes`.`ID` = `VPSOrdersOwners`.`SchemeID`)) as `ServersGroupName`',
			'(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `VPSOrdersOwners`.`OrderID`) AS `ServerID`',
			'(SELECT `Params` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `VPSOrdersOwners`.`OrderID`) AS `Params`',
			'(SELECT `IsAutoProlong` FROM `OrdersOwners` WHERE `VPSOrdersOwners`.`OrderID`=`OrdersOwners`.`ID`) AS `IsAutoProlong`',
			'(SELECT (SELECT `Code` FROM `Services` WHERE `OrdersOwners`.`ServiceID` = `Services`.`ID`) FROM `OrdersOwners` WHERE `VPSOrdersOwners`.`OrderID` = `OrdersOwners`.`ID`) AS `Code`'
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
        $Table[] = 'Параметры доступа';
        #-----------------------------------------------------------------------
        $Server = DB_Select('ServersOwners',Array('Address','Params'),Array('UNIQ','ID'=>$VPSOrder['ServerID']));
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
        $Div = new Tag('DIV',new Tag('SPAN',Array('class'=>'Standard'),$Server['Params']['Url']),$Comp);
        #-----------------------------------------------------------------------
        $Table[] = Array('Адрес панели управления',$Div);
        #-----------------------------------------------------------------------
        $Table[] = Array('Логин',$VPSOrder['Login']);
        #-----------------------------------------------------------------------
        $Table[] = Array('Пароль',$VPSOrder['Password']);
        #-----------------------------------------------------------------------
        $Table[] = Array('FTP, POP3, SMTP, IMAP',$VPSOrder['Login']);
        #-----------------------------------------------------------------------
        $Table[] = 'Именные сервера';
        #-----------------------------------------------------------------------
        $Table[] = Array('Первичный сервер',$Server['Params']['Ns1Name']);
        #-----------------------------------------------------------------------
        $Table[] = Array('Вторичный сервер',$Server['Params']['Ns2Name']);
        #-----------------------------------------------------------------------
        if($Server['Params']['Ns3Name'])
          $Table[] = Array('Дополнительный сервер',$Server['Params']['Ns3Name']);
        #-----------------------------------------------------------------------
        if($Server['Params']['Ns4Name'])
          $Table[] = Array('Расширенный сервер',$Server['Params']['Ns4Name']);
        #-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	$Table[] = 'Прочее';
	#-----------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Logic',$VPSOrder['IsAutoProlong']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-----------------------------------------------------------------------
	$Table[] = Array('Автопродление',$Comp);
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	$Table[] = Array('Дисковый шаблон',IsSet($VPSOrder['Params']['DiskTemplate'])?$VPSOrder['Params']['DiskTemplate']:'по умолчанию');
	#-----------------------------------------------------------------------
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
        $Form = new Tag('FORM',Array('method'=>'POST','name'=>'OrderInfo'),$Comp);
	#-----------------------------------------------------------------------
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
