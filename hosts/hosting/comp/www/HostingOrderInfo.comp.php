<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
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
$HostingOrderID = (integer) @$Args['HostingOrderID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array(
			'*',
			'(SELECT `Name` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = `HostingOrdersOwners`.`SchemeID`) as `Scheme`',
			'(SELECT `Name` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = (SELECT `ServersGroupID` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = `HostingOrdersOwners`.`SchemeID`)) as `ServersGroupName`',
			'(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `HostingOrdersOwners`.`OrderID`) AS `ServerID`',
			'(SELECT `Params` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `HostingOrdersOwners`.`OrderID`) AS `Params`',
			'(SELECT `IsAutoProlong` FROM `Orders` WHERE `HostingOrdersOwners`.`OrderID`=`Orders`.`ID`) AS `IsAutoProlong`',
			'(SELECT (SELECT `Code` FROM `Services` WHERE `OrdersOwners`.`ServiceID` = `Services`.`ID`) FROM `OrdersOwners` WHERE `HostingOrdersOwners`.`OrderID` = `OrdersOwners`.`ID`) AS `Code`'
		);
#-------------------------------------------------------------------------------
$HostingOrder = DB_Select('HostingOrdersOwners',$Columns,Array('UNIQ','ID'=>$HostingOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('HostingOrdersRead',(integer)$__USER['ID'],(integer)$HostingOrder['UserID']);
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
        #-----------------------------------------------------------------------
        $DOM->AddText('Title',SPrintF('Заказ хостинга %s',$HostingOrder['Login']));
        #-----------------------------------------------------------------------
        $Table = Array('Общая информация');
        #-----------------------------------------------------------------------
        $Number = Comp_Load('Formats/Order/Number',$HostingOrder['OrderID']);
        if(Is_Error($Number))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Номер',$Number);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Date/Extended',$HostingOrder['OrderDate']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Дата заказа',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Contract/Number',$HostingOrder['ContractID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Договор №',$Comp);
        #-----------------------------------------------------------------------
        $Table[] = Array('Тарифный план',SPrintF('%s (%s)',$HostingOrder['Scheme'],$HostingOrder['ServersGroupName']));
	#-----------------------------------------------------------------------
        #-----------------------------------------------------------------------
        $Table[] = 'Параметры доступа';
        #-----------------------------------------------------------------------
	$Server = DB_Select('ServersOwners',Array('Address','Params'),Array('UNIQ','ID'=>$HostingOrder['ServerID']));
        if(!Is_Array($Server))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'type'    => 'button',
            'onclick' => SPrintF('OrderManage(%u,%u);',$HostingOrder['ID'],$HostingOrder['ServiceID']),
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
        $Table[] = Array('Логин',$HostingOrder['Login']);
        #-----------------------------------------------------------------------
        $Table[] = Array('Пароль',$HostingOrder['Password']);
        #-----------------------------------------------------------------------
        $Table[] = Array('FTP, POP3, SMTP, IMAP',$Server['Address']);
        #-----------------------------------------------------------------------
        $Table[] = 'Именные сервера';
        #-----------------------------------------------------------------------
        $Table[] = Array('Первичный сервер',$Server['Params']['Ns1Name']);
        #-----------------------------------------------------------------------
        $Table[] = Array('Вторичный сервер',$Server['Params']['Ns2Name']);
        #-----------------------------------------------------------------------
        $Ns3Name = $Server['Params']['Ns3Name'];
        #-----------------------------------------------------------------------
        if($Ns3Name)
          $Table[] = Array('Дополнительный сервер',$Ns3Name);
        #-----------------------------------------------------------------------
        $Ns4Name = $Server['Params']['Ns4Name'];
        #-----------------------------------------------------------------------
        if($Ns4Name)
          $Table[] = Array('Расширенный сервер',$Ns4Name);
        #-----------------------------------------------------------------------
        $Parked = $HostingOrder['Parked'];
        #-----------------------------------------------------------------------
        if($Parked){
          #---------------------------------------------------------------------
          $Parked = Explode(',',$Parked);
          #---------------------------------------------------------------------
          $Table[] = 'Опрос сервера';
          #---------------------------------------------------------------------
          $Table[] = Array('Паркованные домены',new Tag('PRE',Array('class'=>'Standard'),Implode("\n",$Parked)));
        }
        #-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	$Table[] = 'Прочее';
	#-----------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Logic',$HostingOrder['IsAutoProlong']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-----------------------------------------------------------------------
	$Table[] = Array('Автопродление',$Comp);
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
        $Comp = Comp_Load('Statuses/State','HostingOrders',$HostingOrder);
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
            'name'  => 'OrderID',
            'value' => $HostingOrder['OrderID']
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
            'name'  => 'HostingOrderID',
            'value' => $HostingOrder['ID']
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
