<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$ServiceID  = (integer) @$Args['ServiceID'];
$ContractID = (integer) @$Args['ContractID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Upload.php','libs/Server.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ServerSettings = SelectServerSettingsByService($ServiceID);
#-------------------------------------------------------------------------------
if(!Is_Array($ServerSettings))
	$ServerSettings = Array('ID'=>NULL);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Service = DB_Select('Services',Array('ID','Name','IsActive'),Array('UNIQ','ID'=>$ServiceID));
#-------------------------------------------------------------------------------
switch(ValueOf($Service)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if(!$Service['IsActive'])
      return new gException('SERVICE_NOT_ACTIVE','Услуга не активна');
    #---------------------------------------------------------------------------
    $ServiceFields = DB_Select('ServicesFields','*',Array('SortOn'=>'SortID','Where'=>SPrintF('`ServiceID` = %u',$ServiceID)));
    #---------------------------------------------------------------------------
    switch(ValueOf($ServiceFields)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        #-----------------------------------------------------------------------
        $Contract = DB_Select('Contracts','ID',Array('UNIQ','ID'=>$ContractID));
        #-----------------------------------------------------------------------
        switch(ValueOf($Contract)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            $Config = Config();
            #-------------------------------------------------------------------
            $Validators = $Config['Services']['Fields']['Validators'];
            #---------------------------TRANSACTION-----------------------------
            if(Is_Error(DB_Transaction($TransactionID = UniqID('ServiceOrder'))))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $ServiceOrderID = DB_Insert('Orders',Array('ContractID'=>$Contract['ID'],'ServiceID'=>$ServiceID,'ServerID'=>$ServerSettings['ID'],'Params'=>''));
            if(Is_Error($ServiceOrderID))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Orders','StatusID'=>'Waiting','RowsIDs'=>$ServiceOrderID,'Comment'=>'Заказ создан и ожидает оплаты'));
            #-------------------------------------------------------------------
            switch(ValueOf($Comp)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return ERROR | @Trigger_Error(400);
              case 'array':
                #---------------------------------------------------------------
                $Keys = Array();
                #---------------------------------------------------------------
                foreach($ServiceFields as $ServiceField){
                  #-------------------------------------------------------------
                  $ServiceFieldID = $ServiceField['ID'];
                  #-------------------------------------------------------------
                  $IOrderField = Array(
                    #-----------------------------------------------------------
                    'OrderID'        => $ServiceOrderID,
                    'ServiceFieldID' => $ServiceField['ID']
                  );
                  #-------------------------------------------------------------
                  $FieldID = SPrintF('ID%u',$ServiceFieldID);
                  #-------------------------------------------------------------
                  $Value = /*(string)*/ @$Args[$FieldID];
                  #-------------------------------------------------------------
                  switch($ServiceField['TypeID']){
                    case 'File':
                      #---------------------------------------------------------
                      $Upload = Upload_Get($FieldID);
                      #---------------------------------------------------------
                      switch(ValueOf($Upload)){
                        case 'error':
                          return ERROR | @Trigger_Error(500);
                        case 'exception':
                          #-----------------------------------------------------
                          if($ServiceField['IsDuty'])
                            return new gException('FIELD_FILE_NOT_UPLOADED',SPrintF('Файл поля (%s) не был загружен',$ServiceField['Name']));
                          #-----------------------------------------------------
                        break;
                        case 'array':
                          #-----------------------------------------------------
                          $IOrderField['FileName'] = $Upload[0]['Name'];
                          #-----------------------------------------------------
                          $Value = Base64_Encode($Upload[0]['Data']);
                        break;
                        default:
                          return ERROR | @Trigger_Error(101);
                      }
                    break;
                    case 'Select':
                      #---------------------------------------------------------
                      $Options = Explode("\n",$ServiceField['Options']);
                      #---------------------------------------------------------
                      $Alternatives = Array();
                      #---------------------------------------------------------
                      foreach($Options as $Option){
                        #-------------------------------------------------------
                        $Option = Explode("=",$Option);
                        #-------------------------------------------------------
                        $Alternatives[] = Current($Option);
                      }
                      #---------------------------------------------------------
                      if(!In_Array($Value,$Alternatives))
                        return new gException('OPTION_NOT_EXISTS',SPrintF('Неверное значение (%s) поля (%s)',$Value,$ServiceField['Name']));
                      #---------------------------------------------------------
                      if($ServiceField['IsKey'])
                        $Keys[] = $Value;
                      #---------------------------------------------------------
                    break;
                    default:
                      #---------------------------------------------------------
                      $Regulars = Regulars();
                      #---------------------------------------------------------
                      if($Value || $ServiceField['IsDuty']){
                        #-------------------------------------------------------
                        if(!Preg_Match($Regulars[$ServiceField['ValidatorID']],$Value))
                          return new gException('WRONG_FIELD_VALUE',SPrintF('Неверное значение поля (%s) ожидается (%s)',$ServiceField['Name'],$Validators[$ServiceField['ValidatorID']]['Name']));
                      }
                      #---------------------------------------------------------
                      if($ServiceField['IsKey'])
                        $Keys[] = $Value;
                  }
                  #-------------------------------------------------------------
                  $IOrderField['Value'] = $Value;
                  #-------------------------------------------------------------
                  $IsInsert = DB_Insert('OrdersFields',$IOrderField);
                  if(Is_Error($IsInsert))
                    return ERROR | @Trigger_Error(500);
                }
                #---------------------------------------------------------------
                if(!Count($Keys))
                  $Keys[] = '-';
                #---------------------------------------------------------------
                $IsUpdate = DB_Update('Orders',Array('Keys'=>Implode(', ',$Keys)),Array('ID'=>$ServiceOrderID));
                if(Is_Error($IsUpdate))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                if(Is_Error(DB_Commit($TransactionID)))
                  return ERROR | @Trigger_Error(500);
                #---------------------END TRANSACTION---------------------------
                return Array('Status'=>'Ok','ServiceOrderID'=>$ServiceOrderID);
              default:
                return ERROR | @Trigger_Error(101);
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
