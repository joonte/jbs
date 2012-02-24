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
$InvoiceID       = (integer) @$Args['InvoiceID'];
$CreateDate      = (integer) @$Args['CreateDate'];
$PaymentSystemID =  (string) @$Args['PaymentSystemID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Invoice = DB_Select('InvoicesOwners',Array('ID','UserID','ContractID','IsPosted'),Array('UNIQ','ID'=>$InvoiceID));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
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
        $InvoiceID = $Invoice['ID'];
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
        $Contract = DB_Select('Contracts',Array('ID','CreateDate','TypeID'),Array('UNIQ','ID'=>$Invoice['ContractID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($Contract)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            $Config = Config();
            #-------------------------------------------------------------------
            $PaymentSystems = $Config['Invoices']['PaymentSystems'];
            #-------------------------------------------------------------------
            if(!$PaymentSystemID)
              return new gException('PAYMENT_SYSTEM_NOT_SELECTED','Платежная система не указана');
            #-------------------------------------------------------------------
            $Config = Config();
            #-------------------------------------------------------------------
            $PaymentSystems = $Config['Invoices']['PaymentSystems'];
            #-------------------------------------------------------------------
            if(!IsSet($PaymentSystems[$PaymentSystemID]))
              return new gException('PAYMENT_SYSTEM_NOT_FOUND','Платежная система не найдена');
            #-------------------------------------------------------------------
            $PaymentSystem = $PaymentSystems[$PaymentSystemID];
            #-------------------------------------------------------------------
            if(!$PaymentSystem['ContractsTypes'][$Contract['TypeID']])
              return new gException('WRONG_CONTRACT_TYPE','Данный вид договора не может быть использован для выписывания счета данного типа');
            #-------------------------------------------------------------------
            $IsUpdate = DB_Update('Invoices',Array('CreateDate'=>$CreateDate,'PaymentSystemID'=>$PaymentSystemID),Array('ID'=>$InvoiceID));
            if(Is_Error($IsUpdate))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Invoices/Build',$InvoiceID);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            return Array('Status'=>'Ok');
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
