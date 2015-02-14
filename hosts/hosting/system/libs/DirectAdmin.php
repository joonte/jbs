<?php
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/HTTP.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
Require_Once(SPrintF('%s/others/hosting/IDNA.php',SYSTEM_PATH));
#-------------------------------------------------------------------------------
function DirectAdmin_Logon($Settings,$Params){
  /****************************************************************************/
  $__args_types = Array('array','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  return Array('Url'=>$Settings['Params']['Url'],'Args'=>Array('username'=>$Params['Login'],'password'=>$Params['Password'],'LOGOUT_URL'=>@$_SERVER['HTTP_REFERER']));
}
#-------------------------------------------------------------------------------
function DirectAdmin_Get_Domains($Settings){
  /****************************************************************************/
  $__args_types = Array('array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Basic'    => SPrintF('%s:%s',$Settings['Login'],$Settings['Password'])
  );
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/CMD_API_SHOW_DOMAINS',$HTTP);
  if(Is_Error($Response))
    return new gException('SERVER_CONNECTION_ERROR','Не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Result = $Response['Body'];
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  return Array();
}
#-------------------------------------------------------------------------------
function DirectAdmin_Create($Settings,$Login,$Password,$Domain,$IP,$HostingScheme,$Email,$PersonID = 'Default',$Person = Array()){
  /****************************************************************************/
  $__args_types = Array('array','string','string','string','string','array','string','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Basic'    => SPrintF('%s:%s',$Settings['Login'],$Settings['Password'])
  );
  #-----------------------------------------------------------------------------
  $IsReselling = $HostingScheme['IsReselling'];
  #-----------------------------------------------------------------------------
  $IDNA = new Net_IDNA_php5();
  $Domain = $IDNA->encode($Domain);
  #-----------------------------------------------------------------------------
  $Request = Array(
    #---------------------------------------------------------------------------
    'action'      => 'create',
    'add'         => 'Submit',
    'username'    => $Login,
    'passwd'      => $Password,
    'passwd2'     => $Password,
    'domain'      => $Domain,
    'ip'          => $IP,
    'notify'      => 'NO',
    'email'       => $Email
  );
  #-----------------------------------------------------------------------------
  $PackageID = $HostingScheme['PackageID'];
  #-----------------------------------------------------------------------------
  if($PackageID)
    $Request['package'] = $PackageID;
  else{
    #---------------------------------------------------------------------------
    $Adding = Array(
      #-------------------------------------------------------------------------
      'bandwidth'    => $HostingScheme['QuotaTraffic'],
      'ubandwidth'   => ($HostingScheme['QuotaTraffic'] != -1?'OFF':'ON'),
      'quota'        => $HostingScheme['QuotaDisk'],
      'uquota'       => ($HostingScheme['QuotaDisk'] != -1?'OFF':'ON'),
      'vdomains'     => $HostingScheme['QuotaDomains'],
      'uvdomains'    => ($HostingScheme['QuotaDomains'] != -1?'OFF':'ON'),
      'nsubdomains'  => $HostingScheme['QuotaSubDomains'],
      'unsubdomains' => ($HostingScheme['QuotaSubDomains'] != -1?'OFF':'ON'),
      'nemails'      => $HostingScheme['QuotaEmail'],
      'unemails'     => ($HostingScheme['QuotaEmail'] != -1?'OFF':'ON'),
      'nemailf'      => $HostingScheme['QuotaEmailForwards'],
      'unemailf'     => ($HostingScheme['QuotaEmailForwards'] != -1?'OFF':'ON'),
      'nemailml'     => $HostingScheme['QuotaEmailLists'],
      'unemailml'    => ($HostingScheme['QuotaEmailLists'] != -1?'OFF':'ON'),
      'nemailr'      => $HostingScheme['QuotaEmailAutoResp'],
      'unemailr'     => ($HostingScheme['QuotaEmailAutoResp'] != -1?'OFF':'ON'),
      'mysql'        => $HostingScheme['QuotaDBs'],
      'umysql'       => ($HostingScheme['QuotaDBs'] != -1?'OFF':'ON'),
      'domainptr'    => $HostingScheme['QuotaParkDomains'],
      'udomainptr'   => ($HostingScheme['QuotaParkDomains'] != -1?'OFF':'ON'),
      'ftp'          => $HostingScheme['QuotaFTP'],
      'uftp'         => ($HostingScheme['QuotaFTP'] != -1?'OFF':'ON'),
      #-------------------------------------------------------------------------
      'aftp'        => ($HostingScheme['IsAnonimousFTP']?'ON':'OFF'),
      'ssh'         => ($HostingScheme['IsShellAccess']?'ON':'OFF'),
      'ssl'         => ($HostingScheme['IsSSLAccess']?'ON':'OFF'),
      'cgi'         => ($HostingScheme['IsCGIAccess']?'ON':'OFF'),
      'php'         => ($HostingScheme['IsPHPAccess']?'ON':'OFF'),
      'spamd'       => ($HostingScheme['IsSpamAssasing']?'ON':'OFF'),
      'catchall'    => ($HostingScheme['IsCatchAll']?'ON':'OFF'),
      'sysinfo'     => ($HostingScheme['IsSystemInfo']?'ON':'OFF'),
      'dnscontrol'  => ($HostingScheme['IsDnsControll']?'ON':'OFF')
    );
    #---------------------------------------------------------------------------
    Array_Union($Request,$Adding);
  }
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send($IsReselling?'/CMD_API_ACCOUNT_RESELLER':'/CMD_API_ACCOUNT_USER',$HTTP,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[DirectAdmin_Create]: не удалось осуществить запрос');
  #-----------------------------------------------------------------------------
  $Result = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/User\screated\ssuccessfully/',$Result))
    return TRUE;
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function DirectAdmin_Active($Settings,$Login,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Basic'    => SPrintF('%s:%s',$Settings['Login'],$Settings['Password'])
  );
  #-----------------------------------------------------------------------------
  $Request = Array('suspend'=>'Unsuspend','select0'=>$Login);
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/CMD_SELECT_USERS',$HTTP,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[DirectAdmin_Active]: не удалось осуществить запрос');
  #-----------------------------------------------------------------------------
  $Result = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/(.*)/',$Result))
    return TRUE;
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function DirectAdmin_Suspend($Settings,$Login,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Basic'    => SPrintF('%s:%s',$Settings['Login'],$Settings['Password'])
  );
  #-----------------------------------------------------------------------------
  $Request = Array('suspend'=>'Suspend','select0'=>$Login);
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/CMD_SELECT_USERS',$HTTP,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[DirectAdmin_Suspend]: не удалось осуществить запрос');
  #-----------------------------------------------------------------------------
  $Result = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/(.*)/',$Result))
    return TRUE;
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function DirectAdmin_Delete($Settings,$Login,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Basic'    => SPrintF('%s:%s',$Settings['Login'],$Settings['Password'])
  );
  #-----------------------------------------------------------------------------
  $Request = Array('confirmed'=>'Confirm','delete'=>'yes','select0'=>$Login);
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/CMD_SELECT_USERS',$HTTP,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[DirectAdmin_Delete]: не удалось осуществить запрос');
  #-----------------------------------------------------------------------------
  $Result = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/User\s.*\sRemoved/',$Result))
    return TRUE;
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function DirectAdmin_Scheme_Change($Settings,$Login,$HostingScheme){
  /****************************************************************************/
  $__args_types = Array('array','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Basic'    => SPrintF('%s:%s',$Settings['Login'],$Settings['Password'])
  );
  #-----------------------------------------------------------------------------
  $Request = Array(
    #---------------------------------------------------------------------------
    'action'      => 'customize',
    'user'        => $Login,
    #---------------------------------------------------------------------------
    'bandwidth'   => $HostingScheme['QuotaTraffic'],
    'quota'       => $HostingScheme['QuotaDisk'],
    'vdomains'    => $HostingScheme['QuotaDomains'],
    'nsubdomains' => $HostingScheme['QuotaSubDomains'],
    'nemails'     => $HostingScheme['QuotaEmail'],
    'nemailf'     => $HostingScheme['QuotaEmailForwards'],
    'nemailml'    => $HostingScheme['QuotaEmailLists'],
    'nemailr'     => $HostingScheme['QuotaEmailAutoResp'],
    'mysql'       => $HostingScheme['QuotaDBs'],
    'domainptr'   => $HostingScheme['QuotaParkDomains'],
    'ftp'         => $HostingScheme['QuotaFTP'],
    #---------------------------------------------------------------------------
    'aftp'        => ($HostingScheme['IsAnonimousFTP']?'ON':'OFF'),
    'ssh'         => ($HostingScheme['IsShellAccess']?'ON':'OFF'),
    'ssl'         => ($HostingScheme['IsSSLAccess']?'ON':'OFF'),
    'cgi'         => ($HostingScheme['IsCGIAccess']?'ON':'OFF'),
    'php'         => ($HostingScheme['IsPHPAccess']?'ON':'OFF'),
    'spamd'       => ($HostingScheme['IsSpamAssasing']?'ON':'OFF'),
    'catchall'    => ($HostingScheme['IsCatchAll']?'ON':'OFF'),
    'sysinfo'     => ($HostingScheme['IsSystemInfo']?'ON':'OFF'),
    'dnscontrol'  => ($HostingScheme['IsDnsControll']?'ON':'OFF')
  );
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/CMD_MODIFY_USER',$HTTP,$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[DirectAdmin_Scheme_Change]: не удалось осуществить запрос');
  #-----------------------------------------------------------------------------
  $Result = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/User\sModified/',$Result))
    return TRUE;
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function DirectAdmin_Password_Change($Settings,$Login,$Password,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Basic'    => SPrintF('%s:%s',$Settings['Login'],$Settings['Password'])
  );
  #-----------------------------------------------------------------------------
  $Request = Array(
    #---------------------------------------------------------------------------
    'username' => $Login,
    'passwd'   => $Password,
    'passwd2'  => $Password
  );
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/CMD_USER_PASSWD',$HTTP,Array(),$Request);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[DirectAdmin_Password_Change]: не удалось осуществить запрос');
  #-----------------------------------------------------------------------------
  $Result = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/Password\sChanged/',$Result))
    return TRUE;
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
?>
