<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','VPSOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/VPSServer.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$VPSOrder = DB_Select('VPSOrdersOwners',Array('ID','UserID','ServerID','Login','Domain','SchemeID','Password','(SELECT `ProfileID` FROM `Contracts` WHERE `Contracts`.`ID` = `VPSOrdersOwners`.`ContractID`) as `ProfileID`'),Array('UNIQ','ID'=>$VPSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $VPSServer = new VPSServer();
    #---------------------------------------------------------------------------
    $IsSelected = $VPSServer->Select((integer)$VPSOrder['ServerID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsSelected)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'true':
        #-----------------------------------------------------------------------
        $VPSScheme = DB_Select('VPSSchemes','*',Array('UNIQ','ID'=>$VPSOrder['SchemeID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($VPSScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            $IPsPool = Explode("\n",$VPSServer->Settings['IPsPool']);
            #-------------------------------------------------------------------
            $IP = $IPsPool[Rand(0,Count($IPsPool) - 1)];
            #-------------------------------------------------------------------
            $Args = Array($VPSOrder['Login'],$VPSOrder['Password'],$VPSOrder['Domain'],$IP,$VPSScheme);
            #-------------------------------------------------------------------
            $User = DB_Select('Users','*',Array('UNIQ','ID'=>$VPSOrder['UserID']));
            #-------------------------------------------------------------------
            switch(ValueOf($User)) {
                case 'error':
                  return ERROR | @Trigger_Error(500);
                case 'exception':
                  return ERROR | @Trigger_Error(400);
                break;
                case 'array':
                  #-------------------------------------------------------------
                  $Args[] = $User['Email'];
                break;
                default:
                  return ERROR | @Trigger_Error(101);
            }
            #-------------------------------------------------------------------
            $ProfileID = (integer)$VPSOrder['ProfileID'];
            #-------------------------------------------------------------------
            if($ProfileID){
              #-----------------------------------------------------------------
              $Profile = DB_Select('Profiles',Array('TemplateID','Attribs'),Array('UNIQ','ID'=>$ProfileID));
              #-----------------------------------------------------------------
              switch(ValueOf($Profile)){
                case 'error':
                  return ERROR | @Trigger_Error(500);
                case 'exception':
                  # No more...
                break;
                case 'array':
                  #-------------------------------------------------------------
                  $Args[] = $Profile['TemplateID'];
                  $Args[] = $Profile['Attribs'];
                break;
                default:
                  return ERROR | @Trigger_Error(101);
              }
            }
            #-------------------------------------------------------------------
            $IsCreate = Call_User_Func_Array(Array($VPSServer,'Create'),$Args);
            #-------------------------------------------------------------------
            switch(ValueOf($IsCreate)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return $IsCreate;
              case 'true':
                # достаём собсно адрес из БД
                $VPS_IP = DB_Select('VPSOrdersOwners',Array('Login'),Array('UNIQ','ID'=>$VPSOrderID));
                switch(ValueOf($VPS_IP)){
                case 'error':
                  return ERROR | @Trigger_Error(500);
                case 'exception':
                  return ERROR | @Trigger_Error(400);
                case 'array':
                  break;
                default:
                  return ERROR | @Trigger_Error(101);
                }
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>'Active','RowsIDs'=>$VPSOrder['ID'],'Comment'=>'Заказ успешно создан на сервере'));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
		    $Event = Array(
		    			'UserID'	=> $VPSOrder['UserID'],
					'PriorityID'	=> 'Hosting',
					'Text'		=> SPrintF('Заказ VPS [%s] успешно создан на сервере (%s) с тарифным планом (%s), идентификатор пакета (%s)',$VPS_IP['Login'],$VPSServer->Settings['Address'],$VPSScheme['Name'],$VPSScheme['PackageID'])
		                  );
                    $Event = Comp_Load('Events/EventInsert',$Event);
                    if(!$Event)
                       return ERROR | @Trigger_Error(500);
                    #-----------------------------------------------------------
		    $GLOBALS['TaskReturnInfo'] = Array($VPSServer->Settings['Address'],$VPS_IP['Login'],$VPSScheme['Name']);
		    #-----------------------------------------------------------
                    return TRUE;
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
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
