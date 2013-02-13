<?php

#-------------------------------------------------------------------------------
/** @author Sergey Sedov (for www.host-food.ru) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Params');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$CacheID = SPrintF('GC[%s]',Md5('Services'));
$Services = CacheManager::get($CacheID);

if(!$Services){
  $Services = DB_Select('Services', Array('ID','Name','Code'),Array('Where' =>"`IsActive` = 'yes' AND `Code` NOT IN ('Default')"));
  #-----------------------------------------------------------------------------
  for($i=0;$i<Count($Services);$i++){
    $Query = DB_Query( SPrintF("SHOW TABLES LIKE '%s%%OrdersOwners'",$Services[$i]['Code']) );
    $Row = MySQL::Result($Query);
    foreach(Array_Keys($Row[0]) as $Key)
      $Services[$i]['View'] = $Row[0][$Key];
    $View = Preg_Split('/Owner/',$Services[$i]['View']);
    $Services[$i]['Table'] = Current($View);
  }
  #-----------------------------------------------------------------------------
  CacheManager::add($CacheID,$Services,600);
}
#-------------------------------------------------------------------------------
for($i=0;$i<Count($Services);$i++){
  Debug(SPrintF("[Tasks/GC/SetDeletedWaitingOrders]: Код текущей услуги - %s",$Services[$i]['Code']));
  #-----------------------------------------------------------------------------
  $Where = SPrintF("`StatusID` = 'Waiting' AND `StatusDate` < UNIX_TIMESTAMP() - %d * 86400", $Params['DaysBeforeDeleted']);
  #-----------------------------------------------------------------------------
  $Orders = DB_Select($Services[$i]['View'],Array('ID','OrderID','UserID'),Array('Where'=>$Where,'Limits'=>Array(0,$Params['ItemPerIteration'])));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Orders)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      # No more...
    break;
    case 'array':
      #---------------------------------------------------------------------------
      foreach($Orders as $Order){
        Debug(SPrintF("[Tasks/GC/SetDeletedWaitingOrders]: Отмена заказа (%s) #%d.",$Services[$i]['Code'],$Order['OrderID']) );
        #----------------------------------TRANSACTION----------------------------
        if(Is_Error(DB_Transaction($TransactionID = UniqID('comp/Tasks/GC/SetDeletedWaitingOrders'))))
          return ERROR | @Trigger_Error(500);
        #-------------------------------------------------------------------------
        $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>$Services[$i]['Table'],'StatusID'=>'Deleted','RowsIDs'=>$Order['ID'],'IsNoTrigger'=>'yes','Comment'=>SPrintF('Автоматическая отмена заказа, неоплачен более %d дней', $Params['DaysBeforeDeleted'])));
        #-------------------------------------------------------------------------
        switch(ValueOf($Comp)){
          case 'array':
	    $Event = Array(
	    			'UserID'	=> $Order['UserID'],
				'PriorityID'	=> 'Billing',
				'Text'		=> SPrintF('Автоматическая отмена заказа (%s) #%d, неоплачен более %d дней.',$Services[$i]['Code'],$Order['OrderID'],$Params['DaysBeforeDeleted'])
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
      $Count = DB_Count($Services[$i]['Table'],Array('Where'=>$Where));
      if(Is_Error($Count))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      if($Count)
        return $Count;
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# JBS-604: помечаем как удалённые заказы на услуги настраиваемые вгучную
$Where = SPrintF("`StatusID` = 'Waiting' AND `StatusDate` < UNIX_TIMESTAMP() - %d * 86400", $Params['DaysBeforeDeleted']);
#-------------------------------------------------------------------------------
$Orders = DB_Select('OrdersOwners',Array('ID','UserID','ServiceID','(SELECT `NameShort` FROM `Services` WHERE `OrdersOwners`.`ServiceID`=`Services`.`ID`) AS `NameShort`','(SELECT `Email` FROM `Users` WHERE `Users`.`ID` = `OrdersOwners`.`UserID`) as `Email`','(SELECT `Code` FROM `Services` WHERE `OrdersOwners`.`ServiceID`=`Services`.`ID`) AS `Code`'),Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($Orders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($Orders as $Order){
		#-------------------------------------------------------------------------------
		Debug(SPrintF("[Tasks/GC/SetDeletedWaitingOrders]: юзер %s; услуга %s; код услуги %s; ID услуги %s; заказ #%s",$Order['Email'],$Order['NameShort'],$Order['Code'],$Order['ServiceID'],$Order['ID']));
		#-------------------------------------------------------------------------------
		#----------------------------------TRANSACTION----------------------------
		if(Is_Error(DB_Transaction($TransactionID = UniqID('comp/Tasks/GC/SetDeletedWaitingOrders'))))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------
		$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Orders','StatusID'=>'Deleted','RowsIDs'=>$Order['ID'],'IsNoTrigger'=>'yes','Comment'=>SPrintF('Автоматическая отмена заказа, неоплачен более %d дней',$Params['DaysBeforeDeleted'])));
		#-------------------------------------------------------------------------
		switch(ValueOf($Comp)){
		case 'array':
			$Event = Array(
					'UserID'	=> $Order['UserID'],
					'PriorityID'	=> 'Billing',
					'Text'		=> SPrintF('Автоматическая отмена заказа (%s) #%d, неоплачен более %d дней.',$Order['NameShort'],$Order['ID'],$Params['DaysBeforeDeleted'])
					);
			#-------------------------------------------------------------------------------
			$Event = Comp_Load('Events/EventInsert',$Event);
			if(!$Event)
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(500);
		}
		#-------------------------------------------------------------------------
		if(Is_Error(DB_Commit($TransactionID)))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------
	}
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------

?>
