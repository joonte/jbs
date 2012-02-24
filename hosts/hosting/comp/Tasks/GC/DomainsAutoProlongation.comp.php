<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Params');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Where = Array(
		"`StatusID` = 'Active'",
		"`ExpirationDate` - UNIX_TIMESTAMP() <= 864000",	/* 10 days before lock		*/
		"UNIX_TIMESTAMP() - `StatusDate` > 259200",		/* 3 days from status set	*/
		);
#-------------------------------------------------------------------------------
$Columns = Array(
		'ID','OrderID','UserID','SchemeID',"CONCAT(`DomainName`,'.',`Name`) AS `DomainNameFull`",
		'(SELECT `IsAutoProlong` FROM `Orders` WHERE `DomainsOrdersOwners`.`OrderID`=`Orders`.`ID`) AS `IsAutoProlong`',
		'(SELECT `IsProlong` FROM `DomainsSchemes` WHERE `DomainsOrdersOwners`.`SchemeID`=`DomainsSchemes`.`ID`) AS `IsProlong`'
		);
#-------------------------------------------------------------------------------
#$DomainOrders = DB_Select('DomainsOrdersOwners',$Columns,Array('Where'=>$Where,'Limits'=>Array(0,$Params['ItemPerIteration'])));
$DomainOrders = DB_Select('DomainsOrdersOwners',$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return TRUE;
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($DomainOrders as $DomainOrder){
      Debug(SPrintF("[Tasks/GC/DomainsAutoProlongation]: АвтоПродление домена (%s)",$DomainOrder['DomainNameFull']));
      #----------------------------------TRANSACTION----------------------------
      if(Is_Error(DB_Transaction($TransactionID = UniqID('comp/Tasks/GC/DomainsAutoProlongation'))))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      if($DomainOrder['IsAutoProlong']){
        Debug(SPrintF("[Tasks/GC/DomainsAutoProlongation]: АвтоПродление домена (%s) включено",$DomainOrder['DomainNameFull']));
        if($DomainOrder['IsProlong']){
	  Debug(SPrintF("[Tasks/GC/DomainsAutoProlongation]: АвтоПродление домена (%s) возможно, тариф позволяет продление",$DomainOrder['DomainNameFull']));
          #--------------------------------------------------------------------
          $DomainOrderPay = Comp_Load('www/API/DomainOrderPay',Array('DomainOrderID'=>$DomainOrder['ID'],'YearsPay'=>1,'IsNoBasket'=>TRUE,'PayMessage'=>'Автоматическое продление заказа, оплата с баланса договора'));
           #-------------------------------------------------------------------
           switch(ValueOf($DomainOrderPay)){
           case 'error':
             return ERROR | @Trigger_Error(500);
           case 'exception':
	     Debug(SPrintF("[Tasks/GC/DomainsAutoProlongation]: Не удалось автоматически продлить домен (%s), причина (%s)",$DomainOrder['DomainNameFull'],$DomainOrderPay->String));
             #-----------------------------------------------------------------
             $Event = Array(
                            'UserID'        => $DomainOrder['UserID'],
                            'Text'          => SPrintF('Не удалость автоматически оплатить заказ домена (%s), причина (%s)',$DomainOrder['DomainNameFull'],$DomainOrderPay->String)
                           );
             $Event = Comp_Load('Events/EventInsert',$Event);
             if(!$Event)
               return ERROR | @Trigger_Error(500);
             #------------------------------------------------------------------
	     break;
	     #------------------------------------------------------------------
           case 'array':
	     Debug(SPrintF("[Tasks/GC/DomainsAutoProlongation]: Домен (%s) автоматически продлён",$DomainOrder['DomainNameFull']));
             # No more...
             break;
           default:
             return ERROR | @Trigger_Error(101);
           }
	   #--------------------------------------------------------------------
        }else{
	  #---------------------------------------------------------------------
	  # событие, невозможно продлить домен, причина - тариф не позволяет продление
	  Debug(SPrintF("[Tasks/GC/DomainsAutoProlongation]: АвтоПродление домена (%s) невозможно, тариф не позволяет продление",$DomainOrder['DomainNameFull']));
          $Event = Array(
                         'UserID'        => $DomainOrder['UserID'],
                         'Text'          => SPrintF('Не удалость автоматически продлить заказ домена (%s), тариф не позволяет продление',$DomainOrder['DomainNameFull'])
                        );
          $Event = Comp_Load('Events/EventInsert',$Event);
          if(!$Event)
            return ERROR | @Trigger_Error(500);
	}
	#----------------------------------------------------------------------
      }else{
        #----------------------------------------------------------------------
        # событие, невозможно продлить домен, причина - отключено автопродление
	Debug(SPrintF("[Tasks/GC/DomainsAutoProlongation]: АвтоПродление домена (%s) невозможно, отключено автопродление",$DomainOrder['DomainNameFull']));
        $Event = Array(
                       'UserID'        => $DomainOrder['UserID'],
                       'Text'          => SPrintF('Не удалость автоматически продлить заказ домена (%s), отключено автопродление',$DomainOrder['DomainNameFull'])
                       );
        $Event = Comp_Load('Events/EventInsert',$Event);
        if(!$Event)
          return ERROR | @Trigger_Error(500);
      }
      #-------------------------------------------------------------------------
      #-------------------------------------------------------------------------
      if(Is_Error(DB_Commit($TransactionID)))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
    }
    #-------------------------------------------------------------------------
    break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------

?>
