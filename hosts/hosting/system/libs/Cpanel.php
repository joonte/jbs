<?php
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/HTTP.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
Require_Once(SPrintF('%s/others/hosting/IDNA.php',SYSTEM_PATH));
#-------------------------------------------------------------------------------
function Cpanel_Logon($Settings,$Params){
  /****************************************************************************/
  $__args_types = Array('array','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  return Array('Url'=>$Settings['Params']['Url'],'Args'=>Array('user'=>$Params['Login'],'pass'=>$Params['Password']));
}
#-------------------------------------------------------------------------------
function Cpanel_Get_Domains($Settings){
  /****************************************************************************/
  $__args_types = Array('array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Params']['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Basic'    => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
    'IsLogging'=> $Settings['Params']['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Request = Array('nohtml'=>'y');
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/scripts2/listsubdomains',$HTTP,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[Cpanel_Get_Domains]: не удалось осуществить запрос');
  #-----------------------------------------------------------------------------
  $Result = Array();
  #-----------------------------------------------------------------------------
  foreach(Explode("\n",$Response['Body']) as $Invoice){
    #---------------------------------------------------------------------------
    if(Preg_Match('/.+\:.+\:.+/',$Invoice)){
      #-------------------------------------------------------------------------
      $Invoice = Explode(':',$Invoice);
      #-------------------------------------------------------------------------
      if(Count($Invoice) != 3)
        continue;
      #-------------------------------------------------------------------------
      $Invoice = Array_Combine(Array('Domain','User','Parkeds'),$Invoice);
      #-------------------------------------------------------------------------
      $Domains = Array($Invoice['Domain']);
      #-------------------------------------------------------------------------
      $Parkeds = Explode('|',$Invoice['Parkeds']);
      #-------------------------------------------------------------------------
      Array_Pop($Parkeds);
      #-------------------------------------------------------------------------
      foreach($Parkeds as $Parked){
        #-----------------------------------------------------------------------
        $Parked = Explode('=',$Parked);
        #-----------------------------------------------------------------------
        if(Count($Parked) != 2)
          continue;
        #-----------------------------------------------------------------------
        $Parked = Array_Combine(Array('Domain1','Domain2'),$Parked);
        #-----------------------------------------------------------------------
        $Domain2 = $Parked['Domain2'];
        #-----------------------------------------------------------------------
        if($Parked['Domain1'] || $Domain2){
          #---------------------------------------------------------------------
          if(StrLen($Domain2) > 1){
            #-------------------------------------------------------------------
            $Domain2 = Explode(',',$Domain2);
            #-------------------------------------------------------------------
            Array_Pop($Domain2);
            #-------------------------------------------------------------------
            foreach($Domain2 as $Domain)
              $Domains[] = $Domain;
          }else
            $Domains[] = $Parked['Domain1'];
        }
      }
      #-------------------------------------------------------------------------
      $Result[$Invoice['User']] = $Domains;
    }
  }
  #-----------------------------------------------------------------------------
  $Request = Array('nohtml'=>'y');
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/scripts2/listparked',$HTTP,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[Cpanel_Get_Domains]: не удалось осуществить запрос');
  #-----------------------------------------------------------------------------
  foreach(Explode("\n",$Response['Body']) as $Invoice){
    #---------------------------------------------------------------------------
    if(Preg_Match('/.+\:.+\:.+/',$Invoice)){
      #-------------------------------------------------------------------------
      $Invoice = Explode(':',$Invoice);
      #-------------------------------------------------------------------------
      if(Count($Invoice) != 3)
        continue;
      #-------------------------------------------------------------------------
      $Invoice = Array_Combine(Array('Domain','User','Parkeds'),$Invoice);
      #-------------------------------------------------------------------------
      $User = $Invoice['User'];
      #-------------------------------------------------------------------------
      if(IsSet($Result[$User]))
        $Result[$User] = Array();
      #-------------------------------------------------------------------------
      $Result[$User][] = $Invoice['Domain'];
    }
  }
  #-----------------------------------------------------------------------------
  $Request = Array('viewall'=>1,'nohtml'=>'y');
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/scripts2/listaccts',$HTTP,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[Cpanel_Get_Domains]: не удалось осуществить запрос');
  #-----------------------------------------------------------------------------
  foreach(Explode("\n",$Response['Body']) as $Invoice){
    #---------------------------------------------------------------------------
    if(Preg_Match('/.+\=.+\,.+\,.+/',$Invoice)){
      #-------------------------------------------------------------------------
      $Invoice = Array_Combine(Array('User','Domain'),Explode('=',SubStr($Invoice,0,StrPos($Invoice,','))));
      #-------------------------------------------------------------------------
      $User = $Invoice['User'];
      #-------------------------------------------------------------------------
      if(IsSet($Result[$User]))
        $Result[$User] = Array();
      #-------------------------------------------------------------------------
      $Result[$User][] = $Invoice['Domain'];
    }
  }
  #-----------------------------------------------------------------------------
  if(!Count($Result))
    return new gException('DOMAINS_NOT_FOUND','Домены не найдены');
  #-----------------------------------------------------------------------------
  return $Result;
}
#-------------------------------------------------------------------------------
function Cpanel_Create($Settings,$Login,$Password,$Domain,$IP,$HostingScheme,$Email,$PersonID = 'Default',$Person = Array()){
  /****************************************************************************/
  $__args_types = Array('array','string','string','string','string','array','string','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Params']['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Basic'    => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
    'IsLogging'=> $Settings['Params']['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $IDNA = new Net_IDNA_php5();
  $Domain = $IDNA->encode($Domain);
  #-----------------------------------------------------------------------------
  $Request = Array(
    #---------------------------------------------------------------------------
    'nohtml'       => 'y',
    'contactemail' => $Email,
    'username'     => $Login,
    'domain'       => $Domain,
    'password'     => $Password,
    'plan'         => $HostingScheme['PackageID'],
    #---------------------------------------------------------------------------
    'quota'        => $HostingScheme['QuotaDisk'],
    'maxsub'       => $HostingScheme['QuotaSubDomains'],
    'maxpark'      => $HostingScheme['QuotaParkDomains'],
    'maxaddon'     => $HostingScheme['QuotaAddonDomains'],
    'maxpop'       => $HostingScheme['QuotaEmail'],
    'maxsql'       => $HostingScheme['QuotaDBs'],
    'maxlst'       => $HostingScheme['QuotaEmailLists'],
    'maxftp'       => $HostingScheme['QuotaFTP'],
    'bwlimit'      => $HostingScheme['QuotaTraffic'],
    #---------------------------------------------------------------------------
    'cpmod'        => $Settings['Params']['Theme'],
    'customip'     => $IP,
  );
  #-----------------------------------------------------------------------------
  if($HostingScheme['IsShellAccess'])
    $Request['hasshell'] = 'yes';
  #-----------------------------------------------------------------------------
  if($HostingScheme['IsCGIAccess'])
    $Request['cgi'] = 'yes';
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/scripts/wwwacct',$HTTP,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[Cpanel_Create]: не удалось осуществить запрос');
  #-----------------------------------------------------------------------------
  $Response = Strip_Tags(Trim($Response['Body']));
  #-----------------------------------------------------------------------------
  if(Preg_Match('/Creation\sComplete/',$Response))
    return TRUE;
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Response);
}
#-------------------------------------------------------------------------------
function Cpanel_Active($Settings,$Login,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Params']['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Basic'    => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
    'IsLogging'=> $Settings['Params']['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Request = $IsReseller?Array('reseller'=>$Login,'resalso'=>1,'un'=>1):Array('user'=>$Login,'nohtml'=>'y');
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send($IsReseller?'/scripts/suspendreseller':'/scripts/remote_unsuspend',$HTTP,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[Cpanel_Activate]: не удалось осуществить запрос');
  #-----------------------------------------------------------------------------
  $Response = Strip_Tags(Trim($Response['Body']));
  #-----------------------------------------------------------------------------
  if(Preg_Match('/(account\sis\snow\sactive|Unsuspending)/',$Response))
    return TRUE;
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER','Неизвестный ответ');
}
#-------------------------------------------------------------------------------
function Cpanel_Suspend($Settings,$Login,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Params']['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Basic'    => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
    'IsLogging'=> $Settings['Params']['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Request = $IsReseller?Array('reseller'=>$Login,'resalso'=>1):Array('user'=>$Login,'nohtml'=>'y');
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send($IsReseller?'/scripts/suspendreseller':'/scripts/remote_suspend',$HTTP,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[Cpanel_Suspend]: не удалось осуществить запрос');
  #-----------------------------------------------------------------------------
  $Response = Strip_Tags(Trim($Response['Body']));
  #-----------------------------------------------------------------------------
  if(Preg_Match('/(account\shas\sbeen\ssuspended|Invoice\sAlready\sSuspended|Complete)/',$Response))
    return TRUE;
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Response);
}
#-------------------------------------------------------------------------------
function Cpanel_Delete($Settings,$Login,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Params']['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Basic'    => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
    'IsLogging'=> $Settings['Params']['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Request = $IsReseller?Array('reseller'=>$Login,'resalso'=>1):Array('user'=>$Login,'nohtml'=>'y');
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send($IsReseller?'/scripts2/killreseller':'/scripts/killacct',$HTTP,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[Cpanel_Delete]: не удалось осуществить запрос');
  #-----------------------------------------------------------------------------
  $Response = Strip_Tags(Trim($Response['Body']));
  #-----------------------------------------------------------------------------
  if(Preg_Match('/(Complete|.*)/',$Response))
    return TRUE;
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Response);
}
#-------------------------------------------------------------------------------
function Cpanel_Scheme_Change($Settings,$Login,$HostingScheme){
  /****************************************************************************/
  $__args_types = Array('array','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Params']['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Basic'    => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
    'IsLogging'=> $Settings['Params']['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Request = Array(
    #---------------------------------------------------------------------------
    'nohtml' => 'y',
    'user'   => $Login,
    'pkg'    => $HostingScheme['PackageID']
  );
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/scripts2/upacct',$HTTP,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[Cpanel_Scheme_Change]: не удалось осуществить запрос');
  #-----------------------------------------------------------------------------
  $Response = Strip_Tags(Trim($Response['Body']));
  #-----------------------------------------------------------------------------
  if(Preg_Match('/Account\sUpgrade\/Downgrade\sComplete/',$Response))
    return TRUE;
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Response);
}
#-------------------------------------------------------------------------------
function Cpanel_Password_Change($Settings,$Login,$Password,$Params){
  /****************************************************************************/
  $__args_types = Array('array','string','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Params']['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Basic'    => SPrintF('%s:%s',$Settings['Login'],$Settings['Password']),
    'IsLogging'=> $Settings['Params']['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Request = Array('user'=>$Login,'pass'=>$Password);
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/xml-api/passwd',$HTTP,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[Cpanel_Password_Change]: не удалось осуществить запрос');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/Password\schanged/',$Response))
    return TRUE;
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Response);
}
#-------------------------------------------------------------------------------
?>
