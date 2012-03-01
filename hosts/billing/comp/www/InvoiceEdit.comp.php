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
$InvoiceID = (integer) @$Args['InvoiceID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Invoice = DB_Select('InvoicesOwners',Array('ID','CreateDate','UserID','PaymentSystemID','IsPosted','StatusID','(SELECT `TypeID` FROM `Contracts` WHERE `Contracts`.`ID` = `InvoicesOwners`.`ContractID`) as `ContractTypeID`'),Array('UNIQ','ID'=>$InvoiceID));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('ACCOUNT_NOT_FOUND','Счет не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('InvoiceEdit',(integer)$__USER['ID'],(integer)$Invoice['UserID']);
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
        if($Invoice['IsPosted']){
          #---------------------------------------------------------------------
          $Permission = Permission_Check('/Administrator/',(integer)$__USER['ID']);
          #---------------------------------------------------------------------
          switch(ValueOf($Permission)){
            case 'error':
              return ERROR | @Trigger_Error(500);
            case 'exception':
              return ERROR | @Trigger_Error(400);
            case 'true':
              # No more...
            break;
            case 'false':
              return new gException('ACCOUNT_PAYED','Счет оплачен и не может быть изменен');
            default:
              return ERROR | @Trigger_Error(101);
          }
        }
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
        $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/InvoiceEdit.js}')));
        #-----------------------------------------------------------------------
        $DOM->AddText('Title','Изменение счета');
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('jQuery/DatePicker','CreateDate',$Invoice['CreateDate']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Дата создания',$Comp);
        #-----------------------------------------------------------------------
        $Config = Config();
        #-----------------------------------------------------------------------
        $PaymentSystems = $Config['Invoices']['PaymentSystems'];
        #-----------------------------------------------------------------------
        $Options = Array();
        #-----------------------------------------------------------------------
        foreach(Array_Keys($PaymentSystems) as $PaymentSystemID){
          #---------------------------------------------------------------------
          $PaymentSystem = $PaymentSystems[$PaymentSystemID];
          #---------------------------------------------------------------------
          if(!$PaymentSystem['IsActive'] || !$PaymentSystem['ContractsTypes'][$Invoice['ContractTypeID']])
            continue;
          #---------------------------------------------------------------------
          $Options[$PaymentSystemID] = $PaymentSystem['Name'];
        }
        #-----------------------------------------------------------------------
        if(!Count($Options))
          return new gException('PAYMENT_SYSTEMS_NOT_DEFINED','Платежные системы не определены');
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Form/Select',Array('name'=>'PaymentSystemID','size'=>5),$Options);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Платежная система',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'onclick' => 'InvoiceEdit();',
            'type'    => 'button',
            'value'   => 'Изменить'
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = $Comp;
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Tables/Standard',$Table);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form = new Tag('FORM',Array('name'=>'InvoiceEditForm','onsubmit'=>'return false;'),$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'name'  => 'InvoiceID',
            'type'  => 'hidden',
            'value' => $Invoice['ID']
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form->AddChild($Comp);
        #------------------------------------------------------------------------
        $DOM->AddChild('Into',$Form);
        #-----------------------------------------------------------------------
        $Out = $DOM->Build(FALSE);
        #-----------------------------------------------------------------------
        if(Is_Error($Out))
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
