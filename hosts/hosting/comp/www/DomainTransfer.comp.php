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
$DomainName     =  (string) @$Args['DomainName'];
$DomainSchemeID = (integer) @$Args['DomainSchemeID'];
$ContractID     = (integer) @$Args['ContractID'];
$StepID         = (integer) @$Args['StepID'];
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
if(Is_Error($DOM->Load('Window')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Перенос домена');
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/DomainTransfer.js}')));
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'DomainTransferForm','onsubmit'=>'return false;'));
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
  $DomainName = StrToLower($DomainName);
  #-----------------------------------------------------------------------------
  $Regulars = Regulars();
  #-----------------------------------------------------------------------------
  if(!Preg_Match($Regulars['DomainName'],$DomainName))
    return new gException('WRONG_DOMAIN_NAME','Неверное доменное имя');
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
  $Columns = Array('`DomainsSchemes`.`ID`','`DomainsSchemes`.`Name` as `Name`','IsActive','`Registrators`.`Name` as `RegistratorName`','`Registrators`.`TypeID` as `RegistratorTypeID`','Ns1Name','Ns2Name','Ns3Name','Ns4Name');
  #-----------------------------------------------------------------------------
  $DomainScheme = DB_Select(Array('DomainsSchemes','Registrators'),$Columns,Array('UNIQ','Where'=>SPrintF('`DomainsSchemes`.`RegistratorID` = `Registrators`.`ID` AND `DomainsSchemes`.`ID` = %u',$DomainSchemeID)));
  #-----------------------------------------------------------------------------
  switch(ValueOf($DomainScheme)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('DOMAIN_SCHEME_NOT_FOUND','Тарифный план не найден');
    case 'array':
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
        case 'true':
          return new gException('DOMAIN_IS_FREE','Выбранный Вами домен свободен');
        case 'error':
          # No more...
        case 'false':
          # No more...
        case 'array':
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
          $Table[] = Array('Доменное имя',SPrintF('%s.%s | %s',$DomainName,$DomainScheme['Name'],$DomainScheme['RegistratorName']));
          #---------------------------------------------------------------------
          $Table[] = new Tag('TD',Array('colspan'=>2,'width'=>300,'class'=>'Standard','style'=>'background-color:#FDF6D3;'),'Для осуществления переноса необходима подготовка определённого пакета документов. В ближайшее время Вам будет выслана инструкция и необходимая для переноса информация.');
          #---------------------------------------------------------------------
          $Config = Config();
          #---------------------------------------------------------------------
          $Registrator = $Config['Domains']['Registrators'][$DomainScheme['RegistratorTypeID']];
          #---------------------------------------------------------------------
          $IsSupportContracts = $Registrator['IsSupportContracts'];
          #---------------------------------------------------------------------
          $Comp = Comp_Load(
            'Form/Input',
            Array(
              'type' => 'text',
              'name' => 'PersonID',
              'size' => 10
            )
          );
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Adding = new Tag('NOBODY',$Comp);
          #---------------------------------------------------------------------
          $NoBody = new Tag('NOBODY',new Tag('SPAN','Укажите Ваш договор с регистратором или оставьте поле пустым'));
          #---------------------------------------------------------------------
          if($Registrator['PersonID']){
            #-------------------------------------------------------------------
            $NoBody->AddChild(new Tag('BR'));
            $NoBody->AddChild(new Tag('SPAN',Array('class'=>'Comment'),new Tag('SPAN',$Registrator['PersonID'])));
          }
          #---------------------------------------------------------------------
          $Table[] = Array($NoBody,$Adding);
          #---------------------------------------------------------------------
          $Comp = Comp_Load(
            'Form/Input',
            Array(
              'type'    => 'button',
              'onclick' => 'DomainTransfer();',
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
  $Contracts = DB_Select('Contracts',Array('ID','TypeID','Customer'),Array('Where'=>SPrintF('`UserID` = %u',$GLOBALS['__USER']['ID'])));
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
      $Window = JSON_Encode(Array('Url'=>'/DomainTransfer','Args'=>Array()));
      #-------------------------------------------------------------------------
      $A = new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ContractMake',{Window:'%s'});",Base64_Encode($Window))),'[новый]');
      #-------------------------------------------------------------------------
      $NoBody->AddChild($A);
      #-------------------------------------------------------------------------
      $Table = Array(Array('Базовый договор',$NoBody));
      #-------------------------------------------------------------------------
      $Table[] = new Tag('TD',Array('colspan'=>2,'width'=>300,'class'=>'Standard','style'=>'background-color:#FDF6D3;'),'Обращаем Ваше внимание на то, что услуга переноса для Вас является абсолютно бесплатной. Ниже приведены лишь цены на продление Вашего доменного имени.');
      #-------------------------------------------------------------------------
      $UniqID = UniqID('DomainsSchemes');
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Services/Schemes','DomainsSchemes',$__USER['ID'],Array('Name','RegistratorID'),$UniqID);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Columns = Array('ID','Name','RegistratorID','CostProlong','(SELECT `Name` FROM `Registrators` WHERE `RegistratorID` = `Registrators`.`ID`) as `RegistratorName`','(SELECT `Comment` FROM `Registrators` WHERE `RegistratorID` = `Registrators`.`ID`) as `RegistratorComment`','(SELECT `SortID` FROM `Registrators` WHERE `RegistratorID` = `Registrators`.`ID`) as `RegistratorSortID`');
      #-------------------------------------------------------------------------
      $DomainsSchemes = DB_Select($UniqID,$Columns,Array('SortOn'=>Array('RegistratorSortID','SortID'),'Where'=>"`IsActive` = 'yes'"));
      #-------------------------------------------------------------------------
      switch(ValueOf($DomainsSchemes)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return new gException('DOMAINS_SCHEMES_NOT_FOUND','Тарифные планы на домены не определены');
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
              'prompt' => $Messages['Prompts']['Domain']['Name']
            )
          );
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Table[] = Array('Доменное имя',$Comp);
          #---------------------------------------------------------------------
          $Rows = Array();
          #---------------------------------------------------------------------
          $Tr = new Tag('TR');
          #---------------------------------------------------------------------
          $RegistratorName = UniqID();
          #---------------------------------------------------------------------
          foreach($DomainsSchemes as $DomainScheme){
            #-------------------------------------------------------------------
            if($RegistratorName != $DomainScheme['RegistratorName']){
              #-----------------------------------------------------------------
              $RegistratorName = $DomainScheme['RegistratorName'];
              #-----------------------------------------------------------------
              if(Count($Tr->Childs)){
                #---------------------------------------------------------------
                $Rows[] = $Tr;
                #---------------------------------------------------------------
                $Tr = new Tag('TR');
              }
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Formats/String',$DomainScheme['RegistratorComment'],25);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Rows[] = new Tag('TR',new Tag('TD',Array('colspan'=>6,'class'=>'Separator'),new Tag('SPAN',Array('style'=>'font-size:16px;'),SPrintF('%s |',$RegistratorName)),new Tag('SPAN',$Comp)));
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
            $Comp = Comp_Load('Formats/Currency',$DomainScheme['CostProlong']);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
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
              'onclick' => "ShowWindow('/DomainTransfer',FormGet(form));",
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
