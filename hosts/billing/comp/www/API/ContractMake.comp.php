<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$TypeID     =  (string) @$Args['TypeID'];
$ProfileID  = (integer) @$Args['ProfileID'];
$Window     =  (string) @$Args['Window'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Types = $Config['Contracts']['Types'];
#-------------------------------------------------------------------------------
if(!IsSet($Types[$TypeID]))
  return new gException('WRONG_TYPE_ID','Неверный тип договора');
#-------------------------------------------------------------------------------
$Type = $Types[$TypeID];
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$dContract = Array(
  #-----------------------------------------------------------------------------
  'UserID'         => $__USER['ID'],
  'ProfileID'      => NULL,
  'Customer'       => $__USER['Name'],
  'TypeID'         => $TypeID,
  'IsUponConsider' => $Type['IsUponConsider']
);
#-------------------------------------------------------------------------------
$StatusID = 'Complite';
#-------------------------------------------------------------------------------
if($ProfileID){
  #-----------------------------------------------------------------------------
  $Profile = DB_Select('Profiles',Array('Name','TemplateID','UserID','StatusID'),Array('UNIQ','ID'=>$ProfileID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Profile)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('PROFILE_NOT_FOUND','Профиль не найден');
    case 'array':
      #-------------------------------------------------------------------------
      $IsPermission = Permission_Check('ProfileRead',(integer)$__USER['ID'],(integer)$Profile['UserID']);
      #-------------------------------------------------------------------------
      switch(ValueOf($IsPermission)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return ERROR | @Trigger_Error(400);
        case 'false':
          return ERROR | @Trigger_Error(700);
        case 'true':
          #---------------------------------------------------------------------
          $Count = DB_Count('Contracts',Array('Where'=>SPrintF('`ProfileID` = %u',$ProfileID)));
          if(Is_Error($Count))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          if($Count)
            return new gException('PROFILE_ALREADY_USING','Для даного профиля уже сформирован договор');
          #---------------------------------------------------------------------
          if($Profile['TemplateID'] != $Type['ProfileTemplateID'])
            return new gException('WRONG_PROFILE_TEMPLATE','Профиль не может быть использован для формирования договоров данного типа');
          #---------------------------------------------------------------------
          $dContract['ProfileID'] = $ProfileID;
          $dContract['Customer']  = $Profile['Name'];
          #---------------------------------------------------------------------
          $StatusID = ($Profile['StatusID'] != 'OnFilling'?'Public':'OnForming');
        break 2;
        default:
          return ERROR | @Trigger_Error(101);
      }
    default:
      return ERROR | @Trigger_Error(101);
  }
}

# exclude NaturalPartner
if($TypeID != "NaturalPartner"){
	#----------------------------------TRANSACTION----------------------------------
	if(Is_Error(DB_Transaction($TransactionID = UniqID('ContractMake'))))
	  return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Contract = DB_Select('Contracts','ID',Array('UNIQ','Where'=>SPrintF("`UserID` = %u AND `TypeID` = 'Default'",$__USER['ID'])));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Contract)){
	  case 'error':
	    return ERROR | @Trigger_Error(500);
	  case 'exception':
	    #---------------------------------------------------------------------------
	    $ContractID = DB_Insert('Contracts',$dContract);
	    if(Is_Error($ContractID))
	      return ERROR | @Trigger_Error(500);
	    #---------------------------------------------------------------------------
	  break;
	  case 'array':
	    #---------------------------------------------------------------------------
	    $ContractID = (integer)$Contract['ID'];
	    #---------------------------------------------------------------------------
	    $IsUpdate = DB_Update('Contracts',$dContract,Array('ID'=>$ContractID));
	    if(Is_Error($IsUpdate))
	      return ERROR | @Trigger_Error(500);
	    #---------------------------------------------------------------------------
	  break;
	  default:
	    return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Contracts/Build',$ContractID);
	if(Is_Error($Comp))
	  return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Contracts','StatusID'=>$StatusID,'RowsIDs'=>$ContractID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	  case 'error':
	    return ERROR | @Trigger_Error(500);
	  case 'exception':
	    return ERROR | @Trigger_Error(400);
	  case 'array':
	    #---------------------------------------------------------------------------
	    if(Is_Error(DB_Commit($TransactionID)))
	      return ERROR | @Trigger_Error(500);
	   #----------------------------------------------------------------------------
	   $Number = Comp_Load('Formats/Contract/Number',$ContractID);
	   if(Is_Error($Number))
	     return ERROR | @Trigger_Error(500);
	   #----------------------------------------------------------------------------
	   $Event = Array(
	   			'UserID'	=> $__USER['ID'],
				'PriorityID'	=> 'Billing',
				'Text'		=> SPrintF('Сформирован договор №%s, тип (%s), заказчик (%s)',$Number,$Type['Name'],$dContract['Customer'])
	   		 );
	   $Event = Comp_Load('Events/EventInsert',$Event);
           if(!$Event)
             return ERROR | @Trigger_Error(500);
	    #-------------------------------END TRANSACTION-----------------------------
	    $Answer = Array('Status'=>'Ok','ContractID'=>$ContractID);
	    #---------------------------------------------------------------------------
	    if($Window){
	      #-------------------------------------------------------------------------
	      $Window = JSON_Decode(Base64_Decode($Window),TRUE);
	      #-------------------------------------------------------------------------
	      $Window['Args']['ContractID'] = $ContractID;
	      #-------------------------------------------------------------------------
	      $Answer = Array('Status'=>'Window','Window'=>$Window);
	    }
	    #---------------------------------------------------------------------------
            $CacheFlush = Comp_Load('www/CacheFlush');
            if(Is_Error($CacheFlush))
              return ERROR | @Trigger_Error(500);
            #---------------------------------------------------------------------------
	    return $Answer;
	  default:
	    return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}else{
        $Contract = DB_Select('Contracts','ID',Array('UNIQ','Where'=>SPrintF("`UserID` = %u AND `TypeID` = 'NaturalPartner'",$__USER['ID'])));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Contract)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		$ContractID = DB_Insert('Contracts',$dContract);
		if(Is_Error($ContractID))
			return ERROR | @Trigger_Error(500);
		$Answer = Array('Status'=>'Ok','ContractID'=>$ContractID);
		break;
	case 'array':
		#---------------------------------------------------------------------------
		$ContractID = (integer)$Contract['ID'];
		$Answer = Array('Status'=>'Ok','ContractID'=>$ContractID);
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	# set status
	$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Contracts','StatusID'=>$StatusID,'RowsIDs'=>$ContractID));
	switch(ValueOf($Comp)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
                #---------------------------------------------------------------------------
                $CacheFlush = Comp_Load('www/CacheFlush');
                if(Is_Error($CacheFlush))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------------------
		return $Answer;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#---------------------------------------------------------------------------
}


?>
