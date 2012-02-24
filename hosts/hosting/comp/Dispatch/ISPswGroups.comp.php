<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('IsSearch');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$ISPswGroups = DB_Select('ISPswGroups',Array('ID','Address'),Array('SortOn'=>'Address'));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswGroups)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return FALSE;
  case 'array':
    #---------------------------------------------------------------------------
    $Filters = Array('ПО ISPsystem');
    #---------------------------------------------------------------------------
    foreach($ISPswGroups as $ISPswGroup){
      #-------------------------------------------------------------------------
      $Filter = Array('Name'=>$ISPswGroup['Address'],'UsersIDs'=>Array());
      #-------------------------------------------------------------------------
      if($IsSearch){
        #-----------------------------------------------------------------------
        $ISPswOrders = DB_Select('ISPswOrdersOwners','UserID',Array('Where'=>SPrintF('`ServerID` = %u',$ISPswGroup['ID'])));
        #-----------------------------------------------------------------------
        switch(ValueOf($ISPswOrders)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            # No more...
          break;
          case 'array':
            #-------------------------------------------------------------------
            $UsersIDs = Array();
            #-------------------------------------------------------------------
            foreach($ISPswOrders as $ISPswOrder)
              $UsersIDs[] = $ISPswOrder['UserID'];
            #-------------------------------------------------------------------
            $UsersIDs = Array_Unique($UsersIDs);
            #-------------------------------------------------------------------
            $Filter['UsersIDs'] = $UsersIDs;
          break;
          default:
            return ERROR | @Trigger_Error(101);
        }
      }
      #-------------------------------------------------------------------------
      $Filters[$ISPswGroup['Address']] = $Filter;
    }
    #---------------------------------------------------------------------------
    return $Filters;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------


?>
