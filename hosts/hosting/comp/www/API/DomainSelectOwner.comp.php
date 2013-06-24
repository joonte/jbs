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
if(!$Agree)
  return new gException('NOT_AGREE','Вы не дали согласия на передачу ваших персональных данных');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DomainOrder = DB_Select('DomainsOrdersOwners',Array('ID','UserID','SchemeID','StatusID','DomainName','(SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `DomainsOrdersOwners`.`SchemeID`) as `DomainZone`'),Array('UNIQ','ID'=>$DomainOrderID));
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
    $IsPermission = Permission_Check('DomainsOrdersRead',(integer)$__USER['ID'],(integer)$DomainOrder['UserID']);
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
        $DomainScheme = DB_Select('DomainsSchemes',Array('Name','RegistratorID','(SELECT `TypeID` FROM `Registrators` WHERE `RegistratorID` = `Registrators`.`ID`) as `RegistratorTypeID`'),Array('UNIQ','ID'=>$DomainOrder['SchemeID']));
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
              case 'Person':
                #---------------------------------------------------------------
                if(!$PersonID)
                  return new gException('PERSON_ID_EMPTY','Укажите договор регистратора');
                #---------------------------------------------------------------
                $Config = Config();
                #---------------------------------------------------------------
                $IsSupportContracts = $Config['Domains']['Registrators'][$DomainScheme['RegistratorTypeID']]['IsSupportContracts'];
                #---------------------------------------------------------------
                if(!$IsSupportContracts)
                  return new gException('REGISTRATOR_NOT_SUPPORT_CONTRACTS','Регистратор не поддерживает договоры');
                #---------------------------------------------------------------
                $UDomainOrder['PersonID'] = $PersonID;
              break;
              case 'Profile':
                #---------------------------------------------------------------
                $Count = DB_Count('Profiles',Array('ID'=>$ProfileID));
                if(Is_Error($Count))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                if(!$Count)
                  return new gException('PROFILE_NOT_FOUND','Указанный профиль не найден');
                #---------------------------------------------------------------
                $UDomainOrder['ProfileID'] = $ProfileID;
              break;
              default:
                return ERROR | @Trigger_Error(101);
            }
            #-------------------------------------------------------------------
            $IsUpdate = DB_Update('DomainsOrders',$UDomainOrder,Array('ID'=>$DomainOrder['ID']));
            if(Is_Error($IsUpdate))
              return ERROR | @Trigger_Error(500);
	    #-------------------------------------------------------------------
            #-------------------------------------------------------------------
	    $Event = Array(
                           'UserID'        => $__USER['ID'],
			   'PriorityID'    => 'Hosting',
			   'Text'          => SPrintF('Определён владелец для заказа домена %s.%s',$DomainOrder['DomainName'],$DomainOrder['DomainZone']),
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
	        #$Comp = Comp_Load('www/Administrator/API/TaskExecute',Array('TaskID'=>$Task['ID']));
		#if(Is_Error($Comp))
		#  return ERROR | @Trigger_Error(500);
		$IsUpdate = DB_Update('Tasks',Array('ExecuteDate'=>Time()),Array('ID'=>$Task['ID']));
		if(Is_Error($IsUpdate))
		  return ERROR | @Trigger_Error(500);
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
