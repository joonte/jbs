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
               "`StatusID` = 'OnTransfer'",				// статус на переносе
	       "`StatusDate` < UNIX_TIMESTAMP() - 180 * 24 * 3600"	// от статуса - больше 180 дней
	       );
#-------------------------------------------------------------------------------
$Columns = Array(
                 'ID',
                 'UserID',
                 'DomainName',
                 'Name',
                 '(SELECT `Name` FROM `RegistratorsOwners` WHERE `DomainsOrdersOwners`.`RegistratorID` = `RegistratorsOwners`.`ID`) AS `RegistratorName`'
                );
#-------------------------------------------------------------------------------
$DomainOrders = DB_Select('DomainsOrdersOwners',$Columns,Array('Where'=>$Where,'Limits'=>Array(0,$Params['ItemPerIteration'])));
switch(ValueOf($DomainOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return TRUE;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($DomainOrders as $DomainOrder){
      Debug(SPrintF("[Tasks/GC/DeleteDomainsOnTransfer]: Удаление домена на переносе: %s.%s",$DomainOrder['DomainName'],$DomainOrder['Name']));
      #----------------------------------TRANSACTION----------------------------
      if(Is_Error(DB_Transaction($TransactionID = UniqID('comp/Tasks/GC/DeleteDomainsOnTransfer'))))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainsOrders','StatusID'=>'Deleted','RowsIDs'=>$DomainOrder['ID'],'Comment'=>SPrintF('Заказ домена не был перенесён к регистратору %s, более 180 дней',$DomainOrder['RegistratorName'])));
      #-------------------------------------------------------------------------
      switch(ValueOf($Comp)){
      case 'array':
        $Event = Array(
                       'UserID'    => $DomainOrder['UserID'],
                       'PriorityID'=> 'Hosting',
                       'Text'      => SPrintF('Автоматическое удаление домена (%s.%s), находится в статусе "На переносе" более 180 дней',$DomainOrder['DomainName'],$DomainOrder['Name'])
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
    $Count = DB_Count('DomainsOrdersOwners',Array('Where'=>$Where));
    if(Is_Error($Count))
      return ERROR | @Trigger_Error(500);
    return ($Count?$Count:TRUE);
  default:
    return ERROR | @Trigger_Error(101);
}

?>
