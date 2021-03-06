<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','DomainOrderID','Ns1NameOld','Ns1IPOld','Ns2NameOld','Ns2IPOld','Ns3NameOld','Ns3IPOld','Ns4NameOld','Ns4IPOld');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/DomainServer.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','OrderID','UserID','DomainName','PersonID','DomainID','(SELECT `Name` FROM `DomainSchemes` WHERE `DomainSchemes`.`ID` = `DomainOrdersOwners`.`SchemeID`) as `DomainZone`','ServerID','StatusID','Ns1Name','Ns1IP','Ns2Name','Ns2IP','Ns3Name','Ns3IP','Ns4Name','Ns4IP');
#-------------------------------------------------------------------------------
$DomainOrder = DB_Select('DomainOrdersOwners',$Columns,Array('UNIQ','ID'=>$DomainOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $GLOBALS['TaskReturnInfo'] = Array(SPrintF('%s.%s',$DomainOrder['DomainName'],$DomainOrder['DomainZone'])=>Array($DomainOrder['Ns1Name'],$DomainOrder['Ns2Name']));
    #---------------------------------------------------------------------------
    if($DomainOrder['Ns3Name'])
      $GLOBALS['TaskReturnInfo'][SPrintF('%s.%s',$DomainOrder['DomainName'],$DomainOrder['DomainZone'])][] = $DomainOrder['Ns3Name'];
    #---------------------------------------------------------------------------
    if($DomainOrder['Ns4Name'])
      $GLOBALS['TaskReturnInfo'][SPrintF('%s.%s',$DomainOrder['DomainName'],$DomainOrder['DomainZone'])][] = $DomainOrder['Ns4Name'];
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    $Server = new DomainServer();
    #---------------------------------------------------------------------------
    $IsSelected = $Server->Select((integer)$DomainOrder['ServerID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsSelected)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'true':
        #-----------------------------------------------------------------------
        switch($DomainOrder['StatusID']){
          case 'ForNsChange':
            #-------------------------------------------------------------------
            $DomainNsChange = $Server->DomainNsChange($DomainOrder['DomainName'],$DomainOrder['DomainZone'],$DomainOrder['PersonID'],$DomainOrder['DomainID'],$DomainOrder['Ns1Name'],$DomainOrder['Ns1IP'],$DomainOrder['Ns2Name'],$DomainOrder['Ns2IP'],$DomainOrder['Ns3Name'],$DomainOrder['Ns3IP'],$DomainOrder['Ns4Name'],$DomainOrder['Ns4IP']);
            #-------------------------------------------------------------------
            switch(ValueOf($DomainNsChange)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                #---------------------------------------------------------------
                $IsUpdate = DB_Update('DomainOrders',Array('Ns1Name'=>$Ns1NameOld,'Ns1IP'=>$Ns1IPOld,'Ns2Name'=>$Ns2NameOld,'Ns2IP'=>$Ns2IPOld,'Ns3Name'=>$Ns3NameOld,'Ns3IP'=>$Ns3IPOld,'Ns4Name'=>$Ns4NameOld,'Ns4IP'=>$Ns4IPOld),Array('ID'=>$DomainOrder['ID']));
                if(Is_Error($IsUpdate))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainOrders','StatusID'=>'Active','RowsIDs'=>$DomainOrder['ID'],'Comment'=>$DomainNsChange->String));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
		    $Event = Array(
		    			'UserID'	=> $DomainOrder['UserID'],
					'PriorityID'	=> 'Error',
					'Text'		=> SPrintF('Не удалось сменить именные сервера заказу домена (%s.%s) в автоматическом режиме, причина (%s).',$DomainOrder['DomainName'],$DomainOrder['DomainZone'],$DomainNsChange->String),
					'IsReaded'	=> TRUE
		                  );
                     $Event = Comp_Load('Events/EventInsert',$Event);
                     if(!$Event)
                       return ERROR | @Trigger_Error(500);
                    #-----------------------------------------------------------
                    return TRUE;
                  default:
                    return ERROR | @Trigger_Error(101);
                }
              case 'false':
                return 300;
              case 'array':
                #---------------------------------------------------------------
                $Task['Params']['TicketID'] = $DomainNsChange['TicketID'];
                #---------------------------------------------------------------
                $IsUpdate = DB_Update('Tasks',Array('Params'=>$Task['Params']),Array('ID'=>$Task['ID']));
                if(Is_Error($IsUpdate))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainOrders','StatusID'=>'OnNsChange','RowsIDs'=>$DomainOrder['ID'],'Comment'=>'Регистратор принял заявку на изменение именных серверов'));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    return 300;
                  default:
                    return ERROR | @Trigger_Error(101);
                }
              default:
                return ERROR | @Trigger_Error(101);
            }
          case 'OnNsChange':
            #-------------------------------------------------------------------
            $TicketID = $Task['Params']['TicketID'];
            #-------------------------------------------------------------------
            $IsNsChange = $Server->CheckTask($TicketID);
            #-------------------------------------------------------------------
            switch(ValueOf($IsNsChange)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return new gException('TRANSFER_TO_OPERATOR','Задание не может быть выполнено автоматически и передано оператору');
              case 'false':
                return 300;
              case 'array':
                #---------------------------------------------------------------
		$Array = Array($DomainOrder['Ns1Name'],$DomainOrder['Ns2Name']);
		#---------------------------------------------------------------
                if($DomainOrder['Ns3Name'])
                  $Array[] = $DomainOrder['Ns3Name'];
                #---------------------------------------------------------------------------
                if($DomainOrder['Ns4Name'])
                  $Array[] = $DomainOrder['Ns4Name'];
                #---------------------------------------------------------------------------
		$Event = Array(
				'UserID'	=> $DomainOrder['UserID'],
				'PriorityID'	=> 'Billing',
				'Text'		=> SPrintF('Именные сервера для заказа домена (%s.%s) изменены на (%s)',$DomainOrder['DomainName'],$DomainOrder['DomainZone'],Implode(', ',$Array)),
				'PriorityID'	=> 'Notice'
		              );
                $Event = Comp_Load('Events/EventInsert',$Event);
                if(!$Event)
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainOrders','StatusID'=>'Active','RowsIDs'=>$DomainOrder['ID'],'Comment'=>'Именные сервера изменены'));
                #---------------------------------------------------------------
                switch(ValueOf($Comp)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    return TRUE;
                  default:
                    return ERROR | @Trigger_Error(101);
                }
              default:
                return ERROR | @Trigger_Error(101);
            }
          default:
            return new gException('WRONG_STATUS','Задание не может быть в данном статусе');
        }
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
