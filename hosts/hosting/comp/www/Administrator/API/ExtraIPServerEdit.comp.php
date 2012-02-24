<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$ExtraIPServerID = (integer) @$Args['ExtraIPServerID'];
$SystemID        =  (string) @$Args['SystemID'];
$sGroupID  = (integer) @$Args['sGroupID'];
$IsDefault       = (boolean) @$Args['IsDefault'];
$Domain          =  (string) @$Args['Domain'];
$Prefix          =  (string) @$Args['Prefix'];
$Address         =  (string) @$Args['Address'];
$Port            = (integer) @$Args['Port'];
$Protocol        =  (string) @$Args['Protocol'];
$Login           =  (string) @$Args['Login'];
$Password        =  (string) @$Args['Password'];
$IP              =  (string) @$Args['IP'];
$IPsPool         =  (string) @$Args['IPsPool'];
$Theme           =  (string) @$Args['Theme'];
$Language        =  (string) @$Args['Language'];
$Url             =  (string) @$Args['Url'];
$Ns1Name         =  (string) @$Args['Ns1Name'];
$Ns2Name         =  (string) @$Args['Ns2Name'];
$Ns3Name         =  (string) @$Args['Ns3Name'];
$Ns4Name         =  (string) @$Args['Ns4Name'];
$Services        =  (string) @$Args['Services'];
$Notice          =  (string) @$Args['Notice'];
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
if(!IsSet($Config['ExtraIP']['Systems'][$SystemID]))
  return new gException('SYSTEM_NOT_FOUND','Система управления не найдена');
#-------------------------------------------------------------------------------
$Count = DB_Count('ExtraIPsGroups',Array('ID'=>$sGroupID));
if(Is_Error($Count))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
  return new gException('SERVERS_GROUP_NOT_FOUND','Группа серверов не найдена');
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Domain'],$Domain))
  return new gException('WRONG_DOMAIN','Неверный доменный адрес сервера');
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['ID'],$Prefix))
  return new gException('WRONG_PREFIX','Неверный префикс имени аккаунта');
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Domain'],$Address))
  return new gException('WRONG_ADDRESS','Неверный адрес сервера');
#-------------------------------------------------------------------------------
if(!In_Array($Protocol,Array('tcp','ssl')))
  return new gException('WRONG_PROTOCOL','Неверный протокол сервера');
#-------------------------------------------------------------------------------
if(!$Password)
  return new gException('PASSWORD_NOT_FILLED','Не указан пароль от сервера');
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['IP'],$IP))
  return new gException('WRONG_IP','Неверный IP адрес сервера');
#-------------------------------------------------------------------------------
$IPsPool = Trim($IPsPool);
#-------------------------------------------------------------------------------
if(!$IPsPool)
  return new gException('IPS_POOL_IS_EMPY','Пул IP адресов не указан');
#-------------------------------------------------------------------------------
$IPsPool = Explode("\n",$IPsPool);
#-------------------------------------------------------------------------------
$Array = Array();
#-------------------------------------------------------------------------------
foreach($IPsPool as $IPPool){
  #-----------------------------------------------------------------------------
  $IPPool = Trim($IPPool);
  #-----------------------------------------------------------------------------
  if(!$IPPool)
    continue;
  #-----------------------------------------------------------------------------
  if(!Preg_Match($Regulars['IP'],$IPPool))
    return new gException('WRONG_IPS_POOL',SPrintF('Неверный адрес (%s) в пуле IP адресов',$IPPool));
  #-----------------------------------------------------------------------------
  $Array[] = $IPPool;
}
#-------------------------------------------------------------------------------
$IPsPool = Implode("\n",$Array);
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Domain'],$Ns1Name))
  return new gException('WRONG_NS1','Неверный адрес первого сервера имён');
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Domain'],$Ns2Name))
  return new gException('WRONG_NS2','Неверный адрес второго сервера имён');
#-------------------------------------------------------------------------------
if($Ns3Name && !Preg_Match($Regulars['Domain'],$Ns3Name))
  return new gException('WRONG_NS3','Неверный адрес дополнительного сервера имён');
#-------------------------------------------------------------------------------
if($Ns4Name && !Preg_Match($Regulars['Domain'],$Ns4Name))
  return new gException('WRONG_NS4','Неверный адрес расширенного сервера имён');
#-------------------------------------------------------------------------------
$Services = Trim($Services);
#-------------------------------------------------------------------------------
if(!$Services)
  return new gException('SERVICES_IS_EMPY','Сервисы службы мониторинга не указаны');
#-------------------------------------------------------------------------------
$Services = Explode("\n",$Services);
#-------------------------------------------------------------------------------
$Array = Array();
#-------------------------------------------------------------------------------
foreach($Services as $Service){
  #-----------------------------------------------------------------------------
  $Service = Trim($Service);
  #-----------------------------------------------------------------------------
  if(!$Service)
    continue;
  #-----------------------------------------------------------------------------
  if(!Preg_Match('/^[a-zA-Z0-9а-яА-я\-\_]+\=[0-9]+$/',$Service))
    return new gException('WRONG_SERVICE',SPrintF('Неверный сервис (%s), используемый формат ИМЯ=ПОРТ',$Service));
  #-----------------------------------------------------------------------------
  $Array[] = $Service;
}
#-------------------------------------------------------------------------------
$Services = Implode("\n",$Array);
#-------------------------------------------------------------------------------
if($IsDefault){
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('ExtraIPs',Array('IsDefault'=>FALSE),Array('Where'=>SPrintF('`sGroupID` = %u',$sGroupID)));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
}else{
  #-----------------------------------------------------------------------------
  $Count = DB_Count('ExtraIPs',Array('Where'=>SPrintF("`sGroupID` = %u AND `IsDefault` = 'yes'",$sGroupID)));
  if(Is_Error($Count))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  if(!$Count)
    $IsDefault = TRUE;
}
#-------------------------------------------------------------------------------
$IExtraIPServer = Array(
  #-----------------------------------------------------------------------------
  'SystemID'       => $SystemID,
  'sGroupID' => $sGroupID,
  'IsDefault'      => $IsDefault,
  'Domain'         => $Domain,
  'Prefix'         => $Prefix,
  'Address'        => $Address,
  'Port'           => $Port,
  'Protocol'       => $Protocol,
  'Login'          => $Login,
  'IP'             => $IP,
  'IPsPool'        => $IPsPool,
  'Theme'          => $Theme,
  'Language'       => $Language,
  'Url'            => $Url,
  'Ns1Name'        => $Ns1Name,
  'Ns2Name'        => $Ns2Name,
  'Ns3Name'        => $Ns3Name,
  'Ns4Name'        => $Ns4Name,
  'Services'       => $Services,
  'Notice'         => $Notice
);
#-------------------------------------------------------------------------------
if($ExtraIPServerID){
  #-----------------------------------------------------------------------------
  $Count = DB_Count('ExtraIPs',Array('ID'=>$ExtraIPServerID));
  if(Is_Error($Count))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  if(!$Count)
    return new gException('SERVER_NOT_FOUND','Сервер не найден');
  #-----------------------------------------------------------------------------
  if($Password != 'Default')
    $IExtraIPServer['Password'] = $Password;
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('ExtraIPs',$IExtraIPServer,Array('ID'=>$ExtraIPServerID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
}else{
  #-----------------------------------------------------------------------------
  $IExtraIPServer['Password'] = $Password;
  #-----------------------------------------------------------------------------
  $ExtraIPServerID = DB_Insert('ExtraIPs',$IExtraIPServer);
  if(Is_Error($ExtraIPServerID))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
