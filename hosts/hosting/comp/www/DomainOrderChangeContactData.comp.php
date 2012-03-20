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
$DomainOrderID = (integer) @$Args['DomainOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class','classes/Registrator.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DomainOrder = DB_Select('DomainsOrdersOwners',Array('ID','UserID','SchemeID','DomainName','StatusID','RegistratorID'),Array('UNIQ','ID'=>$DomainOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('DomainsOrdersRead',(integer)$__USER['ID'],(integer)$DomainOrder['UserID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsPermission)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'false':
        return ERROR | @Trigger_Error(700);
      case 'true':
        # проверяем - администратор или нет. если нет - то ограничиваем частоту смены данных
        $IsAdmin = Permission_Check('/Administrator/',(integer)$__USER['ID']);
        #-----------------------------------------------------------------------------
        switch(ValueOf($IsAdmin)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return ERROR | @Trigger_Error(400);
        case 'false':
	  #-----------------------------------------------------------------------
          $CacheID = Md5($__FILE__ . $GLOBALS['__USER']['ID'] . $DomainOrder['RegistratorID']);
          $Result = CacheManager::get($CacheID);
          if($Result){
            # в кэше чего-то есть, и чего там есть - неважно.
            return new gException('WAIT_15_MINUT_BEFORE_NEXT_CHANGE','Контактные данные нельзя менять чаще чем раз в 15 минут');
          }
          #-----------------------------------------------------------------------
          break;
        case 'true':
          break;
        default:
          return ERROR | @Trigger_Error(101);
        }
        #-----------------------------------------------------------------------
        #-----------------------------------------------------------------------
        if($DomainOrder['StatusID'] != 'Active')
          return new gException('ORDER_NOT_ACTIVE','Заказ домена не активен');
        #-----------------------------------------------------------------------
        $__USER = $GLOBALS['__USER'];
        #-----------------------------------------------------------------------
        $DomainScheme = DB_Select('DomainsSchemes','Name',Array('UNIQ','ID'=>$DomainOrder['SchemeID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($DomainScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            $DOM = new DOM();
            #-------------------------------------------------------------------
            $Links = &Links();
            # Коллекция ссылок
            $Links['DOM'] = &$DOM;
            #-------------------------------------------------------------------
            if(Is_Error($DOM->Load('Window')))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $DOM->AddText('Title','Смена контактных данных владельца домена');
            #-------------------------------------------------------------------
            $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/DomainOrderChangeContactData.js}')));
            #-------------------------------------------------------------------
            //$DOM->AddAttribs('Body',Array('onload'=>'IsNewNs();'));
            #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            $Domain = SPrintF('%s.%s',$DomainOrder['DomainName'],$DomainScheme['Name']);
            #-------------------------------------------------------------------
	    # получем контактные данные домена
            $Registrator = new Registrator();
            #-------------------------------------------------------------------
            $IsSelected = $Registrator->Select((integer)$DomainOrder['RegistratorID']);
            #---------------------------------------------------------------------------
            switch(ValueOf($IsSelected)){
            case 'error':
              return ERROR | @Trigger_Error(500);
            case 'exception':
              return ERROR | @Trigger_Error(400);
            case 'true':
              break;
            default:
              return ERROR | @Trigger_Error(101);
            }
            #-------------------------------------------------------------------
	    $ContactDetail = $Registrator->GetContactDetail($Domain);
            switch(ValueOf($ContactDetail)){
            case 'error':
              return ERROR | @Trigger_Error(500);
            case 'exception':
              return new gException('CANNOT_GET_CURRENT_CONTACT_DATA','Не удалось получить текущие контактные данные от регистратора');
            case 'array':
              break;
            default:
              return ERROR | @Trigger_Error(101);
            }
            #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            $Table = Array();
            #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            $Table[] = Array('Доменное имя',$Domain);
            #-------------------------------------------------------------------
            $Messages = Messages();
            #-------------------------------------------------------------------
            $Table[] = 'Новые контактные данные';
            #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'name'    => 'Email',
                'size'    => 20,
                'type'    => 'text',
                'prompt'  => $Messages['Prompts']['Email'],
                'value'   => IsSet($ContactDetail['Email'])?$ContactDetail['Email']:''
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Table[] = Array('Электронный адрес',$Comp);
            #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'name'    => 'Phone',
                'size'    => 20,
                'type'    => 'text',
                'prompt'  => $Messages['Prompts']['Phone'],
                'value'   => IsSet($ContactDetail['Phone'])?$ContactDetail['Phone']:''
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Table[] = Array('Номер телефона',$Comp);
            #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            if(In_Array($DomainScheme['Name'],Array('ru','рф'))){
              $Comp = Comp_Load(
                'Form/Input',
                Array(
                  'name'    => 'CellPhone',
                  'size'    => 20,
                  'type'    => 'text',
                  'prompt'  => $Messages['Prompts']['Phone'],
                  'value'   => IsSet($ContactDetail['CellPhone'])?$ContactDetail['CellPhone']:''
                )
              );
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-------------------------------------------------------------------
              $Table[] = Array('Номер мобильного телефона',$Comp);
            }
            #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'type'    => 'button',
                'onclick' => 'DomainOrderChangeContactData();',
                'value'   => 'Изменить'
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Table[] = $Comp;
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Tables/Standard',$Table);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Form = new Tag('FORM',Array('name'=>'DomainOrderChangeContactDataForm','onsubmit'=>'return false;'),$Comp);
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'name'  => 'DomainOrderID',
                'type'  => 'hidden',
                'value' => $DomainOrder['ID']
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Form->AddChild($Comp);
            #-------------------------------------------------------------------
            $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript'),SPrintF("var \$Domain = '%s';",$Domain)));
            #-------------------------------------------------------------------
            $Form->AddChild($Comp);
            #-------------------------------------------------------------------
            $DOM->AddChild('Into',$Form);
            #-------------------------------------------------------------------
            if(Is_Error($DOM->Build(FALSE)))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            if(IsSet($CacheID))
              CacheManager::add($CacheID,Time(),60);
            #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            return Array('Status'=>'Ok','DOM'=>$DOM->Object);
          default:
            return ERROR | @Trigger_Error(101);
        }
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
