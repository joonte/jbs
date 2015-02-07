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
$ContractID     = (integer) @$Args['ContractID'];
$DomainName     =  (string) @$Args['DomainName'];
$DomainSchemeID = (integer) @$Args['DomainSchemeID'];
$StepID         = (integer) @$Args['StepID'];
$HostingOrderID = (integer) @$Args['HostingOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/WhoIs.php')))
	return ERROR | @Trigger_Error(500);
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
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/DomainOrder.js}')));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Заказ доменного имени');
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'DomainOrderForm','onsubmit'=>'return false;'));
#-------------------------------------------------------------------------------
if($HostingOrderID){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'HostingOrderID',
      'type'  => 'hidden',
      'value' => $HostingOrderID
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Form->AddChild($Comp);
}
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
if($StepID){
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
  #-----------------------------------------------------------------------------
  $Form->AddChild($Comp);
  #-----------------------------------------------------------------------------
  SetLocale(LC_ALL,'russian');
  #-----------------------------------------------------------------------------
  //$DomainName = Mb_StrToLower($DomainName,'UTF-8');
  #-----------------------------------------------------------------------------
  $Regulars = Regulars();
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load(
    'Form/Input',
    Array(
      'name'  => 'DomainName',
      'type'  => 'hidden',
      'value' => $DomainName
    )
  );
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Form->AddChild($Comp);
  #-----------------------------------------------------------------------------
  $Table = Array('Общая информация');
  #-----------------------------------------------------------------------------
  if(!$DomainSchemeID)
    return new gException('DOMAIN_SCHEME_NOT_DEFINED','Доменная зона не выбрана');
  #-----------------------------------------------------------------------------
  $Columns = Array('`DomainSchemes`.`ID`','`DomainSchemes`.`Name` as `Name`','`DomainSchemes`.`IsActive` AS `IsActive`','`Servers`.`Params` as `Params`');
  #-----------------------------------------------------------------------------
  $DomainScheme = DB_Select(Array('DomainSchemes','Servers'),$Columns,Array('UNIQ','Where'=>SPrintF('`DomainSchemes`.`ServerID` = `Servers`.`ID` AND `DomainSchemes`.`ID` = %u',$DomainSchemeID)));
  #-----------------------------------------------------------------------------
  switch(ValueOf($DomainScheme)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('DOMAIN_SCHEME_NOT_FOUND','Тарифный план не найден');
    case 'array':
      #---------------------------------------------------------------------------
      $IDNAConverter = new IDNAConvert();
      #---------------------------------------------------------------------------
      $Key = SPrintF('DomainName_%s',$IDNAConverter->encode($DomainScheme['Name']));
      #---------------------------------------------------------------------------
      if(!IsSet($Regulars[$Key]))
        $Key = 'DomainName';
      #---------------------------------------------------------------------------
      if(!Preg_Match($Regulars[$Key],$DomainName))
        return new gException('WRONG_DOMAIN_NAME','Неверное имя домена');
      #-------------------------------------------------------------------------
      if(!$DomainScheme['IsActive'])
        return new gException('SCHEME_NOT_ACTIVE','Выбранный тарифный план заказа домена не активен');
      #-------------------------------------------------------------------------
      $WhoIs = WhoIs_Check($DomainName,$DomainScheme['Name']);
      #-------------------------------------------------------------------------
      switch(ValueOf($WhoIs)){
        case 'exception':
          return new Tag('WHOIS_ERROR','Ошибка получения данных WhoIs',$WhoIs);
        break;
        case 'array':
          return new gException('DOMAIN_IS_BORROWED','Выбранный Вами домен уже занят. Выберите другое имя.');
        case 'error':
          # No more...
        case 'false':
          # No more...
        case 'true':
          #---------------------------------------------------------------------
          $Comp = Comp_Load(
            'Form/Input',
            Array(
              'name'  => 'DomainSchemeID',
              'type'  => 'hidden',
              'value' => $DomainScheme['ID']
            )
          );
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Form->AddChild($Comp);
          #---------------------------------------------------------------------
          $Comp = Comp_Load(
            'Form/Input',
            Array(
              'name'  => 'DomainZone',
              'type'  => 'hidden',
              'value' => $DomainScheme['Name']
            )
          );
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Form->AddChild($Comp);
          #---------------------------------------------------------------------
          $Table[] = Array('Доменное имя',SPrintF('%s.%s | %s',$DomainName,$DomainScheme['Name'],$DomainScheme['Params']['Name']));
	  #---------------------------------------------------------------------
	  #---------------------------------------------------------------------
          $Comp = Comp_Load('Form/Input',Array('name'=>'IsPrivateWhoIs','type'=>'checkbox','value'=>'yes','checked'=>'yes','prompt'=>'Если галочка установлена, то в тех доменных зонах, где поддерживается полное или частичное сокрытие данных владельца домена (ru,su,рф,com ....), они будут скрыты при просмотре информации о домене в сервисе WhoIs.'));
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
	  $Table[] = Array(new Tag('SPAN',Array('style'=>'cursor:pointer;','onclick'=>'ChangeCheckBox(\'IsPrivateWhoIs\'); return false;'),'Скрыть данные в WhoIs'),$Comp);
	  #---------------------------------------------------------------------
          #---------------------------------------------------------------------
          $Config = Config();
          #---------------------------------------------------------------------
          if(!$HostingOrderID){
            #-------------------------------------------------------------------
            $DOM->AddAttribs('Body',Array('onload'=>'IsNewNs();'));
            #-------------------------------------------------------------------
            $Columns = Array('ID','Login','(SELECT `Address` FROM `Servers` WHERE `Servers`.`ID` = `ServerID`) as `Address`','(SELECT `Params` FROM `Servers` WHERE `Servers`.`ID` = `ServerID`) as `Params`');
            #-------------------------------------------------------------------
            $HostingOrders = DB_Select('HostingOrdersOwners',$Columns,Array('Where'=>Array(SPrintF('`UserID` = %u',$__USER['ID']),"`StatusID` IN ('Active','Suspended','Waiting')")));
            #-------------------------------------------------------------------
            switch(ValueOf($HostingOrders)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                # No more...
              break;
              case 'array':
                #---------------------------------------------------------------
                $Table[] = new Tag('TD',Array('colspan'=>2,'width'=>300,'class'=>'Standard','style'=>'background-color:#FDF6D3;'),'Если данный домен будет использоваться совместно с заказом хостинга, выберите нужный заказ из списка:');
                #---------------------------------------------------------------
                $Options = Array('Не использовать');
                #---------------------------------------------------------------
                $Script = Array(SPrintF("var HostingOrders = [{Ns1Name:'%s',Ns2Name:'%s'}];",$DomainScheme['Params']['Ns1Name'],$DomainScheme['Params']['Ns2Name']));
                #---------------------------------------------------------------
                foreach($HostingOrders as $HostingOrder){
                  #-------------------------------------------------------------
                  $HostingOrderID = $HostingOrder['ID'];
                  #-------------------------------------------------------------
                  $Script[] = SPrintF("HostingOrders[%u] = {Ns1Name:'%s',Ns2Name:'%s'}",$HostingOrderID,$HostingOrder['Params']['Ns1Name'],$HostingOrder['Params']['Ns2Name']);
                  #-------------------------------------------------------------
                  $Options[$HostingOrderID] = SPrintF('%s (%s)',$HostingOrder['Login'],$HostingOrder['Address']);
                }
                #---------------------------------------------------------------
                $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript'),Implode(';',$Script)));
                #---------------------------------------------------------------
                $Comp = Comp_Load('Form/Select',Array('name'=>'HostingOrderID','onchange'=>'SetNs();'),$Options);
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Table[] = Array('Заказ хостинга',$Comp);
              break;
              default:
                return ERROR | @Trigger_Error(101);
            }
            #-------------------------------------------------------------------
            $Table[] = new Tag('TD',Array('colspan'=>2,'width'=>300,'class'=>'Standard','style'=>'background-color:#FDF6D3;'),'Для успешной регистрации, домен должен быть настроен на используемых именных серверах. Для этого в панели управления хостингом необходимо добавить домен как дополнительный или паркованный.');
            #-------------------------------------------------------------------
            $Messages = Messages();
            #-------------------------------------------------------------------
            $Table[] = 'Первичный сервер имен';
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'name'    => 'Ns1Name',
                'size'    => 15,
                'type'    => 'text',
                'prompt'  => $Messages['Prompts']['Domain']['NsName'],
                'onkeyup' => 'IsNewNs();',
                'value'   => $DomainScheme['Params']['Ns1Name']
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Table[] = Array('Доменный адрес',$Comp);
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'name'    => 'Ns1IP',
                'size'    => 15,
                'prompt'  => $Messages['Prompts']['IP'],
                'type'    => 'text'
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Table[] = Array('IP адрес',$Comp);
            #-------------------------------------------------------------------
            $Table[] = 'Вторичный сервер имен';
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'name'    => 'Ns2Name',
                'size'    => 15,
                'type'    => 'text',
                'prompt'  => $Messages['Prompts']['Domain']['NsName'],
                'onkeyup' => 'IsNewNs();',
                'value'   => $DomainScheme['Params']['Ns2Name']
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Table[] = Array('Доменный адрес',$Comp);
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'name'    => 'Ns2IP',
                'size'    => 15,
                'prompt'  => $Messages['Prompts']['IP'],
                'type'    => 'text'
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Table[] = Array('IP адрес',$Comp);
            #-------------------------------------------------------------------
            $Table[] = 'Дополнительный сервер имен';
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'name'    => 'Ns3Name',
                'size'    => 15,
                'type'    => 'text',
                'prompt'  => $Messages['Prompts']['Domain']['NsName'],
                'onkeyup' => 'IsNewNs();',
                'value'   => $DomainScheme['Params']['Ns3Name']
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Table[] = Array('Доменный адрес',$Comp);
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'name'    => 'Ns3IP',
                'size'    => 15,
                'prompt'  => $Messages['Prompts']['IP'],
                'type'    => 'text'
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Table[] = Array('IP адрес',$Comp);
            #-------------------------------------------------------------------
            $Table[] = 'Расширенный сервер имен';
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'name'    => 'Ns4Name',
                'size'    => 15,
                'type'    => 'text',
                'prompt'  => $Messages['Prompts']['Domain']['NsName'],
                'onkeyup' => 'IsNewNs();',
                'value'   => $DomainScheme['Params']['Ns4Name']
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Table[] = Array('Доменный адрес',$Comp);
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'name'    => 'Ns4IP',
                'size'    => 15,
                'prompt'  => $Messages['Prompts']['IP'],
                'type'    => 'text'
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Table[] = Array('IP адрес',$Comp);
          }
          #---------------------------------------------------------------------
          $Comp = Comp_Load(
            'Form/Input',
            Array(
              'type'    => 'button',
              'onclick' => 'DomainOrder();',
              'value'   => 'Продолжить'
            )
          );
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
        break 2;
        default:
          return ERROR | @Trigger_Error(101);
      }
    default:
      return ERROR | @Trigger_Error(101);
  }
}else{
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
	#-------------------------------------------------------------------------------
	$Number = Comp_Load('Formats/Contract/Number',$Contract['ID']);
	if(Is_Error($Number))
		return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        if(Mb_StrLen($Customer) > 20)
          $Customer = SPrintF('%s...',Mb_SubStr($Customer,0,20));
	#-------------------------------------------------------------------------------
	$Options[$Contract['ID']] = SPrintF('#%s / %s',$Number,$Customer);
	#-------------------------------------------------------------------------------
      }
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Form/Select',Array('name'=>'ContractID'),$Options,$ContractID);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $NoBody = new Tag('NOBODY',$Comp);
      #-------------------------------------------------------------------------
      $Window = JSON_Encode(Array('Url'=>'/DomainOrder','Args'=>Array()));
      #-------------------------------------------------------------------------
      $A = new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ContractMake',{Window:'%s'});",Base64_Encode($Window))),'[новый]');
      #-------------------------------------------------------------------------
      $NoBody->AddChild($A);
      #-------------------------------------------------------------------------
      $Table = Array(Array('Базовый договор',$NoBody));
      #-------------------------------------------------------------------------
      $UniqID = UniqID('DomainSchemes');
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Services/Schemes','DomainSchemes',$__USER['ID'],Array('Name','ServerID'),$UniqID);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Columns = Array('ID','Name','ServerID','CostOrder','(SELECT `Address` FROM `Servers` WHERE `ServerID` = `Servers`.`ID`) as `Address`','(SELECT `Params` FROM `Servers` WHERE `ServerID` = `Servers`.`ID`) as `Params`','(SELECT `SortID` FROM `Servers` WHERE `ServerID` = `Servers`.`ID`) as `ServersSortID`');
      #-------------------------------------------------------------------------
      $DomainSchemes = DB_Select($UniqID,$Columns,Array('SortOn'=>Array('ServersSortID','SortID'),'Where'=>"`IsActive` = 'yes'"));
      #-------------------------------------------------------------------------
      switch(ValueOf($DomainSchemes)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return new gException('DOMAINS_SCHEMES_NOT_FOUND','Активные тарифные планы на домены не определены');
        case 'array':
          #---------------------------------------------------------------------
          $Messages = Messages();
          #---------------------------------------------------------------------
          $Comp = Comp_Load(
            'Form/Input',
            Array(
              'name'   => 'DomainName',
              'size'   => 20,
              'type'   => 'text',
              'value'  => $DomainName,
              'onblur' => 'TrimDomainName(this);',
              'prompt' => $Messages['Prompts']['Domain']['Name'],
            )
          );
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Table[] = Array('Доменное имя',$Comp);
          #---------------------------------------------------------------------
          $Config = Config();
          #---------------------------------------------------------------------
          $Rows = Array();
          #---------------------------------------------------------------------
          $Tr = new Tag('TR');
          #---------------------------------------------------------------------
          $ServerAddress = UniqID();
          #---------------------------------------------------------------------
          foreach($DomainSchemes as $DomainScheme){
            #-------------------------------------------------------------------
            if($ServerAddress != $DomainScheme['Address']){
              #-----------------------------------------------------------------
              $ServerAddress = $DomainScheme['Address'];
              #-----------------------------------------------------------------
              if(Count($Tr->Childs)){
                #---------------------------------------------------------------
                $Rows[] = $Tr;
                #---------------------------------------------------------------
                $Tr = new Tag('TR');
              }
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Formats/String',$DomainScheme['Params']['Comment'],55);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Rows[] = new Tag('TR',new Tag('TD',Array('colspan'=>8,'class'=>'Separator'),new Tag('SPAN',Array('style'=>'font-size:16px;'),SPrintF('%s |',$DomainScheme['Params']['Name'])),new Tag('SPAN',Array('style'=>'font-size:11px;'),$Comp)));
            }
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'name'  => 'DomainSchemeID',
                'type'  => 'radio',
                'value' => $DomainScheme['ID']
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            if($DomainScheme['ID'] == $DomainSchemeID)
              $Comp->AddAttribs(Array('checked'=>'true'));
            #-------------------------------------------------------------------
            $Tr->AddChild(new Tag('TD',Array('width'=>20),$Comp));
            #-------------------------------------------------------------------
            $Tr->AddChild(new Tag('TD',Array('class'=>'Comment'),$DomainScheme['Name']));
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Formats/Currency',$DomainScheme['CostOrder']);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #---------------------------------------------------------------
            $Tr->AddChild(new Tag('TD',Array('class'=>'Standard','align'=>'right'),$Comp));
            #-------------------------------------------------------------------
            if(Count($Tr->Childs)%6 == 0){
              #-----------------------------------------------------------------
              $Rows[] = $Tr;
              #-----------------------------------------------------------------
              $Tr = new Tag('TR');
            }
          }
          #---------------------------------------------------------------------
          if(Count($Tr->Childs))
            $Rows[] = $Tr;
          #---------------------------------------------------------------------
          $Comp = Comp_Load('Tables/Extended',$Rows,Array('align'=>'center'));
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Table[] = $Comp;
          #---------------------------------------------------------------------
          $Comp = Comp_Load(
            'Form/Input',
            Array(
              'type'    => 'button',
              'onclick' => "ShowWindow('/DomainOrder',FormGet(form));",
              'value'   => 'Продолжить'
            )
          );
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
              'type'  => 'hidden',
              'name'  => 'StepID',
              'value' => 1
            )
          );
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Form->AddChild($Comp);
        break 2;
        default:
          return ERROR | @Trigger_Error(101);
      }
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Form);
#-------------------------------------------------------------------------------
$Out = $DOM->Build(FALSE);
#-------------------------------------------------------------------------------
if(Is_Error($Out))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------

?>
