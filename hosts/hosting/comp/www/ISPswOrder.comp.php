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
$ContractID	=  (string) @$Args['ContractID'];
$ISPswSchemeID	= (integer) @$Args['ISPswSchemeID'];
$StepID		= (integer) @$Args['StepID'];
$VPSOrderID	= (integer) @$Args['VPSOrderID'];
$DSOrderID	= (integer) @$Args['DSOrderID'];
$OrderType	=  (string) @$Args['OrderType'];	# тип заказа к которому цепляем лицензию
$DependOrderID	= (integer) @$Args['DependOrderID'];	# номер заказа к которому цепляем лицензию
$IP		=  (string) @$Args['IP'];		# IP адрес на который заказывается внешняя лицензия
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
$UniqID = UniqID('ISPswSchemes');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# узнаём, есть ли возможность заказа внешних лицензий
$UniqID2 = $UniqID . '_2';
$Comp = Comp_Load('Services/Schemes','ISPswSchemes',$__USER['ID'],Array('Name'),$UniqID2);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------
$Columns = Array('ID','Name','Comment','CostMonth');
#-------------------------------------------------------------------------
$ISPswSchemes = DB_Select($UniqID2,$Columns,Array('SortOn'=>Array('SortID'),'Where'=>"`IsActive` = 'yes' AND `IsInternal` = 'no'"));
#-------------------------------------------------------------------------
switch(ValueOf($ISPswSchemes)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	break;
case 'array':
	$AllowExternalOrder = 1;
	break;
default:
	return ERROR | @Trigger_Error(101);
}
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
$DOM->AddText('Title','Заказ лицензии ISPsystem');
#-------------------------------------------------------------------------------
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/ISPswOrder.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'ISPswOrderForm','onsubmit'=>'return false;'));
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
if($StepID){

Debug("[comp/www/ISPswOrder]: StepID = $StepID");

# intermediate step
if($StepID == 1){

$Table[] = new Tag('TD',Array('colspan'=>2,'width'=>300,'class'=>'Standard','style'=>'background-color:#FDF6D3;'),'Необходимо выбрать заказ VPS или выделенного сервера, к которому будет прикреплена заказанная лицензия. Обратите внимание, что нужно выбрать что-то одно - одну лицензию нельзя прикрепить к нескольким услугам.');
$OrderCount = 0;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# create select, using ContractID for VPSOrders
$Columns = Array('ID','Login','(SELECT `Address` FROM `VPSServers` WHERE `VPSServers`.`ID` = `ServerID`) as `Address`');
$VPSOrders = DB_Select('VPSOrdersOwners',$Columns,Array('Where'=>'`ContractID` = ' . $ContractID . " AND `StatusID` = 'Active'"));
switch(ValueOf($VPSOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	$Options = Array('Не использовать');
	foreach($VPSOrders as $VPSOrder){
		$VPSOrderID = $VPSOrder['ID'];
		$Options[$VPSOrderID] = SPrintF('%s [%s]',$VPSOrder['Login'],$VPSOrder['Address']);
		$OrderCount++;
	}
	$Comp = Comp_Load('Form/Select',Array('name'=>'VPSOrderID'),$Options);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	$Table[] = Array('Заказ виртуального сервера',$Comp);
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# create select, using ContractID for DSOrders
$Columns = Array('ID','IP','(SELECT `Name` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `SchemeID`) as `Name`');
$DSOrders = DB_Select('DSOrdersOwners',$Columns,Array('Where'=>'`ContractID` = ' . $ContractID . " AND `StatusID` = 'Active'"));
switch(ValueOf($DSOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	$Options = Array('Не использовать');
	foreach($DSOrders as $DSOrder){
		$DSOrderID = $DSOrder['ID'];
		$Options[$DSOrderID] = SPrintF('%s [%s]',$DSOrder['IP'],$DSOrder['Name']);
		$OrderCount++;
	}
	$Comp = Comp_Load('Form/Select',Array('name'=>'DSOrderID'),$Options);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	$Table[] = Array('Заказ выделенного сервера',$Comp);
	break;
default:
	return ERROR | @Trigger_Error(101);
}

# check - have it Owner some orders or not
if($OrderCount < 1){
#	# проверяем, может заказы есть но они не активны
#	$Count = DB_Count('VPSOrdersOwners',Array('Where'=>"`ContractID` = " . $ContractID . " AND `StatusID` != 'Active'"));
#	if(Is_Error($Count))
#		return ERROR | @Trigger_Error(500);
#	#---------------------------------------------------------------------------
#	if($Count)
#		return new gException('ISPsw_OWNER_HAVE_INACTIVE_VPS_ORDER','Заказанный вами виртуальный сервер неактивен. Необходимо его оплатить, или, если он уже оплачен, дождаться активации. После этого, вы сможете заказать лицензию на программное обеспечение ISPsystem.');
	#---------------------------------------------------------------------------
	#---------------------------------------------------------------------------
	$Count = DB_Count('DSOrdersOwners',Array('Where'=>"`ContractID` = " . $ContractID . " AND `StatusID` != 'Active'"));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#---------------------------------------------------------------------------
	if($Count)
		return new gException('ISPsw_OWNER_HAVE_INACTIVE_DS_ORDER','Заказанный вами выделенный сервер неактивен. Необходимо оплатить его размешение, или, если он уже оплачен, дождаться активации. После этого, вы сможете заказать лицензию на программное обеспечение ISPsystem.');
	#---------------------------------------------------------------------------
	#---------------------------------------------------------------------------
	# если нет заказанных услуг - будет общее сообщение
	# при условии, что нет тарифов на заказ внешних лицензий
	if(!IsSet($AllowExternalOrder))
	  return new gException('ISPsw_OWNER_NOT_HAVE_ACTIVE_ORDERS','Выбранный профиль не имеет активных заказанных услуг. Выберите другой, или, закажите услугу хостинга, VPS или выделенного сервера. После этого, вы сможете заказать лицензию на программное обеспечение ISPsystem.');
}
#---------------------------------------------------------------------------
#---------------------------------------------------------------------------
if(IsSet($AllowExternalOrder)){
	# окошко для ввода IP
	$Comp = Comp_Load(
			'Form/Input',
			Array(
				'name'   => 'IP',
				'size'   => 25,
				'type'   => 'text',
				'prompt' => 'IP адрес для заказываемой лицензии. Будте внимательны, адрес можно менять лишь раз в месяц.',
				'value'  => '0.0.0.0'
			)
		);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------
	$Table[] = 'Для лицензий, заказываемых не к нашим услугам - для ваших нужд';
	$Table[] = Array('IP адрес лицензии',$Comp);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------


#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'ContractID',
      'type'  => 'hidden',
      'value' => $ContractID
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  $Form->AddChild($Comp);

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

	$Comp = Comp_Load(
				'Form/Input',
				Array(	'type'    => 'button',
					'name'    => 'Submit',
					'onclick' => "ShowWindow('/ISPswOrder',FormGet(form));",
					'value'   => 'Продолжить'
				)
			);
	#---------------------------------------------------------------------
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Table[] = $Comp;
          #---------------------------------------------------------------------
          $Comp = Comp_Load('Tables/Standard',$Table);
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Form->AddChild($Comp);
          #---------------------------------------------------------------------
          $Comp = Comp_Load(
            'Form/Input',
            Array(
              'name'  => 'StepID',
              'value' => 2,
              'type'  => 'hidden',
            )
          );
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Form->AddChild($Comp);
          #---------------------------------------------------------------------
          $DOM->AddChild('Into',$Form);


}else{ # $StepID 1 -> another



# check, select or not some order
if(!$VPSOrderID && !$DSOrderID && $IP == '0.0.0.0'){
	return new gException('ISPsw_ORDER_NOT_SELECTED','Необходимо выбрать заказ к которому прикрепляется ПО');
}
# select used order
# and check, select only one order or more
$SelectCount = 0;
if($VPSOrderID){
	$SelectCount++;
	$OrderType = "VPS";
	$DependOrderID = $VPSOrderID;
	$Columns = Array('`Login` AS `IP`');
}
if($DSOrderID){
	$SelectCount++;
	$OrderType = "DS";
	$DependOrderID = $DSOrderID;
	$Columns = Array('IP');
}
if($SelectCount > 1){
	return new gException('ISPsw_SELECTED_MORE_THAN_ONE_ORDER','Лицензию можно прикрепить только к одному заказу. Выберите лишь один пункт.');
}

#-----------------------------------------------------------------------------
#-----------------------------------------------------------------------------
# надо проверять не на этом этапе, а после выбора тарифа
# если IP != 0.0.0.0 то проверяем его в ISPsystem
#if(Is_Error(System_Load('libs/IspSoft.php')))
#	return ERROR | @Trigger_Error(500);
#-----------------------------------------------------------------------------
#$Config = Config();
#$Settings = $Config['IspSoft']['Settings'];
#if(IspSoft_Check_ISPsystem_IP($Settings, $ISPswInfo)){
#	# OK
#}else{
#	return new gException('ISPsw_IP_ADDRESS_IN_USE','Для даного IP адреса уже есть лицензия такого типа. За более подробной информацией, обратитесь в службу поддержки пользователей.');
#}
#-----------------------------------------------------------------------------
#-----------------------------------------------------------------------------
# тупая проверка IP на валидность - на этом этапе больше ничего не проверить
$IP = long2ip(ip2long($IP));


#-----------------------------------------------------------------------------
#-----------------------------------------------------------------------------
## select IP for order - если он не задан явно
if($IP == '0.0.0.0'){
	$OrderInfo = DB_Select($OrderType . 'OrdersOwners',$Columns,Array('ID'=>$DependOrderID,'UNIQ'));
	switch(ValueOf($OrderInfo)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
		case 'array':
		Debug("[comp/www/ISPswOrder]: OrderInfo found, IP = " . $OrderInfo['IP']);
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
}else{
	$OrderInfo = Array('IP' => $IP);
}

  #-----------------------------------------------------------------------------
  # IP заказа к которому надо прицепить лицензию
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'IP',
      'type'  => 'hidden',
      'value' => $OrderInfo['IP']
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  $Form->AddChild($Comp);
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  # номер заказа к которому надо прицепить лицензию
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'DependOrderID',
      'type'  => 'hidden',
      'value' => $DependOrderID
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  $Form->AddChild($Comp);
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'ContractID',
      'type'  => 'hidden',
      'value' => $ContractID
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  $Form->AddChild($Comp);
  #-----------------------------------------------------------------------------




      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Services/Schemes','ISPswSchemes',$__USER['ID'],Array('Name'),$UniqID);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Columns = Array('ID','Name','Comment','CostMonth');
      #-------------------------------------------------------------------------
      Debug("[comp/www/ISPswOrder]: IP before WherePart = " . $IP);
      if($IP == '0.0.0.0' || $IP == ''){
      	$WherePart = " AND `IsInternal` = 'yes'";
      }else{
	$WherePart = " AND `IsInternal` = 'no'";
      }
      $ISPswSchemes = DB_Select($UniqID,$Columns,Array('SortOn'=>Array('SortID'),'Where'=>"`IsActive` = 'yes'" . $WherePart));
      #-------------------------------------------------------------------------
      switch(ValueOf($ISPswSchemes)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return new gException('ISPsw_SCHEMES_NOT_FOUND','Для указанного заказа нет возможности приобретения ПО ISPsystem. Обратитесь в службу поддержки пользователей.');
        case 'array':
          #---------------------------------------------------------------------
          $NoBody = new Tag('NOBODY');
          #---------------------------------------------------------------------
          $Tr = new Tag('TR');
          #---------------------------------------------------------------------
          $Tr->AddChild(new Tag('TD',Array('class'=>'Head','colspan'=>2),'Тариф'));
          $Tr->AddChild(new Tag('TD',Array('class'=>'Head','align'=>'center'),'Цена в месяц'));
          #---------------------------------------------------------------------
          #---------------------------------------------------------------------
          $Rows = Array($Tr);
          #---------------------------------------------------------------------
          #---------------------------------------------------------------------
          foreach($ISPswSchemes as $ISPswScheme){
            #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'name'  => 'ISPswSchemeID',
                'type'  => 'radio',
                'value' => $ISPswScheme['ID']
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            if($ISPswScheme['ID'] == $ISPswSchemeID)
              $Comp->AddAttribs(Array('checked'=>'true'));
            #-------------------------------------------------------------------
            $Comment = $ISPswScheme['Comment'];
            #-------------------------------------------------------------------
            if($Comment)
              $Rows[] = new Tag('TR',new Tag('TD',Array('colspan'=>2)),new Tag('TD',Array('colspan'=>2,'class'=>'Standard','style'=>'background-color:#FDF6D3;'),$Comment));
            #-------------------------------------------------------------------
            $CostMonth = Comp_Load('Formats/Currency',$ISPswScheme['CostMonth']);
            if(Is_Error($CostMonth))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
	    #-------------------------------------------------------------------
            $Rows[] = new Tag('TR',
	    			new Tag('TD',Array('width'=>20),$Comp),
				new Tag('TD',Array('class'=>'Comment'),$ISPswScheme['Name']),
				new Tag('TD',Array('class'=>'Standard','align'=>'right'),$CostMonth)
			);
          }
          #---------------------------------------------------------------------
          $Comp = Comp_Load('Tables/Extended',$Rows,Array('align'=>'center'));
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Table[] = $Comp;
          #---------------------------------------------------------------------
          #---------------------------------------------------------------------
        break;
        default:
          return ERROR | @Trigger_Error(101);
      }




# 











      #-------------------------------------------------------------------------
      $Div = new Tag('DIV',Array('align'=>'right'),'');
      #-------------------------------------------------------------------------
      $Comp = Comp_Load(
        'Form/Input',
        Array(
          'type'    => 'button',
          'onclick' => 'ISPswOrder();',
          'value'   => 'Продолжить'
        )
      );
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Div->AddChild($Comp);
      #-------------------------------------------------------------------------
      $Table[] = $Div;
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Tables/Standard',$Table,Array('width'=>400));
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Form->AddChild($Comp);
      #-------------------------------------------------------------------------
      $DOM->AddChild('Into',$Form);

}	# end of $StepID is set, and $StepID != 1 or 2


}else{ # $StepID is set -> $StepID not set
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  $Contracts = DB_Select('Contracts',Array('ID','Customer'),Array('Where'=>SPrintF("`UserID` = %u AND `TypeID` != 'NaturalPartner'",$__USER['ID'])));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Contracts)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('CONTRACTS_NOT_FOUND','Система не обнаружила у Вас ни одного договора. Пожалуйста, перейдите в раздел [Мой офис - Договоры] и сформируйте хотя бы 1 договор.');
    case 'array':
      #-------------------------------------------------------------------------
      $Options = Array();
      #-------------------------------------------------------------------------
      foreach($Contracts as $Contract){
        #-----------------------------------------------------------------------
        $Customer = $Contract['Customer'];
        #-----------------------------------------------------------------------
        if(Mb_StrLen($Customer) > 20)
          $Customer = SPrintF('%s...',Mb_SubStr($Customer,0,20));
        #-----------------------------------------------------------------------
        $Options[$Contract['ID']] = $Customer;
      }
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Form/Select',Array('name'=>'ContractID'),$Options,$ContractID);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $NoBody = new Tag('NOBODY',$Comp);
      #-------------------------------------------------------------------------
      $Window = JSON_Encode(Array('Url'=>'/ISPswOrder','Args'=>Array()));
      #-------------------------------------------------------------------------
      $A = new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ContractMake',{Window:'%s'});",Base64_Encode($Window))),'[новый]');
      #-------------------------------------------------------------------------
      $NoBody->AddChild($A);
      #-------------------------------------------------------------------------
      $Table = Array(Array('Базовый договор',$NoBody));
      #-------------------------------------------------------------------------
      

	$Comp = Comp_Load(
				'Form/Input',
				Array(	'type'    => 'button',
					'name'    => 'Submit',
					'onclick' => "ShowWindow('/ISPswOrder',FormGet(form));",
					'value'   => 'Продолжить'
				)
			);
	#---------------------------------------------------------------------
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Table[] = $Comp;
          #---------------------------------------------------------------------
          $Comp = Comp_Load('Tables/Standard',$Table);
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Form->AddChild($Comp);
          #---------------------------------------------------------------------
          $Comp = Comp_Load(
            'Form/Input',
            Array(
              'name'  => 'StepID',
              'value' => 1,
              'type'  => 'hidden',
            )
          );
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Form->AddChild($Comp);
          #---------------------------------------------------------------------
          $DOM->AddChild('Into',$Form);




    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
$Out = $DOM->Build(FALSE);
#-------------------------------------------------------------------------------
if(Is_Error($Out))
  return ERROR | @Trigger_Error(500);
#Debug("[comp/www/ISPswOrder]: EOF");
#Debug(print_r($DOM, true));
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------

?>
