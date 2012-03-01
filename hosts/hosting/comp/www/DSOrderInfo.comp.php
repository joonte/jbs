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
$DSOrderID = (integer) @$Args['DSOrderID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array(
			'*',
			'(SELECT `Name` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`) as `Scheme`',
			'(SELECT `Name` FROM `DSServersGroups` WHERE `DSServersGroups`.`ID` = (SELECT `ServersGroupID` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrdersOwners`.`SchemeID`)) as `ServersGroupName`',
			'(SELECT `IsAutoProlong` FROM `Orders` WHERE `DSOrdersOwners`.`OrderID`=`Orders`.`ID`) AS `IsAutoProlong`',
			'(SELECT (SELECT `Code` FROM `Services` WHERE `Orders`.`ServiceID` = `Services`.`ID`) FROM `Orders` WHERE `DSOrdersOwners`.`OrderID` = `Orders`.`ID`) AS `Code`'
		);
#-------------------------------------------------------------------------------
$DSOrder = DB_Select('DSOrdersOwners',$Columns,Array('UNIQ','ID'=>$DSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DSOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('DSOrdersRead',(integer)$__USER['ID'],(integer)$DSOrder['UserID']);
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
        $DOM->AddText('Title','Заказ выделенного сервера');
        #-----------------------------------------------------------------------
        $Table = Array('Общая информация');
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Order/Number',$DSOrder['OrderID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Номер',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Date/Extended',$DSOrder['OrderDate']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Дата заказа',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Contract/Number',$DSOrder['ContractID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Договор №',$Comp);
        #-----------------------------------------------------------------------
        $Table[] = Array('Тарифный план',SPrintF('%s (%s)',$DSOrder['Scheme'],$DSOrder['ServersGroupName']));
        #-----------------------------------------------------------------------
        #-----------------------------------------------------------------------
	$Table = Comp_Load('OrderConsiderInfo',Array('OrderID'=>$DSOrder['OrderID'],'Table'=>$Table,'Code'=>$DSOrder['Code'],'UserID'=>$DSOrder['UserID']));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        #-----------------------------------------------------------------------
         #-----------------------------------------------------------------------
	 $Table[] = 'IP адреса';
	 $Table[] = Array('Первичный IP адрес',$DSOrder['IP']);
	 $Table[] = Array('Дополнительные IP адреса', new Tag('PRE',$DSOrder['ExtraIP']));
#        #-----------------------------------------------------------------------
#        $Table[] = 'Параметры доступа';
#        #-----------------------------------------------------------------------
#        $Server = DB_Select('DSServers',Array('Url','Address'),Array('UNIQ','ID'=>$DSOrder['ServerID']));
#        if(!Is_Array($Server))
#          return ERROR | @Trigger_Error(500);
#        #-----------------------------------------------------------------------
#        $Comp = Comp_Load(
#          'Form/Input',
#          Array(
#            'type'    => 'button',
#            'onclick' => SPrintF('DSManage(%u);',$DSOrder['ID']),
#            'value'   => 'Вход'
#          )
#        );
#        if(Is_Error($Comp))
#          return ERROR | @Trigger_Error(500);
#        #-----------------------------------------------------------------------
#        $Div = new Tag('DIV',new Tag('SPAN',Array('class'=>'Standard'),$Server['Url']),$Comp);
#        #-----------------------------------------------------------------------
#        $Table[] = Array('Адрес панели управления',$Div);
#        #-----------------------------------------------------------------------
#        $Table[] = Array('Логин',$DSOrder['Login']);
#        #-----------------------------------------------------------------------
#        $Table[] = Array('Пароль',$DSOrder['Password']);
#        #-----------------------------------------------------------------------
#        $Table[] = Array('FTP, POP3, SMTP, IMAP',$Server['Address']);
#        #-----------------------------------------------------------------------
#        $Table[] = 'Именные сервера';
#        #-----------------------------------------------------------------------
#        $Table[] = Array('Первичный сервер',$DSOrder['Ns1Name']);
#        #-----------------------------------------------------------------------
#        $Table[] = Array('Вторичный сервер',$DSOrder['Ns2Name']);
#        #-----------------------------------------------------------------------
#        $Ns3Name = $DSOrder['Ns3Name'];
#        #-----------------------------------------------------------------------
#        if($Ns3Name)
#          $Table[] = Array('Дополнительный сервер',$Ns3Name);
#        #-----------------------------------------------------------------------
#        $Ns4Name = $DSOrder['Ns4Name'];
#        #-----------------------------------------------------------------------
#        if($Ns4Name)
#          $Table[] = Array('Расширенный сервер',$Ns4Name);
#        #-----------------------------------------------------------------------
#        $Parked = $DSOrder['Parked'];
#        #-----------------------------------------------------------------------
#        if($Parked){
#          #---------------------------------------------------------------------
#          $Parked = Explode(',',$Parked);
#          #---------------------------------------------------------------------
#          $Table[] = 'Опрос сервера';
#          #---------------------------------------------------------------------
#          $Table[] = Array('Паркованные домены',new Tag('PRE',Array('class'=>'Standard'),Implode("\n",$Parked)));
#        }
        #------------------------------------------------------------------------
	$Table[] = 'Прочее';
	#------------------------------------------------------------------------
	#------------------------------------------------------------------------
	if($DSOrder['IsAutoProlong']){
		$Button = "Отключить";
		$msg = "[включено]";
	}else{
		$Button = "Включить";
		$msg = "[выключено]";
	}
	#------------------------------------------------------------------------
	$Params = Array('type'=>'hidden','name'=>'IsAutoProlong','value'=>$DSOrder['IsAutoProlong']?'0':'1');
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
        $Comp = Comp_Load('Statuses/State','DSOrders',$DSOrder);
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
            'name'  => 'DSOrderID',
            'value' => $DSOrder['ID']
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
            'value' => $DSOrder['OrderID']
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
