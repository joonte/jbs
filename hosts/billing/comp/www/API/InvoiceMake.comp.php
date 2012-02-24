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
$ContractID      = (integer) @$Args['ContractID'];
$PaymentSystemID =  (string) @$Args['PaymentSystemID'];
$Summ            =  (double) @$Args['Summ'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Contract = DB_Select('Contracts',Array('ID','TypeID','Customer','UserID'),Array('UNIQ','ID'=>$ContractID));
#-------------------------------------------------------------------------------
switch(ValueOf($Contract)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('CONTRACT_NOT_FOUND','Договор не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('ContractRead',(integer)$__USER['ID'],(integer)$Contract['UserID']);
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
        if(!$PaymentSystemID)
          return new gException('PAYMENT_SYSTEM_NOT_SELECTED','Платежная система не указана');
        #-----------------------------------------------------------------------
        $Config = Config();
        #-----------------------------------------------------------------------
        $PaymentSystems = $Config['Invoices']['PaymentSystems'];
        #-----------------------------------------------------------------------
        if(!IsSet($PaymentSystems[$PaymentSystemID]))
          return new gException('PAYMENT_SYSTEM_NOT_FOUND','Платежная система не найдена');
        #-----------------------------------------------------------------------
        $PaymentSystem = $PaymentSystems[$PaymentSystemID];
        #-----------------------------------------------------------------------
        if(!$PaymentSystem['ContractsTypes'][$Contract['TypeID']])
          return new gException('WRONG_CONTRACT_TYPE','Данный вид договора не может быть использован для выписывания счета данного типа');
	#---------------------------------------------------------------------------
	#---------------------------------------------------------------------------
	if($Summ){
		# check minimal summ
		if($Summ < $PaymentSystem['MinimumPayment']){
			return new gException('PAYMENT_SYSTEM_MinimumPayment','Сумма платежа меньше, чем разрешено платёжной системой');
		}
		# check maximal summ
		if($Summ > $PaymentSystem['MaximumPayment']){
			return new gException('PAYMENT_SYSTEM_MaximumPayment','Сумма платежа больше, чем разрешено платёжной системой');
		}
	}
	#---------------------------------------------------------------------------
	#---------------------------------------------------------------------------
	
        #-----------------------------TRANSACTION-------------------------------
        if(Is_Error(DB_Transaction($TransactionID = UniqID('InvoiceMake'))))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $IInvoice = Array('ContractID'=>$Contract['ID'],'PaymentSystemID'=>$PaymentSystemID);
        #-----------------------------------------------------------------------
        $InvoiceID = DB_Insert('Invoices',$IInvoice);
        if(Is_Error($InvoiceID))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Where = SPrintF('`ContractID` = %u',$Contract['ID']);
        #-----------------------------------------------------------------------
        $Basket = DB_Select('BasketOwners',Array('ID','ServiceID','Comment','OrderID','Amount','Summ'),Array('Where'=>$Where));
        #-----------------------------------------------------------------------
        switch(ValueOf($Basket)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            #-------------------------------------------------------------------
            if(!$Summ)
              return new gException('SUMM_NOT_FILL','Сумма для зачисления не указана');
            #-------------------------------------------------------------------
            $IItem = Array(
              #-----------------------------------------------------------------
              'InvoiceID' => $InvoiceID,
              'ServiceID' => 1000,
              'Amount'    => 1,
              'Summ'      => $Summ
            );
            #-------------------------------------------------------------------
            $IsInsert = DB_Insert('InvoicesItems',$IItem);
            if(Is_Error($IsInsert))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
          break;
          case 'array':
            #-------------------------------------------------------------------
            $Summ = 0;
            #-------------------------------------------------------------------
            foreach($Basket as $Item){
              #-----------------------------------------------------------------
              $Summ += $Item['Summ'];
              #-----------------------------------------------------------------
              $IItem = Array(
                #---------------------------------------------------------------
                'InvoiceID' => $InvoiceID,
                'ServiceID' => $Item['ServiceID'],
                'Comment'   => $Item['Comment'],
                'OrderID'   => $Item['OrderID'],
                'Amount'    => $Item['Amount'],
                'Summ'      => $Item['Summ']
              );
              #-----------------------------------------------------------------
              $IsInsert = DB_Insert('InvoicesItems',$IItem);
              if(Is_Error($IsInsert))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $IsDelete = DB_Delete('Basket',Array('ID'=>$Item['ID']));
              if(Is_Error($IsDelete))
                return ERROR | @Trigger_Error(500);
            }
          break;
          default:
            return ERROR | @Trigger_Error(101);
        }
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Invoices','StatusID'=>'Waiting','RowsIDs'=>$InvoiceID,'Comment'=>'Счет успешно сформирован и ожидает оплаты'));
        #-----------------------------------------------------------------------
        switch(ValueOf($Comp)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            if(Is_Error(DB_Commit($TransactionID)))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Number = Comp_Load('Formats/Invoice/Number',$InvoiceID);
            if(Is_Error($Number))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Summ = Comp_Load('Formats/Currency',$Summ);
            if(Is_Error($Summ))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
	    $Event = Array(
	    			'UserID'	=> $Contract['UserID'],
				'PriorityID'	=> 'Billing',
				'Text'		=> SPrintF('Выписан новый счет №%s по договору (%s), платежная система (%s), сумма (%s)',$Number,$Contract['Customer'],$PaymentSystem['Name'],$Summ)
	    		  );
	    $Event = Comp_Load('Events/EventInsert',$Event);
	    if(!$Event)
              return ERROR | @Trigger_Error(500);
            #---------------------------END TRANSACTION-------------------------
            return Array('Status'=>'Ok','InvoiceID'=>$InvoiceID);
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
