<?php
#-------------------------------------------------------------------------------
/** @author Бреславский А.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$HostingOrders = DB_Select('HostingOrdersOwners');
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($HostingOrders as $HostingOrder){
      #-------------------------------------------------------------------------
      $Contract = DB_Select('Contracts',Array('ID','IsUponConsider'),Array('UNIQ','ID'=>$HostingOrder['ContractID']));
      #-------------------------------------------------------------------------
      switch(ValueOf($Contract)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return ERROR | @Trigger_Error(400);
        case 'array':
          #---------------------------------------------------------------------
          if($Contract['IsUponConsider'])
            continue;
          #---------------------------------------------------------------------
          if(Is_Error(System_Load('libs/WorksComplite.lib')))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $CurrentMonth = (Date('Y') - 1970)*12 + (integer)Date('n');
          #---------------------------------------------------------------------
          $Number = Comp_Load('Formats/Order/Number',$HostingOrder['OrderID']);
          if(Is_Error($Number))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Where = SPrintF('`HostingOrderID` = %u AND `DaysRemainded` > 0',$HostingOrder['ID']);
          #---------------------------------------------------------------------
          $HostingConsiders = DB_Select('HostingConsider','*',Array('Where'=>$Where));
          #---------------------------------------------------------------------
          switch(ValueOf($HostingConsiders)){
            case 'error':
              return ERROR | @Trigger_Error(500);
            case 'exception':
              # No more...
            break;
            case 'array':
              #-----------------------------------------------------------------
              if(Is_Error(DB_Transaction($TransactionID = UniqID('HostingConsider'))))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              foreach($HostingConsiders as $HostingConsider){
                #---------------------------------------------------------------
                $IsAddWorkComplite = Work_Complite_Add((integer)$Contract['ID'],$CurrentMonth,10000,SPrintF('№%s',$Number),(integer)$HostingConsider['DaysConsidered'],(double)$HostingConsider['Cost'],(double)$HostingConsider['Discont']);
                #---------------------------------------------------------------
                switch(ValueOf($IsAddWorkComplite)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'true':
                    #-----------------------------------------------------------
                    $IsUpdate = DB_Update('HostingConsider',Array('DaysConsidered'=>0),Array('ID'=>$HostingConsider['ID']));
                    if(Is_Error($IsUpdate))
                      return ERROR | @Trigger_Error(500);
                    #-----------------------------------------------------------
                  break;
                  default:
                    return ERROR | @Trigger_Error(101);
                }
              }
              #-----------------------------------------------------------------
              if(Is_Error(DB_Commit($TransactionID)))
                return ERROR | @Trigger_Error(500);
            break;
            default:
              return ERROR | @Trigger_Error(101);
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
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>