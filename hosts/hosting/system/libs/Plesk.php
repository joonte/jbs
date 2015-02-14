<?php
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/HTTP.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
Require_Once(SPrintF('%s/others/hosting/IDNA.php',SYSTEM_PATH));
#-------------------------------------------------------------------------------
function Plesk_Logon($Settings,$Params){
  /****************************************************************************/
  $__args_types = Array('array','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  return Array('Url'=>$Settings['Params']['Url'],'Args'=>Array('login_name'=>$Params['Login'],'passwd'=>$Params['Password']));
}
#-------------------------------------------------------------------------------
function Plesk_Get_Domains($Settings){
  /****************************************************************************/
  $__args_types = Array('array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $packet = new Tag('packet',Array('version'=>'1.4.2.0'));
  #-----------------------------------------------------------------------------
  $domain = new Tag('domain',new Tag('get',new Tag('filter'),new Tag('dataset',new Tag('hosting'))));
  #-----------------------------------------------------------------------------
  $packet->AddChild($domain);
  #-----------------------------------------------------------------------------
  $client = new Tag('client',new Tag('get',new Tag('filter'),new Tag('dataset',new Tag('gen_info'))));
  #-----------------------------------------------------------------------------
  $packet->AddChild($client);
  #-----------------------------------------------------------------------------
  $Request = SPrintF("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\" ?>\n%s",$packet->ToXMLString());
  #-----------------------------------------------------------------------------
  $Headers = Array('Content-Type: text/xml',SPrintF('HTTP_AUTH_LOGIN: %s',$Settings['Login']),SPrintF('HTTP_AUTH_PASSWD: %s',$Settings['Password']));
  #-----------------------------------------------------------------------------
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $Settings['Password'],
    'IsLoggin' => FALSE
  );
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/enterprise/control/agent.php',$HTTP,Array(),$Request,$Headers);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[Plesk_Get_Domains]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray('result');
  #-----------------------------------------------------------------------------
  $Packet = $XML['packet'];
  #-----------------------------------------------------------------------------
  $Users = Array();
  #-----------------------------------------------------------------------------
  foreach((array)$Packet['client']['get'] as $Client){
    #-------------------------------------------------------------------------
    if(!IsSet($Client['data']))
      continue;
    #---------------------------------------------------------------------------
    $Login = $Client['data']['gen_info']['login'];
    #---------------------------------------------------------------------------
    $Domains = Array();
    #---------------------------------------------------------------------------
    foreach((array)$Packet['domain']['get'] as $Domain){
      #-------------------------------------------------------------------------
      if(!IsSet($Domain['data']))
        continue;
      #-------------------------------------------------------------------------
      $gen_info = $Domain['data']['gen_info'];
      #-------------------------------------------------------------------------
      if($gen_info['client_id'] == $Client['id'])
        $Domains[] = $gen_info['name'];
    }
    #---------------------------------------------------------------------------
    $Users[$Login] = $Domains;
  }
  #-----------------------------------------------------------------------------
  return $Users;
}
#-------------------------------------------------------------------------------
function Plesk_Create($Settings,$Login,$Password,$Domain,$IP,$HostingScheme,$Email,$PersonID = 'Default',$Person = Array()){
  /****************************************************************************/
  $__args_types = Array('array','string','string','string','string','array','string','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $gen_info = new Tag('gen_info');
  #-----------------------------------------------------------------------------
  switch($PersonID){
    case 'Default':
      #-------------------------------------------------------------------------
      $gen_info->AddChild(new Tag('cname','не указано'));
      $gen_info->AddChild(new Tag('pname',SPrintF('не указано (%s)',$Login)));
      $gen_info->AddChild(new Tag('country','RU'));
      $gen_info->AddChild(new Tag('state','не указано'));
      $gen_info->AddChild(new Tag('city','не указано'));
      $gen_info->AddChild(new Tag('address','не указано'));
      $gen_info->AddChild(new Tag('phone'));
      $gen_info->AddChild(new Tag('fax'));
      $gen_info->AddChild(new Tag('email'));
    break;
    case 'Natural':
      #-------------------------------------------------------------------------
      $gen_info->AddChild(new Tag('cname','не указано'));
      $gen_info->AddChild(new Tag('pname',SPrintF('%s %s (%s)',$Person['Sourname'],$Person['Name'],$Login)));
      $gen_info->AddChild(new Tag('country',$Person['pCountry']));
      $gen_info->AddChild(new Tag('state',$Person['pState']));
      $gen_info->AddChild(new Tag('city',$Person['pCity']));
      $gen_info->AddChild(new Tag('address',$Person['pCity']));
      $gen_info->AddChild(new Tag('phone',$Person['Phone']));
      $gen_info->AddChild(new Tag('fax',$Person['Phone']));
      $gen_info->AddChild(new Tag('email',$Person['Email']));
    break;
    case 'Juridical':
      #-------------------------------------------------------------------------
      $gen_info->AddChild(new Tag('cname',SPrintF('%s "%s" (%s)',$Person['CompanyForm'],$Person['CompanyName'],$Login)));
      $gen_info->AddChild($Person['dSourname'] && $Person['dSourname'] && $Person['dLastname']?new Tag('pname',SPrintF('%s %s %s',$Person['dSourname'],$Person['dName'],$Person['dLastname'])):new Tag('pname',SPrintF('не указано (%s)',$Login)));
      $gen_info->AddChild(new Tag('country',$Person['jCountry']));
      $gen_info->AddChild(new Tag('state',$Person['jState']));
      $gen_info->AddChild(new Tag('city',$Person['jCity']));
      $gen_info->AddChild(new Tag('address',$Person['jCity']));
      $gen_info->AddChild(new Tag('phone',$Person['Phone']));
      $gen_info->AddChild(new Tag('fax',$Person['Phone']));
      $gen_info->AddChild(new Tag('email',$Person['Email']));
    break;
    case 'Individual':
      #-------------------------------------------------------------------------
      $gen_info->AddChild(new Tag('cname',SPrintF('ИП "%s" (%s)',$Person['CompanyName'],$Login)));
      $gen_info->AddChild($Person['dSourname'] && $Person['dSourname'] && $Person['dLastname']?new Tag('pname',SPrintF('%s %s %s',$Person['dSourname'],$Person['dName'],$Person['dLastname'])):new Tag('pname','не указано'));
      $gen_info->AddChild(new Tag('country',$Person['jCountry']));
      $gen_info->AddChild(new Tag('state',$Person['jState']));
      $gen_info->AddChild(new Tag('city',$Person['jCity']));
      $gen_info->AddChild(new Tag('address',$Person['jCity']));
      $gen_info->AddChild(new Tag('phone',$Person['Phone']));
      $gen_info->AddChild(new Tag('fax',$Person['Phone']));
      $gen_info->AddChild(new Tag('email',$Person['Email']));
    break;
    default:
      return ERROR | @Trigger_Error('[Plesk_Create]: тип персоны не определён');
  }
  #-----------------------------------------------------------------------------
  $gen_info->AddChild(new Tag('login',$Login));
  $gen_info->AddChild(new Tag('passwd',$Password));
  $gen_info->AddChild(new Tag('status',0));
  $gen_info->AddChild(new Tag('pcode'));
  #-----------------------------------------------------------------------------
  $add = new Tag('add',$gen_info);
  #-----------------------------------------------------------------------------
  $limits = new Tag('limits');
  #-----------------------------------------------------------------------------
  $QuotaDisk = $HostingScheme['QuotaDisk']*1048576;
  #-----------------------------------------------------------------------------
  $limits->AddChild(new Tag('limit',new Tag('name','disk_space'),new Tag('value',$QuotaDisk > 0?$QuotaDisk:-1)));
  $limits->AddChild(new Tag('limit',new Tag('name','max_dom'),new Tag('value',$HostingScheme['QuotaDomains'])));
  $limits->AddChild(new Tag('limit',new Tag('name','max_subdom'),new Tag('value',$HostingScheme['QuotaSubDomains'])));
  $limits->AddChild(new Tag('limit',new Tag('name','max_dom_aliases'),new Tag('value',$HostingScheme['QuotaParkDomains'])));
  $limits->AddChild(new Tag('limit',new Tag('name','max_db'),new Tag('value',$HostingScheme['QuotaDBs'])));
  $limits->AddChild(new Tag('limit',new Tag('name','max_wu'),new Tag('value',$HostingScheme['QuotaWebUsers'])));
  $limits->AddChild(new Tag('limit',new Tag('name','max_mg'),new Tag('value',$HostingScheme['QuotaEmailGroups'])));
  $limits->AddChild(new Tag('limit',new Tag('name','max_maillists'),new Tag('value',$HostingScheme['QuotaEmailLists'])));
  $limits->AddChild(new Tag('limit',new Tag('name','max_resp'),new Tag('value',$HostingScheme['QuotaEmailAutoResp'])));
  #-----------------------------------------------------------------------------
  $QuotaEmailBox = $HostingScheme['QuotaEmailBox']*1048576;
  #-----------------------------------------------------------------------------
  $limits->AddChild(new Tag('limit',new Tag('name','mbox_quota'),new Tag('value',$QuotaEmailBox > 0?$QuotaEmailBox:-1)));
  $limits->AddChild(new Tag('limit',new Tag('name','max_webapps'),new Tag('value',$HostingScheme['QuotaWebApp'])));
  $limits->AddChild(new Tag('limit',new Tag('name','max_box'),new Tag('value',$HostingScheme['QuotaEmail'])));
  $limits->AddChild(new Tag('limit',new Tag('name','max_redir'),new Tag('value',$HostingScheme['QuotaEmailForwards'])));
  #-----------------------------------------------------------------------------
  $QuotaTraffic = $HostingScheme['QuotaTraffic']*1048576;
  #-----------------------------------------------------------------------------
  $limits->AddChild(new Tag('limit',new Tag('name','max_traffic'),new Tag('value',$QuotaTraffic > 0?$QuotaTraffic:-1)));
  #-----------------------------------------------------------------------------
  $add->AddChild($limits);
  #-----------------------------------------------------------------------------
  $permissions = new Tag('permissions');
  #-----------------------------------------------------------------------------
  $permissions->AddChild(new Tag('permission',new Tag('name','create_domains'),new Tag('value',$HostingScheme['IsCreateDomains']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_phosting'),new Tag('value',$HostingScheme['IsManageHosting']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_quota'),new Tag('value',$HostingScheme['IsManageQuota']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_subdomains'),new Tag('value',$HostingScheme['IsManageSubdomains']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','change_limits'),new Tag('value',$HostingScheme['IsChangeLimits']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_log'),new Tag('value',$HostingScheme['IsManageLog']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_crontab'),new Tag('value',$HostingScheme['IsManageCrontab']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_anonftp'),new Tag('value',$HostingScheme['IsManageAnonFtp']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_webapps'),new Tag('value',$HostingScheme['IsManageWebapps']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_maillists'),new Tag('value',$HostingScheme['IsManageMaillists']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_drweb'),new Tag('value',$HostingScheme['IsManageDrWeb']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','make_dumps'),new Tag('value',$HostingScheme['IsMakeDumps']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','site_builder'),new Tag('value',$HostingScheme['IsSiteBuilder']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','remote_access_interface'),new Tag('value',$HostingScheme['IsRemoteInterface']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_performance'),new Tag('value',$HostingScheme['IsManagePerformance']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','cp_access'),new Tag('value',$HostingScheme['IsCpAccess']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_domain_aliases'),new Tag('value',$HostingScheme['IsManageDomainAliases']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_iis_app_pool'),new Tag('value',$HostingScheme['IsManageIISAppPool']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','dashboard'),new Tag('value',$HostingScheme['IsDashBoard']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','stdgui'),new Tag('value',$HostingScheme['IsStdGIU']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_dashboard'),new Tag('value',$HostingScheme['IsManageDashboard']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_subftp'),new Tag('value',$HostingScheme['IsManageSubFtp']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_spamfilter'),new Tag('value',$HostingScheme['ISManageSpamFilter']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','allow_local_backups'),new Tag('value',$HostingScheme['IsLocalBackups']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','allow_ftp_backups'),new Tag('value',$HostingScheme['IsFtpBackups']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_dns'),new Tag('value',$HostingScheme['IsDnsControll']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_sh_access'),new Tag('value',$HostingScheme['IsShellAccess']?'true':'false')));
  #-----------------------------------------------------------------------------
  $add->AddChild($permissions);
  #-----------------------------------------------------------------------------
  $packet = new Tag('packet',Array('version'=>'1.5.0.0'),new Tag('client',$add));
  #-----------------------------------------------------------------------------
  $Request = SPrintF("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\" ?>\n%s",$packet->ToXMLString());
  #-----------------------------------------------------------------------------
  $Headers = Array('Content-Type: text/xml',SPrintF('HTTP_AUTH_LOGIN: %s',$Settings['Login']),SPrintF('HTTP_AUTH_PASSWD: %s',$Settings['Password']));
  #-----------------------------------------------------------------------------
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $Settings['Password']
  );
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/enterprise/control/agent.php',$HTTP,Array(),$Request,$Headers);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[Plesk_Create]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray();
  #-----------------------------------------------------------------------------
  $Result = $XML['packet']['client']['add']['result'];
  #-----------------------------------------------------------------------------
  switch($Result['status']){
    case 'ok':
     #--------------------------------------------------------------------------
     $ClientID = $Result['id'];
     #--------------------------------------------------------------------------
     $ippool_add_ip = new Tag('ippool_add_ip',new Tag('client_id',$ClientID),new Tag('ip_address',$Settings['IP']));
     #--------------------------------------------------------------------------
     $packet = new Tag('packet',Array('version'=>'1.4.2.0'),new Tag('client',$ippool_add_ip));
     #--------------------------------------------------------------------------
     $Request = SPrintF("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\" ?>\n%s",$packet->ToXMLString());
     #--------------------------------------------------------------------------
     $Response = HTTP_Send('/enterprise/control/agent.php',$HTTP,Array(),$Request,$Headers);
     if(Is_Error($Response))
       return ERROR | @Trigger_Error('[Plesk_Create]: не удалось соедениться с сервером');
     #--------------------------------------------------------------------------
     $Response = Trim($Response['Body']);
     #--------------------------------------------------------------------------
     $XML = String_XML_Parse($Response);
     if(Is_Exception($XML))
       return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
     #--------------------------------------------------------------------------
     $XML = $XML->ToArray();
     #--------------------------------------------------------------------------
     $Result = $XML['packet']['client']['ippool_add_ip']['result'];
     #--------------------------------------------------------------------------
     switch($Result['status']){
       case 'ok':
         #----------------------------------------------------------------------
         $IDNA = new Net_IDNA_php5();
         $Domain = $IDNA->encode($Domain);
         #----------------------------------------------------------------------
         $gen_setup = new Tag('gen_setup');
         $gen_setup->AddChild(new Tag('client_id',$ClientID));
         $gen_setup->AddChild(new Tag('name',$Domain));
         $gen_setup->AddChild(new Tag('ip_address',$IP));
         $gen_setup->AddChild(new Tag('status',0));
         #----------------------------------------------------------------------
         $add = new Tag('add',$gen_setup);
         #----------------------------------------------------------------------
         $vrt_hst = new Tag('vrt_hst');
         $vrt_hst->AddChild(new Tag('ftp_login',$Login));
         $vrt_hst->AddChild(new Tag('ftp_password',$Password));
         $vrt_hst->AddChild(new Tag('php','true'));
         $vrt_hst->AddChild(new Tag('ssi','true'));
         $vrt_hst->AddChild(new Tag('cgi','true'));
         $vrt_hst->AddChild(new Tag('php_safe_mode','true'));
         $vrt_hst->AddChild(new Tag('ip_address',$Settings['IP']));
         #----------------------------------------------------------------------
         $add->AddChild(new Tag('hosting',$vrt_hst));
         #----------------------------------------------------------------------
         $packet = new Tag('packet',Array('version'=>'1.4.2.0'),new Tag('domain',$add));
         #----------------------------------------------------------------------
         $Request = SPrintF("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\" ?>\n%s",$packet->ToXMLString());
         #----------------------------------------------------------------------
         $Response = HTTP_Send('/enterprise/control/agent.php',$HTTP,Array(),$Request,$Headers);
         if(Is_Error($Response))
           return ERROR | @Trigger_Error('[Plesk_Create]: не удалось соедениться с сервером');
         #----------------------------------------------------------------------
         $Response = Trim($Response['Body']);
         #----------------------------------------------------------------------
         $XML = String_XML_Parse($Response);
         if(Is_Exception($XML))
           return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
         #----------------------------------------------------------------------
         $XML = $XML->ToArray();
         #----------------------------------------------------------------------
         $Result = $XML['packet']['domain']['add']['result'];
         #----------------------------------------------------------------------
         switch($Result['status']){
           case 'ok':
             return TRUE;
           case 'error':
            #-------------------------------------------------------------------
            Debug(SPrintF('[%u]=(%s)',$Result['errcode'],$Result['errtext']));
            #-------------------------------------------------------------------
            return new gException('SERVER_ERROR',Trim($Result['errtext']));
           default:
             return new gException('WRONG_ANSWER','Неизвестный ответ');
         }
       case 'error':
        #-----------------------------------------------------------------------
        Debug(SPrintF('[%u]=(%s)',$Result['errcode'],$Result['errtext']));
        #-----------------------------------------------------------------------
        return new gException('SERVER_ERROR',Trim($Result['errtext']));
       break;
       default:
         return new gException('WRONG_ANSWER','Неизвестный ответ');
     }
    case 'error':
     #--------------------------------------------------------------------------
     Debug(SPrintF('[%u]=(%s)',$Result['errcode'],$Result['errtext']));
     #--------------------------------------------------------------------------
     return new gException('SERVER_ERROR',Trim($Result['errtext']));
    default:
      return new gException('WRONG_ANSWER','Неизвестный ответ');
  }
}
#-------------------------------------------------------------------------------
function Plesk_Active($Settings,$Login,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $packet = new Tag('packet',Array('version'=>'1.4.2.0'));
  #-----------------------------------------------------------------------------
  $set = new Tag('set',new Tag('filter',new Tag('login',$Login)),new Tag('values',new Tag('gen_info',new Tag('status','0'))));
  #-----------------------------------------------------------------------------
  $packet->AddChild(new Tag('client',$set));
  #-----------------------------------------------------------------------------
  $Request = SPrintF("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\" ?>\n%s",$packet->ToXMLString());
  #-----------------------------------------------------------------------------
  $Headers = Array('Content-Type: text/xml',SPrintF('HTTP_AUTH_LOGIN: %s',$Settings['Login']),SPrintF('HTTP_AUTH_PASSWD: %s',$Settings['Password']));
  #-----------------------------------------------------------------------------
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $Settings['Password']
  );
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/enterprise/control/agent.php',$HTTP,Array(),$Request,$Headers);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[Plesk_Active]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray();
  #-----------------------------------------------------------------------------
  $Result = $XML['packet']['client']['set']['result'];
  #-----------------------------------------------------------------------------
  switch($Result['status']){
    case 'ok':
      return TRUE;
    case 'error':
     #--------------------------------------------------------------------------
     Debug(SPrintF('[%u]=(%s)',$Result['errcode'],$Result['errtext']));
     #--------------------------------------------------------------------------
     return new gException('SERVER_ERROR',Trim($Result['errtext']));
    default:
      return new gException('WRONG_ANSWER','Неизвестный ответ');
  }
}
#-------------------------------------------------------------------------------
function Plesk_Suspend($Settings,$Login,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $packet = new Tag('packet',Array('version'=>'1.4.2.0'));
  #-----------------------------------------------------------------------------
  $set = new Tag('set',new Tag('filter',new Tag('login',$Login)),new Tag('values',new Tag('gen_info',new Tag('status','16'))));
  #-----------------------------------------------------------------------------
  $packet->AddChild(new Tag('client',$set));
  #-----------------------------------------------------------------------------
  $Request = SPrintF("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\" ?>\n%s",$packet->ToXMLString());
  #-----------------------------------------------------------------------------
  $Headers = Array('Content-Type: text/xml',SPrintF('HTTP_AUTH_LOGIN: %s',$Settings['Login']),SPrintF('HTTP_AUTH_PASSWD: %s',$Settings['Password']));
  #-----------------------------------------------------------------------------
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $Settings['Password']
  );
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/enterprise/control/agent.php',$HTTP,Array(),$Request,$Headers);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[Plesk_Suspend]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray();
  #-----------------------------------------------------------------------------
  $Result = $XML['packet']['client']['set']['result'];
  #-----------------------------------------------------------------------------
  switch($Result['status']){
    case 'ok':
      return TRUE;
    case 'error':
     #--------------------------------------------------------------------------
     Debug(SPrintF('[%u]=(%s)',$Result['errcode'],$Result['errtext']));
     #--------------------------------------------------------------------------
     return new gException('SERVER_ERROR',Trim($Result['errtext']));
    default:
      return new gException('WRONG_ANSWER','Неизвестный ответ');
  }
}
#-------------------------------------------------------------------------------
function Plesk_Scheme_Change($Settings,$Login,$HostingScheme){
  /****************************************************************************/
  $__args_types = Array('array','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $limits = new Tag('limits');
  #-----------------------------------------------------------------------------
  $QuotaDisk = $HostingScheme['QuotaDisk']*1048576;
  #-----------------------------------------------------------------------------
  $limits->AddChild(new Tag('limit',new Tag('name','disk_space'),new Tag('value',$QuotaDisk > 0?$QuotaDisk:-1)));
  $limits->AddChild(new Tag('limit',new Tag('name','max_dom'),new Tag('value',$HostingScheme['QuotaDomains'])));
  $limits->AddChild(new Tag('limit',new Tag('name','max_subdom'),new Tag('value',$HostingScheme['QuotaSubDomains'])));
  $limits->AddChild(new Tag('limit',new Tag('name','max_dom_aliases'),new Tag('value',$HostingScheme['QuotaParkDomains'])));
  $limits->AddChild(new Tag('limit',new Tag('name','max_db'),new Tag('value',$HostingScheme['QuotaDBs'])));
  $limits->AddChild(new Tag('limit',new Tag('name','max_wu'),new Tag('value',$HostingScheme['QuotaWebUsers'])));
  $limits->AddChild(new Tag('limit',new Tag('name','max_mg'),new Tag('value',$HostingScheme['QuotaEmailGroups'])));
  $limits->AddChild(new Tag('limit',new Tag('name','max_maillists'),new Tag('value',$HostingScheme['QuotaEmailLists'])));
  $limits->AddChild(new Tag('limit',new Tag('name','max_resp'),new Tag('value',$HostingScheme['QuotaEmailAutoResp'])));
  #-----------------------------------------------------------------------------
  $QuotaEmailBox = $HostingScheme['QuotaEmailBox']*1048576;
  #-----------------------------------------------------------------------------
  $limits->AddChild(new Tag('limit',new Tag('name','mbox_quota'),new Tag('value',$QuotaEmailBox > $QuotaEmailBox?$QuotaEmailBox:-1)));
  $limits->AddChild(new Tag('limit',new Tag('name','max_webapps'),new Tag('value',$HostingScheme['QuotaWebApp'])));
  $limits->AddChild(new Tag('limit',new Tag('name','max_box'),new Tag('value',$HostingScheme['QuotaEmail'])));
  $limits->AddChild(new Tag('limit',new Tag('name','max_redir'),new Tag('value',$HostingScheme['QuotaEmailForwards'])));
  #-----------------------------------------------------------------------------
  $QuotaTraffic = $HostingScheme['QuotaTraffic']*1048576;
  #-----------------------------------------------------------------------------
  $limits->AddChild(new Tag('limit',new Tag('name','max_traffic'),new Tag('value',$QuotaTraffic > 0?$QuotaTraffic:-1)));
  #-----------------------------------------------------------------------------
  $values = new Tag('values',$limits);
  #-----------------------------------------------------------------------------
  $permissions = new Tag('permissions');
  #-----------------------------------------------------------------------------
  $permissions->AddChild(new Tag('permission',new Tag('name','create_domains'),new Tag('value',$HostingScheme['IsCreateDomains']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_phosting'),new Tag('value',$HostingScheme['IsManageHosting']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_quota'),new Tag('value',$HostingScheme['IsManageQuota']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_subdomains'),new Tag('value',$HostingScheme['IsManageSubdomains']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','change_limits'),new Tag('value',$HostingScheme['IsChangeLimits']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_log'),new Tag('value',$HostingScheme['IsManageLog']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_crontab'),new Tag('value',$HostingScheme['IsManageCrontab']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_anonftp'),new Tag('value',$HostingScheme['IsManageAnonFtp']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_webapps'),new Tag('value',$HostingScheme['IsManageWebapps']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_maillists'),new Tag('value',$HostingScheme['IsManageMaillists']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_drweb'),new Tag('value',$HostingScheme['IsManageDrWeb']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','make_dumps'),new Tag('value',$HostingScheme['IsMakeDumps']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','site_builder'),new Tag('value',$HostingScheme['IsSiteBuilder']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','remote_access_interface'),new Tag('value',$HostingScheme['IsRemoteInterface']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_performance'),new Tag('value',$HostingScheme['IsManagePerformance']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','cp_access'),new Tag('value',$HostingScheme['IsCpAccess']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_domain_aliases'),new Tag('value',$HostingScheme['IsManageDomainAliases']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_iis_app_pool'),new Tag('value',$HostingScheme['IsManageIISAppPool']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','dashboard'),new Tag('value',$HostingScheme['IsDashBoard']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','stdgui'),new Tag('value',$HostingScheme['IsStdGIU']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_dashboard'),new Tag('value',$HostingScheme['IsManageDashboard']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_subftp'),new Tag('value',$HostingScheme['IsManageSubFtp']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_spamfilter'),new Tag('value',$HostingScheme['ISManageSpamFilter']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','allow_local_backups'),new Tag('value',$HostingScheme['IsLocalBackups']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','allow_ftp_backups'),new Tag('value',$HostingScheme['IsFtpBackups']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_dns'),new Tag('value',$HostingScheme['IsDnsControll']?'true':'false')));
  $permissions->AddChild(new Tag('permission',new Tag('name','manage_sh_access'),new Tag('value',$HostingScheme['IsShellAccess']?'true':'false')));
  #-----------------------------------------------------------------------------
  $values->AddChild($permissions);
  #-----------------------------------------------------------------------------
  $set = new Tag('set',new Tag('filter',new Tag('login',$Login)),$values);
  #-----------------------------------------------------------------------------
  $packet = new Tag('packet',Array('version'=>'1.5.0.0'),new Tag('client',$set));
  #-----------------------------------------------------------------------------
  $Request = SPrintF("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\" ?>\n%s",$packet->ToXMLString());
  #-----------------------------------------------------------------------------
  $Headers = Array('Content-Type: text/xml',SPrintF('HTTP_AUTH_LOGIN: %s',$Settings['Login']),SPrintF('HTTP_AUTH_PASSWD: %s',$Settings['Password']));
  #-----------------------------------------------------------------------------
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $Settings['Password']
  );
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/enterprise/control/agent.php',$HTTP,Array(),$Request,$Headers);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[Plesk_Scheme_Change]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray();
  #-----------------------------------------------------------------------------
  $Result = $XML['packet']['client']['set']['result'];
  #-----------------------------------------------------------------------------
  switch($Result['status']){
    case 'ok':
      return TRUE;
    case 'error':
     #--------------------------------------------------------------------------
     Debug(SPrintF('[%u]=(%s)',$Result['errcode'],$Result['errtext']));
     #--------------------------------------------------------------------------
     return new gException('SERVER_ERROR',Trim($Result['errtext']));
    default:
      return new gException('WRONG_ANSWER','Неизвестный ответ');
  }
}
#-------------------------------------------------------------------------------
function Plesk_Delete($Settings,$Login,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $packet = new Tag('packet',Array('version'=>'1.4.2.0'));
  #-----------------------------------------------------------------------------
  $del = new Tag('del',new Tag('filter',new Tag('login',$Login)));
  #-----------------------------------------------------------------------------
  $packet->AddChild(new Tag('client',$del));
  #-----------------------------------------------------------------------------
  $Request = SPrintF("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\" ?>\n%s",$packet->ToXMLString());
  #-----------------------------------------------------------------------------
  $Headers = Array('Content-Type: text/xml',SPrintF('HTTP_AUTH_LOGIN: %s',$Settings['Login']),SPrintF('HTTP_AUTH_PASSWD: %s',$Settings['Password']));
  #-----------------------------------------------------------------------------
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $Settings['Password']
  );
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/enterprise/control/agent.php',$HTTP,Array(),$Request,$Headers);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[Plesk_Delete]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray();
  #-----------------------------------------------------------------------------
  $Result = $XML['packet']['client']['del']['result'];
  #-----------------------------------------------------------------------------
  switch($Result['status']){
    case 'ok':
      return TRUE;
    case 'error':
     #--------------------------------------------------------------------------
     Debug(SPrintF('[%u]=(%s)',$Result['errcode'],$Result['errtext']));
     #--------------------------------------------------------------------------
     return new gException('SERVER_ERROR',Trim($Result['errtext']));
    default:
      return new gException('WRONG_ANSWER','Неизвестный ответ');
  }
}
#-------------------------------------------------------------------------------
function Plesk_Password_Change($Settings,$Login,$Password,$IsReseller = FALSE){
  /****************************************************************************/
  $__args_types = Array('array','string','string','boolean');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $packet = new Tag('packet',Array('version'=>'1.4.2.0'));
  #-----------------------------------------------------------------------------
  $set = new Tag('set',new Tag('filter',new Tag('login',$Login)),new Tag('values',new Tag('gen_info',new Tag('passwd',$Password))));
  #-----------------------------------------------------------------------------
  $packet->AddChild(new Tag('client',$set));
  #-----------------------------------------------------------------------------
  $Request = SPrintF("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\" ?>\n%s",$packet->ToXMLString());
  #-----------------------------------------------------------------------------
  $Headers = Array('Content-Type: text/xml',SPrintF('HTTP_AUTH_LOGIN: %s',$Settings['Login']),SPrintF('HTTP_AUTH_PASSWD: %s',$Settings['Password']));
  #-----------------------------------------------------------------------------
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['IP'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Hidden'   => $Settings['Password']
  );
  #-----------------------------------------------------------------------------
  $Response = HTTP_Send('/enterprise/control/agent.php',$HTTP,Array(),$Request,$Headers);
  if(Is_Error($Response))
    return ERROR | @Trigger_Error('[Plesk_Password_Change]: не удалось соедениться с сервером');
  #-----------------------------------------------------------------------------
  $Response = Trim($Response['Body']);
  #-----------------------------------------------------------------------------
  $XML = String_XML_Parse($Response);
  if(Is_Exception($XML))
    return new gException('WRONG_SERVER_ANSWER',$Response,$XML);
  #-----------------------------------------------------------------------------
  $XML = $XML->ToArray();
  #-----------------------------------------------------------------------------
  $Result = $XML['packet']['client']['set']['result'];
  #-----------------------------------------------------------------------------
  switch($Result['status']){
    case 'ok':
      return TRUE;
    case 'error':
     #--------------------------------------------------------------------------
     Debug(SPrintF('[%u]=(%s)',$Result['errcode'],$Result['errtext']));
     #--------------------------------------------------------------------------
     return new gException('SERVER_ERROR',Trim($Result['errtext']));
    default:
      return new gException('WRONG_ANSWER','Неизвестный ответ');
  }
}
#-------------------------------------------------------------------------------
?>
