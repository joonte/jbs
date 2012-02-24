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
$ServiceID  = (integer) @$Args['ServiceID'];
$ContractID = (integer) @$Args['ContractID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Service = DB_Select('Services',Array('ID','Name','IsActive'),Array('UNIQ','ID'=>$ServiceID));
#-------------------------------------------------------------------------------
switch(ValueOf($Service)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if(!$Service['IsActive'])
      return new gException('SERVICE_NOT_ACTIVE','Услуга не активна');
    #---------------------------------------------------------------------------
    $ServiceFields = DB_Select('ServicesFields','*',Array('SortOn'=>'SortID','Where'=>SPrintF('`ServiceID` = %u',$ServiceID)));
    #---------------------------------------------------------------------------
    switch(ValueOf($ServiceFields)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return new gException('FIELDS_NOT_DEFINED','Поля услуги не определены');
      case 'array':
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
        $Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/ServiceOrder.js}'));
        #-----------------------------------------------------------------------
        $DOM->AddChild('Head',$Script);
        #-----------------------------------------------------------------------
        $DOM->AddText('Title',$Service['Name']);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'name'  => 'ServiceID',
            'type'  => 'hidden',
            'value' => $ServiceID
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form = new Tag('FORM',Array('name'=>'ServiceOrderForm','onsubmit'=>'return false;'),$Comp);
        #-----------------------------------------------------------------------
        $Table = Array();
        #-----------------------------------------------------------------------
        $__USER = $GLOBALS['__USER'];
        #-----------------------------------------------------------------------
        $Contracts = DB_Select('Contracts',Array('ID','Customer'),Array('Where'=>SPrintF("`UserID` = %u AND `TypeID` != 'NaturalPartner'",$__USER['ID'])));
        #-----------------------------------------------------------------------
        switch(ValueOf($Contracts)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return new gException('CONTRACTS_NOT_FOUND','Система не обнаружила у Вас ни одного договора. Пожалуйста, перейдите в раздел [Мой офис - Договоры] и сформируйте хотя бы 1 договор.');
          case 'array':
            #-------------------------------------------------------------------
            $Options = Array();
            #-------------------------------------------------------------------
            foreach($Contracts as $Contract){
              #-----------------------------------------------------------------
              $Customer = $Contract['Customer'];
              #-----------------------------------------------------------------
              if(Mb_StrLen($Customer) > 20)
                $Customer = SPrintF('%s...',Mb_SubStr($Customer,0,20));
              #-----------------------------------------------------------------
              $Options[$Contract['ID']] = $Customer;
            }
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Form/Select',Array('name'=>'ContractID'),$Options,$ContractID);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $NoBody = new Tag('NOBODY',$Comp);
            #-------------------------------------------------------------------
            $Window = JSON_Encode(Array('Url'=>'/ServiceOrder','Args'=>Array('ServiceID'=>$ServiceID)));
            #-------------------------------------------------------------------
            $A = new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ContractMake',{Window:'%s'});",Base64_Encode($Window))),'[новый]');
            #-------------------------------------------------------------------
            $NoBody->AddChild($A);
            #-------------------------------------------------------------------
            $Table = Array(Array('Базовый договор',$NoBody));
            #-------------------------------------------------------------------
            $Table[] = 'Параметры услуги';
            #-------------------------------------------------------------------
            foreach($ServiceFields as $ServiceField){
              #-----------------------------------------------------------------
              $ServiceFieldID = $ServiceField['ID'];
              #-----------------------------------------------------------------
              $FieldID = SPrintF('ID%u',$ServiceField['ID']);
              #-----------------------------------------------------------------
              switch($ServiceField['TypeID']){
                case 'Input':
                  #-------------------------------------------------------------
                  $Comp = Comp_Load(
                    'Form/Input',
                    Array(
                      'name'   => $FieldID,
                      'type'   => 'text',
                      'prompt' => $ServiceField['Prompt'],
                      'value'  => $ServiceField['Default']
                    )
                  );
                  if(Is_Error($Comp))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                break;
                case 'TextArea':
                  #-------------------------------------------------------------
                  $Comp = Comp_Load(
                    'Form/TextArea',
                    Array(
                      'name'   => $FieldID,
                      'rows'   => 4,
                      'prompt' => $ServiceField['Prompt'],
                      'cols'   => 25
                    ),
                    $ServiceField['Default']
                  );
                  if(Is_Error($Comp))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                break;
                case 'Select':
                  #-------------------------------------------------------------
                  $Options = Explode("\n",$ServiceField['Options']);
                  #-------------------------------------------------------------
                  if(!Count($Options))
                    return new gException('OPTIONS_IS_EMPTY',SPrintF('Список выбора поля (%s) пуст',$ServiceField['Name']));
                  #-------------------------------------------------------------
                  $Alternatives = Array();
                  #-------------------------------------------------------------
                  foreach($Options as $Option){
                    #-----------------------------------------------------------
                    $Option = Explode("=",$Option);
                    #-----------------------------------------------------------
                    $Cost = (double)End($Option);
                    #-----------------------------------------------------------
                    Reset($Option);
                    #-----------------------------------------------------------
                    if($Cost){
                      #---------------------------------------------------------
                      $Comp = Comp_Load('Formats/Currency',$Cost);
                      if(Is_Error($Comp))
                        return ERROR | @Trigger_Error(500);
                      #---------------------------------------------------------
                      $Alternatives[Current($Option)] = SPrintF('%s (+%s)',Next($Option),$Comp);
                    }else
                      $Alternatives[Current($Option)] = Next($Option);
                  }                  
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('Form/Select',Array('prompt'=>$ServiceField['Prompt'],'name'=>$FieldID),$Alternatives,$ServiceField['Default']);
                  if(Is_Error($Comp))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                break;
                case 'Hidden':
                  #-------------------------------------------------------------
                  $Comp = Comp_Load(
                    'Form/Input',
                    Array(
                      'name'  => $FieldID,
                      'type'  => 'hidden',
                      'value' => $ServiceField['Default']
                    )
                  );
                  if(Is_Error($Comp))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  $Form->AddChild($Comp);
                continue 2;
                case 'File':
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('Upload',$FieldID,'-');
                  if(Is_Error($Comp))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                break;
                default:
                  return ERROR | @Trigger_Error(101);
              }
              #-----------------------------------------------------------------
              $Table[] = Array(SPrintF($ServiceField['IsDuty']?'*%s':'%s',$ServiceField['Name']),$Comp);
            }
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'type'    => 'button',
                'onclick' => 'ServiceOrder();',
                'value'   => 'Заказать'
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
            $Form->AddChild($Comp);
            #-------------------------------------------------------------------
            $DOM->AddChild('Into',$Form);
            #-------------------------------------------------------------------
            $Out = $DOM->Build(FALSE);
            #-------------------------------------------------------------------
            if(Is_Error($Out))
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
