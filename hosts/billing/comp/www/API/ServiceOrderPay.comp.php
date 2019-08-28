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
$ServiceOrderID = (integer) @$Args['ServiceOrderID'];
$AmountPay      = (integer) @$Args['AmountPay'];
$IsNoBasket     = (boolean) @$Args['IsNoBasket'];
$IsUseBasket	= (boolean) @$Args['IsUseBasket'];
$PayMessage     =  (string) @$Args['PayMessage'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','ContractID','UserID','ServiceID','IsPayed','ExpirationDate','Keys','StatusID','(SELECT `Balance` FROM `Contracts` WHERE `Contracts`.`ID` = `ContractID`) as `ContractBalance`');
#-------------------------------------------------------------------------------
$ServiceOrder = DB_Select('OrdersOwners',$Columns,Array('UNIQ','ID'=>$ServiceOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ServiceOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('SERVICE_ORDER_NOT_FOUND','Выбранный заказ не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('ServicesOrdersPay',(integer)$__USER['ID'],(integer)$ServiceOrder['UserID']);
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
        if($AmountPay < 1)
          return new gException('WRONG_AMOUNT_PAY','Неверное кол-во единиц оплаты');
        #-----------------------------------------------------------------------
        $StatusID = $ServiceOrder['StatusID'];
        #-----------------------------------------------------------------------
        if(!In_Array($StatusID,Array('Waiting','Active','Suspended')))
          return new gException('SERVICE_ORDER_CAN_NOT_PAY','Заказ не может быть оплачен');
        #-----------------------------------------------------------------------
        $Service = DB_Select('Services',Array('ID','ConsiderTypeID','Measure','CostOn','Cost','IsActive','IsProlong','Params'),Array('UNIQ','ID'=>$ServiceOrder['ServiceID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($Service)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            $IsPayed = $ServiceOrder['IsPayed'];
            #-------------------------------------------------------------------
            if($IsPayed){
              #-----------------------------------------------------------------
              if(!$Service['IsProlong'])
                return new gException('SERVICE_NOT_ALLOW_PROLONG','Услуга не позволяет продление');
            }else{
              #-----------------------------------------------------------------
              if(!$Service['IsActive'])
                return new gException('SERVICE_NOT_ACTIVE','Услуга не активна');
            }
            #-------------------------TRANSACTION-------------------------------
            if(Is_Error(DB_Transaction($TransactionID = UniqID('ServiceOrderPay'))))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Cost = $Service['Cost'];
            #-------------------------------------------------------------------
            $ServiceOrderFields = DB_Select('OrdersFields',Array('ID','ServiceFieldID','Value','FileName'),Array('Where'=>SPrintF('`OrderID` = %u',$ServiceOrderID)));
            #-------------------------------------------------------------------
            switch(ValueOf($ServiceOrderFields)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                # No more...
              break;
              case 'array':
                #---------------------------------------------------------------
                foreach($ServiceOrderFields as $ServiceOrderField){
                  #-------------------------------------------------------------
                  $Value = $ServiceOrderField['Value'];
                  #-------------------------------------------------------------
                  $ServiceField = DB_Select('ServicesFields',Array('Name','TypeID','Options'),Array('UNIQ','ID'=>$ServiceOrderField['ServiceFieldID']));
                  #-------------------------------------------------------------
                  switch(ValueOf($ServiceField)){
                    case 'error':
                      return ERROR | @Trigger_Error(500);
                    case 'exception':
                      return ERROR | @Trigger_Error(400);
                    case 'array':
                      #---------------------------------------------------------
                      if($ServiceField['TypeID'] != 'Select')
                        break;
                      #---------------------------------------------------------
                      $Options = Explode("\n",$ServiceField['Options']);
                      #---------------------------------------------------------
                      if(Count($Options)){
                        #-------------------------------------------------------
                        foreach($Options as $Option){
                          #-----------------------------------------------------
                          $Option = Explode("=",$Option);
                          #-----------------------------------------------------
                          if(Current($Option) == $Value)
                            $Cost += (double)End($Option);
                        }
                      }
                    break;
                    default:
                      return ERROR | @Trigger_Error(101);
                  }
                }
              break;
              default:
                return ERROR | @Trigger_Error(101);
            }
            #-------------------------------------------------------------------
            $CostPay = $Cost*$AmountPay;
            #-------------------------------------------------------------------
            if(!$IsPayed && $Service['CostOn'])
              $CostPay += $Service['CostOn'];
            #-------------------------------------------------------------------
            $CostPay = Round($CostPay,2);
            #-------------------------------------------------------------------
            if($IsUseBasket || (!$IsNoBasket && $CostPay > $ServiceOrder['ContractBalance'])){
              #-----------------------------------------------------------------
              if(Is_Error(DB_Roll($TransactionID)))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $IBasket = Array('OrderID'=>$ServiceOrderID,'Comment'=>$ServiceOrder['Keys'],'Amount'=>$AmountPay,'Summ'=>$CostPay);
              #-----------------------------------------------------------------
              $Count = DB_Count('Basket',Array('Where'=>SPrintF('`OrderID` = %u',$ServiceOrderID)));
              if(Is_Error($Count))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              if($Count){
                #---------------------------------------------------------------
                $IsInsert = DB_Update('Basket',$IBasket,Array('Where'=>SPrintF('`OrderID` = %u',$ServiceOrderID)));
                if(Is_Error($IsInsert))
                  return ERROR | @Trigger_Error(500);
              }else{
                #---------------------------------------------------------------
                $IsInsert = DB_Insert('Basket',$IBasket);
                if(Is_Error($IsInsert))
                  return ERROR | @Trigger_Error(500);
              }
              #-----------------------------------------------------------------
              return Array('Status'=>'UseBasket');
            }else{
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Formats/Order/Number',$ServiceOrder['ID']);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Comp = Comp_Load('www/Administrator/API/PostingMake',Array('ContractID'=>$ServiceOrder['ContractID'],'Summ'=>-$CostPay,'ServiceID'=>$Service['ID'],'Comment'=>SPrintF('№%s на %s %s',$Comp,$AmountPay,$Service['Measure'])));
              #-----------------------------------------------------------------
              switch(ValueOf($Comp)){
                case 'error':
                  return ERROR | @Trigger_Error(500);
                case 'exception':
                  #-------------------------------------------------------------
                  if(Is_Error(DB_Roll($TransactionID)))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  #return new gException('CAN_NOT_UPDATE_BALANCE',$Comp);
		  return $Comp;
                case 'array':
                  #-------------------------------------------------------------
                  $CurrentMonth = (Date('Y') - 1970)*12 + (integer)Date('n');
                  #-------------------------------------------------------------
		  # TODO к первой единице оплаты надо прибавить цену инсталляции
		    #-------------------------------------------------------------
                    $IWorkComplite = Array(
                      #-----------------------------------------------------------
                      'ContractID' => $ServiceOrder['ContractID'],
                      'Month'      => $CurrentMonth,
                      'ServiceID'  => $Service['ID'],
                      'Comment'    => $ServiceOrder['Keys'],
                      'Amount'     => $AmountPay,
                      'Cost'       => $Cost,
                      'Discont'    => 0
                    );
                    #-------------------------------------------------------------
                    $IsInsert = DB_Insert('WorksComplite',$IWorkComplite);
                    if(Is_Error($IsInsert))
                      return ERROR | Trigger_Error(500);
                  #-------------------------------------------------------------
                  $ExpirationDate = $ServiceOrder['ExpirationDate'];
                  #-------------------------------------------------------------
                  if(!$ExpirationDate)
                    $ExpirationDate = Time();
                  #-------------------------------------------------------------
                  switch($Service['ConsiderTypeID']){
                    case 'Upon':
                      $ExpirationDate = 0;
                    break;
                    case 'Daily':
                      $ExpirationDate = MkTime(0,0,0,Date('n',$ExpirationDate),Date('j',$ExpirationDate)+$AmountPay,Date('Y',$ExpirationDate));
                    break;
                    case 'Monthly':
                      $ExpirationDate = MkTime(0,0,0,Date('n',$ExpirationDate)+$AmountPay,Date('j',$ExpirationDate),Date('Y',$ExpirationDate));
                    break;
                    case 'Yearly':
                      $ExpirationDate = MkTime(0,0,0,Date('n',$ExpirationDate),Date('j',$ExpirationDate),Date('Y',$ExpirationDate)+$AmountPay);
                    break;
                    default:
                      return ERROR | @Trigger_Error(101);
                  }
                  #-------------------------------------------------------------
                  $IsUpdate = DB_Update('Orders',Array('IsPayed'=>TRUE,'ExpirationDate'=>$ExpirationDate),Array('ID'=>$ServiceOrderID));
                  if(Is_Error($IsUpdate))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  if(!$PayMessage)
                    $PayMessage = "Заказ оплачен";
                  #-------------------------------------------------------------
                  #-------------------------------------------------------------
                  if($StatusID == 'Waiting'){
                    #-------------------------------------------------------------
                    $NewStatusID = 'OnCreate';
                    #-------------------------------------------------------------
                  }else{
                    #-------------------------------------------------------------
                    $NewStatusID = 'OnProlong';
                    #-------------------------------------------------------------
                    if(IsSet($Service['Params']['Statuses']['OnProlong']['IsNoAction']) && $Service['Params']['Statuses']['OnProlong']['IsNoAction'])
                      $NewStatusID = 'Active';
                    #-------------------------------------------------------------
                  }
                  #-------------------------------------------------------------
		  #-------------------------------------------------------------
                  $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Orders','StatusID'=>$NewStatusID,'RowsIDs'=>$ServiceOrderID,'Comment'=>$PayMessage));
                  #-------------------------------------------------------------
                  switch(ValueOf($Comp)){
                    case 'error':
                      return ERROR | @Trigger_Error(500);
                    case 'exception':
                      return ERROR | @Trigger_Error(400);
                    case 'array':
                      #---------------------------------------------------------
                      if(Is_Error(DB_Commit($TransactionID)))
                        return ERROR | @Trigger_Error(500);
                      #-------------------END TRANSACTION-----------------------
                      return Array('Status'=>'Ok');
                    default:
                      return ERROR | @Trigger_Error(101);
                  }
                default:
                   return ERROR | @Trigger_Error(101);
              }
            }
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
