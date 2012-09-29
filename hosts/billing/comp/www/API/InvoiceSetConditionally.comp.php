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
$InvoicesIDs  = (array) @$Args['RowsIDs'];
#-------------------------------------------------------------------------------
if(Count($InvoicesIDs) < 1)
  return new gException('ACCOUNTS_NOT_SELECTED','Счёт для зачисления условной оплаты не выбран');
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$GLOBALS['__USER']['IsAdmin'])
  return new gException('TMP_ONLY_FOR_ADMINs','Данная возможность находится в разработке');
#-------------------------------------------------------------------------------
if(SizeOf($InvoicesIDs) > 1)
  return new gException('CONDITIONALLY_PAYED_MORE_ONE_INVOICE','Условно зачислить можно лишь один счёт');
#Debug("[comp/www/API/InvoiceSetConditionally]: " . print_r($InvoicesIDs,true));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем статус счёта - только неоплаченные или отменённые можно проводить условно
# проверяем нету ли у юзера условных счетов
# проверяем не отрицательный ли у него балланс, на каком-либо договоре
# проверяем что он наоплачивал на ту сумму, начиная с которой можно проводить счета условно
# проверяем что сумма счёта не превышает сумму на которую юзер может проводить счета условно
# проверяем что именно оплачивается этим счётом - доступны не все услуги
# проводим счёт условно






#-------------------------------------------------------------------------------
$Invoices = DB_Select('InvoicesOwners',Array('ID','UserID','StatusID','IsPosted'),Array('Where'=>SPrintF('`ID` IN (%s)',Implode(',',$Array))));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoices)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    if(Is_Error(DB_Transaction($TransactionID = UniqID('InvoicesReject'))))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    foreach($Invoices as $Invoice){
      #-------------------------------------------------------------------------
      $IsPermission = Permission_Check('InvoiceEdit',(integer)$__USER['ID'],(integer)$Invoice['UserID']);
      #-------------------------------------------------------------------------
      switch(ValueOf($IsPermission)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return ERROR | @Trigger_Error(400);
        case 'false':
          return ERROR | @Trigger_Error(700);
        case 'true':
          #---------------------------------------------------------------------
          $Number = Comp_Load('Formats/Invoice/Number',$Invoice['ID']);
          if(Is_Error($Number))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          if($Invoice['IsPosted'])
            return new gException('ACCOUNT_PAYED',SPrintF('Счет №%s оплачен и не может быть отменен',$Number));
          #---------------------------------------------------------------------
          if($Invoice['StatusID'] == 'Rejected')
            return new gException('ACCOUNT_REJECTED',SPrintF('Счет №%s уже отменен',$Number));
          #---------------------------------------------------------------------
          $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Invoices','StatusID'=>'Rejected','RowsIDs'=>$Invoice['ID']));
          #---------------------------------------------------------------------
          switch(ValueOf($Comp)){
            case 'error':
              return ERROR | @Trigger_Error(500);
            case 'exception':
              return $StatusSet;
            case 'array':
              # No more...
            break;
            default:
              return ERROR | @Trigger_Error(101);
          }
        break;
        default:
          return ERROR | @Trigger_Error(101);
      }
    }
    #---------------------------------------------------------------------------
    if(Is_Error(DB_Commit($TransactionID)))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    return Array('Status'=>'Ok');
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
