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
          $Columns = Array('MIN(`CreateDate`) as `CreateDate`','ContractID','Month','ServiceID','Comment','SUM(`Amount`) as `Amount`','Cost','Discont');
          #---------------------------------------------------------------------
          $WorksComplite = DB_Select('WorksComplite',$Columns,Array('GroupBy'=>Array('ContractID','ServiceID','Comment','Cost','Discont'),'Where'=>SPrintF('`ContractID` = %u',$Contract['ID'])));
          #---------------------------------------------------------------------
          switch(ValueOf($WorksComplite)){
            case 'error':
              return ERROR | @Trigger_Error(500);
            case 'exception':
              # No more...
            break;
            case 'array':
              #---------------------------TRANSACTION---------------------------
              if(Is_Error(DB_Transaction($TransactionID = UniqID('WorksComplite'))))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $IsDelete = DB_Delete('WorksComplite',Array('Where'=>SPrintF('`ContractID` = %u',$Contract['ID'])));
              if(Is_Error($IsDelete))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              foreach($WorksComplite as $WorkComplite){
                #---------------------------------------------------------------
                $IsInsert = DB_Insert('WorksComplite',$WorkComplite);
                if(Is_Error($IsInsert))
                  return ERROR | @Trigger_Error(500);
              }
              #-----------------------------------------------------------------
              if(Is_Error(DB_Commit($TransactionID)))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
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