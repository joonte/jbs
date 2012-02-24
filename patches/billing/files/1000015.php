<?php
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/Statuses.lib')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('ID'))))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsDelete = DB_Delete('ContractsEnclosures',Array('Where'=>"`TypeID` IN ('HostingOrderBlank','HostingOrderSchemeChange','HostingOrderDelete')"));
if(Is_Error($IsDelete))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Users = DB_Select('Users','ID',Array('Where'=>'`ID` > 999'));
#-------------------------------------------------------------------------------
switch(ValueOf($Users)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Users as $User){
      #-------------------------------------------------------------------------
      $IsInsert = DB_Insert('Contracts',Array('UserID'=>$User['ID'],'TypeID'=>'Default','Customer'=>'Default'));
      if(Is_Error($IsInsert))
        return ERROR | @Trigger_Error(500);
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Contracts = DB_Select('Contracts','*',Array('Where'=>"`TypeID` = 'Public'"));
#-------------------------------------------------------------------------------
switch(ValueOf($Contracts)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Contracts as $Contract){
      #-------------------------------------------------------------------------
      $User = DB_Select('Users','*',Array('UNIQ','ID'=>$Contract['UserID']));
      if(!Is_Array($User))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Count = 0;
      #-------------------------------------------------------------------------
      $Numbers = DB_Count('HostingOrdersOwners',Array('Where'=>SPrintF('`ContractID` = %u',$Contract['ID'])));
      if(Is_Error($Numbers))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Count += $Numbers;
      #-------------------------------------------------------------------------
      $Numbers = DB_Count('DomainsOrdersOwners',Array('Where'=>SPrintF('`ContractID` = %u',$Contract['ID'])));
      if(Is_Error($Numbers))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Count += $Numbers;
      #-------------------------------------------------------------------------
      if(HOST_ID == 'joonte.com'){
        #-----------------------------------------------------------------------
        $Numbers = DB_Count('JBsOrdersOwners',Array('Where'=>SPrintF('`ContractID` = %u',$Contract['ID'])));
        if(Is_Error($Numbers))
          return ERROR | @Trigger_Error(500);
      }
      #-------------------------------------------------------------------------
      $Count += $Numbers;
      #-------------------------------------------------------------------------
      if($Count < 1){
        #-----------------------------------------------------------------------
        $IsDelete = DB_Delete('Contracts',Array('ID'=>$Contract['ID']));
        if(Is_Error($IsDelete))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        continue;
      }
      #-------------------------------------------------------------------------
      $Profiles = DB_Select('Profiles','*',Array('Where'=>SPrintF("`UserID` = %u AND `TemplateID` = 'Natural'",$Contract['UserID'])));
      #-------------------------------------------------------------------------
      switch(ValueOf($Profiles)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          #---------------------------------------------------------------------
          $Template = System_XML('profiles/Natural.xml');
          if(Is_Error($Template))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Params = $Template['Attribs'];
          #---------------------------------------------------------------------
          $Attribs = Array();
          #---------------------------------------------------------------------
          foreach(Array_Keys($Params) as $ParamID)
            $Attribs[$ParamID] = $Params[$ParamID]['Value'];
          #---------------------------------------------------------------------
          $Name = Explode(' ',$User['Name']);
          #---------------------------------------------------------------------
          $Attribs['Name'] = Current($Name);
          #---------------------------------------------------------------------
          if(Count($Name) > 1)
            $Attribs['Sourname'] = Next($Name);
          #---------------------------------------------------------------------
          $Attribs['Email'] = $User['Email'];
          #---------------------------------------------------------------------
          $ProfileName = $User['Name'];
          #---------------------------------------------------------------------
          $IProfile = Array('UserID'=>$User['ID'],'TemplateID'=>'Natural','Name'=>$ProfileName,'Attribs'=>$Attribs,'IsDefault'=>FALSE);
          #---------------------------------------------------------------------
          $ProfileID = DB_Insert('Profiles',$IProfile);
          if(Is_Error($ProfileID))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $IsSet = Status_Set('Profiles','OnFilling',$ProfileID);
          #---------------------------------------------------------------------
          switch(ValueOf($IsSet)){
            case 'error':
              return ERROR | @Trigger_Error(500);
            case 'exception':
              return ERROR | @Trigger_Error(400);
            case 'true':
              # No more...
            break;
            default:
              return ERROR | @Trigger_Error(101);
          }
        break;
        case 'array':
          #---------------------------------------------------------------------
          $Profile = Current($Profiles);
          #---------------------------------------------------------------------
          $ProfileID = $Profile['ID'];
          #---------------------------------------------------------------------
          $ProfileName = $Profile['Name'];
        break;
        default:
          return ERROR | @Trigger_Error(101);
      }
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Contracts',Array('Customer'=>$ProfileName,'StatusID'=>'Waiting','TypeID'=>'Natural','ProfileID'=>$ProfileID),Array('ID'=>$Contract['ID']));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Contracts/Build',$Contract['ID']);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
if(Is_Error(DB_Commit($TransactionID)))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>