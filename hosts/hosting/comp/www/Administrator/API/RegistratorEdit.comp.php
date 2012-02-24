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
$RegistratorID =  (string) @$Args['RegistratorID'];
$Name          =  (string) @$Args['Name'];
$TypeID        =  (string) @$Args['TypeID'];
$Comment       =  (string) @$Args['Comment'];
$SortID        = (integer) @$Args['SortID'];
$Address       =  (string) @$Args['Address'];
$Port          = (integer) @$Args['Port'];
$Protocol      =  (string) @$Args['Protocol'];
$PrefixAPI     =  (string) @$Args['PrefixAPI'];
$Login         =  (string) @$Args['Login'];
$Password      =  (string) @$Args['Password'];
$Ns1Name       =  (string) @$Args['Ns1Name'];
$Ns2Name       =  (string) @$Args['Ns2Name'];
$Ns3Name       =  (string) @$Args['Ns3Name'];
$Ns4Name       =  (string) @$Args['Ns4Name'];
$ParentID      = (integer) @$Args['ParentID'];
$PrefixNic     =  (string) @$Args['PrefixNic'];
$PartnerLogin  =  (string) @$Args['PartnerLogin'];
$PartnerContract =(string) @$Args['PartnerContract'];
$JurName       =  (string) @$Args['JurName'];
#-------------------------------------------------------------------------------
if(!$Name)
  return new gException('NAME_IS_EMPTY','Введите название регистратора');
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
if(!IsSet($Config['Domains']['Registrators'][$TypeID]))
  return new gException('REGISTRATOR_NOT_FOUND','Тип регистратора не найден');
#-------------------------------------------------------------------------------
if(!$Comment)
  return new gException('COMMENT_IS_EMPTY','Введите комментарий для регистратора');
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Domain'],$Address))
  return new gException('WRONG_ADDRESS','Неверный адрес');
#-------------------------------------------------------------------------------
if(!In_Array($Protocol,Array('tcp','ssl')))
  return new gException('WRONG_PROTOCOL','Неверный протокол');
#-------------------------------------------------------------------------------
if(!$Login)
  return new gException('LOGIN_IS_EMPTY','Введите логин от регистратора');
#-------------------------------------------------------------------------------
if(!$Password)
  return new gException('PASSORD_IS_EMPTY','Введите пароль от регистратора');
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Domain'],$Ns1Name))
  return new gException('WRONG_NAME_NS1','Неверное имя первого сервера имен');
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Domain'],$Ns2Name))
  return new gException('WRONG_NAME_NS2','Неверное имя второго сервера имен');
#-------------------------------------------------------------------------------
if($Ns3Name && !Preg_Match($Regulars['Domain'],$Ns3Name))
  return new gException('WRONG_NAME_NS3','Неверное имя дополнительного сервера имен');
#-------------------------------------------------------------------------------
if($Ns4Name && !Preg_Match($Regulars['Domain'],$Ns4Name))
  return new gException('WRONG_NAME_NS4','Неверное имя расширенного сервера имен');
#-------------------------------------------------------------------------------
if(!$PrefixNic)
  return new gException('LOGIN_IS_EMPTY','Введите префикс nic-hdl от регистратора');
#-------------------------------------------------------------------------------
if(!$PartnerLogin)
  return new gException('LOGIN_IS_EMPTY','Введите партнерский аккаунт от регистратора');
#-------------------------------------------------------------------------------
if(!$PartnerContract)
  return new gException('LOGIN_IS_EMPTY','Введите номер партнерского договора с регистратором');
#-------------------------------------------------------------------------------  
if(!$JurName)
  return new gException('LOGIN_IS_EMPTY','Введите оф. наименование регистратора');
#-------------------------------------------------------------------------------  
$IRegistrator = Array(
  #-----------------------------------------------------------------------------
  'Name'      => $Name,
  'TypeID'    => $TypeID,
  'Comment'   => $Comment,
  'SortID'    => $SortID,
  'Address'   => $Address,
  'Port'      => $Port,
  'Protocol'  => $Protocol,
  'PrefixAPI' => $PrefixAPI,
  'Login'     => $Login,
  'Ns1Name'   => $Ns1Name,
  'Ns2Name'   => $Ns2Name,
  'Ns3Name'   => $Ns3Name,
  'Ns4Name'   => $Ns4Name,
  'ParentID'  => $ParentID,
  'PrefixNIC' => $PrefixNic,
  'PartnerLogin'    => $PartnerLogin,
  'PartnerContract' => $PartnerContract,
  'JurName'   => $JurName
);
#-------------------------------------------------------------------------------
$Answer = Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
if($RegistratorID){
  #-----------------------------------------------------------------------------
  if($Password != 'Default')
    $IRegistrator['Password'] = $Password;
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('Registrators',$IRegistrator,Array('ID'=>$RegistratorID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
}else{
  #-----------------------------------------------------------------------------
  $IRegistrator['Password'] = $Password;
  Debug("1");
  #-----------------------------------------------------------------------------
  $RegistratorID = DB_Insert('Registrators',$IRegistrator);
  if(Is_Error($RegistratorID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Answer['RegistratorID'] = $RegistratorID;
}
#-------------------------------------------------------------------------------
return $Answer;
#-------------------------------------------------------------------------------

?>
