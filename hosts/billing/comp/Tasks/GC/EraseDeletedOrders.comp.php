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
$Services = MemoryCache_Get($CacheID);
if(Is_Error($Services)){
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
  MemoryCache_Add($CacheID,$Services,600);
}
#-------------------------------------------------------------------------------
for($i=0;$i<Count($Services);$i++){
  Debug( SPrintF("[Tasks/GC/EraseDeletedOrders]: Код текущей услуги - %s",$Services[$i]['Code']) );
  #-----------------------------------------------------------------------------
  $Where = SPrintF("`StatusID` = 'Deleted' AND `StatusDate` < UNIX_TIMESTAMP( ) - %d *86400", $Params['DaysBeforeErase']);
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
        Debug( SPrintF("[Tasks/GC/EraseDeletedOrders]: Удаление заказа (%s) #%d.",$Services[$i]['Code'],$Order['OrderID']) );
        #----------------------------------TRANSACTION----------------------------
        if(Is_Error(DB_Transaction($TransactionID = UniqID('comp/Tasks/GC/EraseDeletedOrders'))))
          return ERROR | @Trigger_Error(500);
        #-------------------------------------------------------------------------
        $Comp = Comp_Load('www/API/Delete',Array('TableID'=>$Services[$i]['Table'],'RowsIDs'=>$Order['ID']));
        #-------------------------------------------------------------------------
        switch(ValueOf($Comp)){
          case 'array':
	    $Event = Array(
	    			'UserID'	=> $Order['UserID'],
				'PriorityID'	=> 'Billing',
				'Text'		=> SPrintF('Отмененный заказ (%s) #%d автоматически удален.',$Services[$i]['Code'],$Order['OrderID'])
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
      #-------------------------------------------------------------------------
      if($Count)
        return $Count;
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------

?>
