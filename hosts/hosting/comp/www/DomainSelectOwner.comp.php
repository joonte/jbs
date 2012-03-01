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
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$DomainOrderID = (integer) @$Args['DomainOrderID'];
$ProfileID     = (integer) @$Args['ProfileID'];
$StepID        = (integer) @$Args['StepID'];
$OwnerTypeID   =  (string) @$Args['OwnerTypeID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DomainOrder = DB_Select('DomainsOrdersOwners',Array('ID','UserID','SchemeID','StatusID'),Array('UNIQ','ID'=>$DomainOrderID));
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
        #-----------------------------------------------------------------------
        $StatusID = $DomainOrder['StatusID'];
        #-----------------------------------------------------------------------
        if(!In_Array($StatusID,Array('Waiting','ClaimForRegister','ForContractRegister','ForRegister')))
          return new gException('ORDER_NOT_CLAIM_STATUS','Владелец может быть определён, только для не зарегистрированных доменов и не поступивших на регистрацию');
        #-----------------------------------------------------------------------
        $__USER = $GLOBALS['__USER'];
        #-----------------------------------------------------------------------
        $DomainScheme = DB_Select('DomainsSchemes',Array('Name','RegistratorID','(SELECT `TypeID` FROM `Registrators` WHERE `RegistratorID` = `Registrators`.`ID`) as `RegistratorTypeID`'),Array('UNIQ','ID'=>$DomainOrder['SchemeID']));
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
            $DOM->AddText('Title','Владелец домена');
            #-------------------------------------------------------------------
            $Form = new Tag('FORM',Array('name'=>'DomainSelectOwnerForm','onsubmit'=>'return false;'));
            #-------------------------------------------------------------------
            if($DomainOrderID){
              #-----------------------------------------------------------------
              $Comp = Comp_Load(
                'Form/Input',
                Array(
                  'name'  => 'DomainOrderID',
                  'type'  => 'hidden',
                  'value' => $DomainOrderID
                )
              );
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Form->AddChild($Comp);
            }
            #-------------------------------------------------------------------
            $Config = Config();
            #-------------------------------------------------------------------
            $Registrator = $Config['Domains']['Registrators'][$DomainScheme['RegistratorTypeID']];
            #-------------------------------------------------------------------
            $IsSupportContracts = $Registrator['IsSupportContracts'];
            #-------------------------------------------------------------------
            if($StepID){
              #-----------------------------------------------------------------
              $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/DomainSelectOwner.js}')));
              #-----------------------------------------------------------------
              $Table = Array();
              #-----------------------------------------------------------------
              $Comp = Comp_Load(
                'Form/Input',
                Array(
                  'type'  => 'hidden',
                  'name'  => 'OwnerTypeID',
                  'value' => $OwnerTypeID
                )
              );
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Form->AddChild($Comp);
              #-----------------------------------------------------------------
              switch($OwnerTypeID){
                case 'Person':
                  #-------------------------------------------------------------
                  if(!$IsSupportContracts)
                    return new gException('REGISTRATOR_NOT_SUPPORT_CONTRACTS','Регистратор не поддерживает договоры');
                  #-------------------------------------------------------------
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
                  #-------------------------------------------------------------
                  $Adding = new Tag('NOBODY',$Comp);
                  #-------------------------------------------------------------
                  $NoBody = new Tag('NOBODY',new Tag('SPAN','Договор регистратора'));
                  #-------------------------------------------------------------
                  if($Registrator['PersonID']){
                    #-----------------------------------------------------------
                    $NoBody->AddChild(new Tag('BR'));
                    $NoBody->AddChild(new Tag('SPAN',Array('class'=>'Comment'),new Tag('SPAN',$Registrator['PersonID'])));
                  }
                  #-------------------------------------------------------------
                  $Where = SPrintF("`ID` != %u AND `PersonID` != '' AND `UserID` = %u AND `DomainsOrdersOwners`.`SchemeID` IN(SELECT `ID` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`RegistratorID` = %u)",$DomainOrder['ID'],$__USER['ID'],$DomainScheme['RegistratorID']);
                  #-------------------------------------------------------------
                  $Persons = DB_Select('DomainsOrdersOwners','PersonID',Array('Where'=>$Where));
                  #-------------------------------------------------------------
                  switch(ValueOf($Persons)){
                    case 'error':
                      return ERROR | @Trigger_Error(500);
                    case 'exception':
                      # No more...
                    break;
                    case 'array':
                      #---------------------------------------------------------
                      $Options = Array(NULL=>'ввести в ручную');
                      #---------------------------------------------------------
                      foreach($Persons as $Person)
                        $Options[$Person['PersonID']] = $Person['PersonID'];
                      #---------------------------------------------------------
                      $Comp = Comp_Load('Form/Select',Array('onchange'=>"form.PersonID.value = value;"),$Options);
                      if(Is_Error($Comp))
                        return ERROR | @Trigger_Error(500);
                      #---------------------------------------------------------
                      $Adding->AddChild(new Tag('SPAN','из списка'));
                      #---------------------------------------------------------
                      $Adding->AddChild($Comp);
                    break;
                    default:
                      return ERROR | @Trigger_Error(101);
                  }
                  #-------------------------------------------------------------
                  $Table[] = Array($NoBody,$Adding);
                break;
                case 'Profile':
                  #-------------------------------------------------------------
                  $Where = SPrintF("`UserID` = %u AND `TemplateID` IN ('Natural','Juridical') AND `StatusID` != 'Rejected'",$__USER['ID']);
                  #-------------------------------------------------------------
                  $Profiles = DB_Select('Profiles',Array('ID','Name','StatusID'),Array('Where'=>$Where));
                  #-------------------------------------------------------------
                  switch(ValueOf($Profiles)){
                    case 'error':
                      return ERROR | @Trigger_Error(500);
                    case 'exception':
                      #---------------------------------------------------------
                      $Window = JSON_Encode(Array('Url'=>'/DomainSelectOwner','Args'=>Array('DomainOrderID'=>$DomainOrder['ID'],'StepID'=>1,'OwnerTypeID'=>'Profile')));
                      #---------------------------------------------------------
                      $Comp = Comp_Load('www/ProfileEdit',Array('Window'=>Base64_Encode($Window),'TemplatesIDs'=>'Natural,Juridical'));
                      if(Is_Error($Comp))
                        return ERROR | @Trigger_Error(500);
                      #---------------------------------------------------------
                      return $Comp;
                    case 'array':
                      #---------------------------------------------------------
                      if(Count($Profiles) < 2){
                        #-------------------------------------------------------
                        $Profile = Current($Profiles);
                        #-------------------------------------------------------
                        if($Profile['StatusID'] == 'OnFilling'){
                          #-----------------------------------------------------
                          $Window = JSON_Encode(Array('Url'=>'/DomainSelectOwner','Args'=>Array('DomainOrderID'=>$DomainOrder['ID'],'StepID'=>1,'OwnerTypeID'=>'Profile')));
                          #-----------------------------------------------------
                          $Comp = Comp_Load('www/ProfileEdit',Array('Window'=>Base64_Encode($Window),'ProfileID'=>$Profile['ID']));
                          if(Is_Error($Comp))
                            return ERROR | @Trigger_Error(500);
                          #-----------------------------------------------------
                          return $Comp;
                        }
                      }
                      #---------------------------------------------------------
                      if($ProfileID)
                        $DOM->AddAttribs('Body',Array('onload'=>'DomainSelectOwner();'));
                      #---------------------------------------------------------
                      $Options = Array();
                      #---------------------------------------------------------
                      foreach($Profiles as $Profile)
                        $Options[$Profile['ID']] = $Profile['Name'];
                      #---------------------------------------------------------
                      $Comp = Comp_Load('Form/Select',Array('name'=>'ProfileID'),$Options,$ProfileID);
                      if(Is_Error($Comp))
                        return ERROR | @Trigger_Error(500);
                      #---------------------------------------------------------
                      $NoBody = new Tag('NOBODY',$Comp);
                      #---------------------------------------------------------
                      $Window = JSON_Encode(Array('Url'=>'/DomainSelectOwner','Args'=>Array('DomainOrderID'=>$DomainOrder['ID'],'StepID'=>1,'OwnerTypeID'=>'Profile')));
                      #---------------------------------------------------------
                      $A = new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ProfileEdit',{Window:'%s',TemplatesIDs:'Natural,Juridical'});",Base64_Encode($Window))),'[новый]');
                      #---------------------------------------------------------
                      $NoBody->AddChild($A);
                      #---------------------------------------------------------
                      $Table[] = Array('Использовать профиль',$NoBody);
                    break 2;
                    default:
                      return ERROR | @Trigger_Error(101);
                  }
                default:
                  return ERROR | @Trigger_Error(101);
              }
              #-----------------------------------------------------------------
              $Comp = Comp_Load(
                'Form/Input',
                Array(
                  'type'    => 'button',
                  'onclick' => 'DomainSelectOwner();',
                  'value'   => 'Продолжить'
                )
              );
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Table[] = $Comp;
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Tables/Standard',$Table);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Form->AddChild($Comp);
            }else{
              #-----------------------------------------------------------------
              $Rows = Array();
              #-----------------------------------------------------------------
              if($IsSupportContracts){
                #---------------------------------------------------------------
                $Comp = Comp_Load(
                  'Form/Input',
                  Array(
                    'name'  => 'OwnerTypeID',
                    'type'  => 'radio',
                    'value' => 'Person'
                  )
                );
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Rows[] = Array(new Tag('TD',$Comp),new Tag('TD',Array('class'=>'Standard'),'Я уже работал с регистратором и имею с ним договор я хочу заказать домен на него'));
              }else{
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/DomainSelectOwner',Array('DomainOrderID'=>$DomainOrder['ID'],'OwnerTypeID'=>'Profile','StepID'=>1));
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                return $Comp;
              }
              #-----------------------------------------------------------------
              $Comp = Comp_Load(
                'Form/Input',
                Array(
                  'name'    => 'OwnerTypeID',
                  'type'    => 'radio',
                  'checked' => 'true',
                  'value'   => 'Profile',

                )
              );
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Rows[] = Array(new Tag('TD',$Comp),new Tag('TD',Array('class'=>'Standard'),'Я еще не регистрировал домены у данного регистратора и хотел бы заполнить данные владельца в профиле'));
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Tables/Extended',$Rows);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Table = Array($Comp);
              #-----------------------------------------------------------------
              $Comp = Comp_Load(
                'Form/Input',
                Array(
                  'type'    => 'button',
                  'onclick' => "ShowWindow('/DomainSelectOwner',FormGet(form));",
                  'value'   => 'Продолжить'
                )
              );
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Table[] = $Comp;
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Tables/Standard',$Table,Array('width'=>400));
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Form->AddChild($Comp);
              #-----------------------------------------------------------------
              $Comp = Comp_Load(
                'Form/Input',
                Array(
                  'name'  => 'StepID',
                  'type'  => 'hidden',
                  'value' => 1
                )
              );
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Form->AddChild($Comp);
            }
            #-------------------------------------------------------------------
            $DOM->AddChild('Into',$Form);
            #-------------------------------------------------------------------
            if(Is_Error($DOM->Build(FALSE)))
              return ERROR | @Trigger_Error(500);
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
