<?php

#-------------------------------------------------------------------------------
/** @author Sergey Sedov (for www.host-food.ru) from Tasks/DomainsForSuspend*/
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Params');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Where = "`StatusID` = 'Active' AND `ExpirationDate` - UNIX_TIMESTAMP() <= 0 AND UNIX_TIMESTAMP() - `StatusDate` > 86400";
#-------------------------------------------------------------------------------
$DomainOrders = DB_Select('DomainsOrdersOwners',Array('ID','OrderID','UserID','CONCAT(`DomainName`,".",(SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `DomainsOrdersOwners`.`SchemeID`)) AS `DomainName`'),Array('Where'=>$Where,/*'Limits'=>Array(0,$Params['ItemPerIteration'])*/));
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
      Debug( SPrintF("[Tasks/GC/SetSuspendExpiredDomains]: Блокировка домена %s; #%d.",$DomainOrder['DomainName'],$DomainOrder['OrderID']) );
      #----------------------------------TRANSACTION----------------------------
      if(Is_Error(DB_Transaction($TransactionID = UniqID('comp/Tasks/GC/SetSuspendExpiredDomains'))))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainsOrders','StatusID'=>'Suspended','RowsIDs'=>$DomainOrder['ID'],'Comment'=>'Заказ не был продлен до окончания срока регистрации'));
      #-------------------------------------------------------------------------
      switch(ValueOf($Comp)){
        case 'array':
	  $Event = Array(
	  			'UserID'	=> $DomainOrder['UserID'],
				'PriorityID'	=> 'Billing',
				'Text'		=> SPrintF('Заказ домена #%d (%s) не был продлен до окончания срока регистрации. Заказ заблокирован.',$DomainOrder['OrderID'],$DomainOrder['OrderID'])
	                );
          $Event = Comp_Load('Events/EventInsert',$Event);
          if(!$Event)
            return ERROR | @Trigger_Error(500);
          break;
        default:
          return ERROR | @Trigger_Error(500);
      }
      #-------------------------------------------------------------------------
      if(Is_Error(DB_Commit($TransactionID)))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
    }
    $Count = DB_Count('DomainsOrders',Array('Where'=>$Where));
    if(Is_Error($Count))
      return ERROR | @Trigger_Error(500);
    return ($Count?$Count:TRUE);
  break;
  default:
    return ERROR | @Trigger_Error(101);
}

?>
