<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','DSOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/DSServer.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DSOrder = DB_Select('DSOrdersOwners',Array('ID','UserID','IP','SchemeID','(SELECT `ProfileID` FROM `Contracts` WHERE `Contracts`.`ID` = `DSOrdersOwners`.`ContractID`) as `ProfileID`'),Array('UNIQ','ID'=>$DSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DSOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $DSServer = new DSServer();
    #---------------------------------------------------------------------------
    $IsSelected = $DSServer->Select((integer)$DSOrder['SchemeID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsSelected)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'true':
        #-----------------------------------------------------------------------
        $DSScheme = DB_Select('DSSchemes','*',Array('UNIQ','ID'=>$DSOrder['SchemeID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($DSScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            $Args = Array();
            #-------------------------------------------------------------------
            $ProfileID = (integer)$DSOrder['ProfileID'];
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
            $IsCreate = Call_User_Func_Array(Array($DSServer,'Create'),$Args);
            #-------------------------------------------------------------------
            switch(ValueOf($IsCreate)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return $IsCreate;
              case 'true':
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DSOrders','StatusID'=>'Active','RowsIDs'=>$DSOrder['ID'],'Comment'=>'Заказ успешно создан на сервере'));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
		    $Event = Array(
		    			'UserID'	=> $DSOrder['UserID'],
					'PriorityID'	=> 'Billing',
					'Text'		=> SPrintF('Арендованный сервер, IP %s, тариф %s, идаентификатор %s, успешно включен',$DSOrder['IP'],$DSScheme['Name'],$DSScheme['PackageID'])
		                  );
                    $Event = Comp_Load('Events/EventInsert',$Event);
                    if(!$Event)
                       return ERROR | @Trigger_Error(500);
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
