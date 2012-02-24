<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('UserID','OrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Links = &Links();
#-------------------------------------------------------------------------------
$CacheID = SPrintF('Basket:%u',$UserID);
#-------------------------------------------------------------------------------
if(IsSet($Links[$CacheID]))
  return TRUE;
#-------------------------------------------------------------------------------
$Links[$CacheID] = TRUE;
#-------------------------------------------------------------------------------
$Columns = Array('OrderID','Amount','(SELECT `ServiceID` FROM `Orders` WHERE `Orders`.`ID` = `OrderID`) as `ServiceID`');
#-------------------------------------------------------------------------------
$Where = Array();
#-------------------------------------------------------------------------------
if($UserID)
  $Where[] = SPrintF('(SELECT `UserID`  FROM `Orders` WHERE `Orders`.`ID` = `OrderID`) = %u',$UserID);
#-------------------------------------------------------------------------------
if($OrderID)
  $Where[] = SPrintF('`OrderID` != %u',$OrderID);
#-------------------------------------------------------------------------------
$Basket = DB_Select('BasketOwners',$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($Basket)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Basket as $Order){
      #-------------------------------------------------------------------------
      $Path = SPrintF('Services/%u',$Order['ServiceID']);
      #-------------------------------------------------------------------------
      $Element = System_Element(SPrintF('comp/%s.comp.php',$Path));
      if(!Is_Error($Element)){
        #-----------------------------------------------------------------------
        $Comp = Comp_Load($Path,$Order);
        #-----------------------------------------------------------------------
        switch(ValueOf($Comp)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            # No more...
          break;
          case 'true':
            # No more...
          break;
          default:
            return ERROR | @Trigger_Error(101);
        }
      }else{
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('www/API/ServiceOrderPay',Array('ServiceOrderID'=>$Order['OrderID'],'AmountPay'=>$Order['Amount']));
        #-----------------------------------------------------------------------
        switch(ValueOf($Comp)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            # No more...
          break;
          break;
          case 'array':
            # No more...
          break;
          default:
            return ERROR | @Trigger_Error(101);
        }
      }
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------

?>
