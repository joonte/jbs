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
$DomainOrderID = (integer) @$Args['DomainOrderID'];
$PersonID      =  (string) @$Args['PersonID'];
$ProfileID     = (integer) @$Args['ProfileID'];
$OwnerTypeID   =  (string) @$Args['OwnerTypeID'];
$Agree         = (boolean) @$Args['Agree'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DomainOrder = DB_Select('DomainOrdersOwners',Array('ID','UserID','SchemeID','StatusID','DomainName','(SELECT `Name` FROM `DomainSchemes` WHERE `DomainSchemes`.`ID` = `DomainOrdersOwners`.`SchemeID`) as `DomainZone`'),Array('UNIQ','ID'=>$DomainOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('DomainOrdersRead',(integer)$__USER['ID'],(integer)$DomainOrder['UserID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsPermission)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'false':
        return ERROR | @Trigger_Error(700);
      case 'true':
        #-----------------------------------------------------------------------
        $StatusID = $DomainOrder['StatusID'];
        #-----------------------------------------------------------------------
        if(!In_Array($StatusID,Array('Waiting','ClaimForRegister','ForContractRegister','ForRegister','ForTransfer')))
          return new gException('ORDER_NOT_CLAIM_STATUS','Владелец может быть определён, только для не зарегистрированных доменов и не поступивших на регистрацию или перенос');
        #-----------------------------------------------------------------------
        $__USER = $GLOBALS['__USER'];
        #-----------------------------------------------------------------------
        $DomainScheme = DB_Select('DomainSchemes',Array('Name','ServerID','(SELECT `Params` FROM `Servers` WHERE `ServerID` = `Servers`.`ID`) as `Params`'),Array('UNIQ','ID'=>$DomainOrder['SchemeID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($DomainScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            $UDomainOrder = Array('PersonID'=>'','ProfileID'=>NULL);
            #-------------------------------------------------------------------
            switch($OwnerTypeID){
              //case 'Person':
              case 'Profile':
	        #---------------------------------------------------------------
	        if(!$Agree)
                  return new gException('NOT_AGREE','Вы не дали согласия на передачу ваших персональных данных');
                #---------------------------------------------------------------
/*                $Count = DB_Count('Profiles',Array('ID'=>$ProfileID));
                if(Is_Error($Count))
                  return ERROR | @Trigger_Error(500);
*/
		#-------------------------------------------------------------------------------
		$Profile = DB_Select('ProfilesOwners',Array('*'),Array('ID'=>$ProfileID));
		#-------------------------------------------------------------------------------
		switch(ValueOf($DomainScheme)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return new gException('PROFILE_NOT_FOUND','Профиль не найден');
		case 'array':
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
                #---------------------------------------------------------------
                if(!SizeOf($Profile))
                  return new gException('PROFILE_NOT_FOUND','Указанный профиль не найден');
                #---------------------------------------------------------------
		// првоеряем что это полноценный профиль, а не обрубок какой

                $UDomainOrder['ProfileID'] = $ProfileID;
              break;
              default:
                return ERROR | @Trigger_Error(101);
            }
            #-------------------------------------------------------------------
            $IsUpdate = DB_Update('DomainOrders',$UDomainOrder,Array('ID'=>$DomainOrder['ID']));
            if(Is_Error($IsUpdate))
              return ERROR | @Trigger_Error(500);
	    #-------------------------------------------------------------------
            #-------------------------------------------------------------------
	    $Event = Array(
                           'UserID'        => $__USER['ID'],
			   'PriorityID'    => 'Hosting',
			   'Text'          => SPrintF('Определён владелец для заказа %sдомена %s.%s',($StatusID == "ForTransfer")?'на перенос ':'',$DomainOrder['DomainName'],$DomainOrder['DomainZone']),
			   );
             $Event = Comp_Load('Events/EventInsert',$Event);
             if(!$Event)
               return ERROR | @Trigger_Error(500);
	    #-------------------------------------------------------------------
	    #-------------------------------------------------------------------
	    $Where = Array(
	                    "`IsExecuted` = 'no'",
			    "`IsActive` = 'yes'",
			    "`TypeID` = 'DomainPathRegister'",
			    SPrintF('`UserID` = %u',$__USER['ID'])
	                  );
            $Tasks = DB_Select('Tasks',Array('ID'),Array('Where'=>$Where));
	    #-------------------------------------------------------------------------------
	    switch(ValueOf($Tasks)){
	    case 'error':
	      return ERROR | @Trigger_Error(500);
	    case 'exception':
	      # no tasks
	      break;
	    case 'array':
	      # активируем задачи
	      foreach($Tasks as $Task){
	        #-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('Tasks',Array('ExecuteDate'=>Time()),Array('ID'=>$Task['ID']));
		if(Is_Error($IsUpdate))
		  return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	      }
	      break;
	    default:
	      return ERROR | @Trigger_Error(101);
	    }
            #-------------------------------------------------------------------
	    #-------------------------------------------------------------------
            return Array('Status'=>'Ok');
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
