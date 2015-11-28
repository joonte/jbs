<?php
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/HTTP.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
function NauNet_Domain_Register($Settings,$DomainName,$DomainZone,$Years,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP,$Ns3Name,$Ns3IP,$Ns4Name,$Ns4IP,$IsPrivateWhoIs,$ContractID = '',$PepsonID = 'Default',$Person = Array()){
  /****************************************************************************/
  $__args_types = Array('array','string','string','integer','string','string','string','string','string','string','string','string','boolean','string','string','array');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'KOI8-R',
    'IsLogging'=> $Settings['Params']['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Domain = SPrintF('%s.%s',$DomainName,$DomainZone);
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'action' => 'NEW',
    'login'  => $Settings['Login'],
    'passwd' => $Settings['Password'],
    'domain' => $Domain,
    'mode'   => 'async'
  );
  #-----------------------------------------------------------------------------
  $Query['private-whois'] = ($IsPrivateWhoIs?'yes':'no');
  #-----------------------------------------------------------------------------
  if($ContractID)
    $Query['contact-login'] = $ContractID;
  else{
    #---------------------------------------------------------------------------
    switch($PepsonID){
      case 'Natural':
        #---------------------------------------------------------------------
        $Query['person']     = SPrintF('%s %s %s',Translit($Person['Name']),Mb_SubStr(Translit($Person['Lastname']),0,1),Translit($Person['Sourname']));
        $Query['person-r']   = SPrintF('%s %s %s',$Person['Sourname'],$Person['Name'],$Person['Lastname']);
        $Query['passport']   = SPrintF('%s %s выдан %s %s',$Person['PasportLine'],$Person['PasportNum'],$Person['PasportWhom'],$Person['PasportDate']);
        $Query['residence']  = SPrintF('%s, %s, %s, %s %s',$Person['pIndex'],$Person['pState'],$Person['pCity'],$Person['pType'],$Person['pAddress']);
        $Query['birth-date'] = $Person['BornDate'];
        $Query['address-r']  = SPrintF('%s, %s, %s, %s %s, %s',$Person['pIndex'],$Person['pState'],$Person['pCity'],$Person['pType'],$Person['pAddress'],$Person['pRecipient']);
        $Query['p-addr']     = SPrintF('%s, %s, %s, %s %s, %s',$Person['pIndex'],$Person['pState'],$Person['pCity'],$Person['pType'],$Person['pAddress'],$Person['pRecipient']);
        $Query['phone']      = $Person['Phone'];
	$Query['sms-phone']  = $Person['CellPhone'];
        $Query['fax-no']     = $Person['Fax'];
        $Query['e-mail']     = $Person['Email'];
      break;
      case 'Juridical':
        #---------------------------------------------------------------------
        $Query['org']           = SPrintF('%s %s',Translit($Person['CompanyName']),Translit($Person['CompanyForm']));
        $Query['org-r']         = SPrintF('%s "%s"',$Person['CompanyForm'],$Person['CompanyName']);
        $Query['code']          = $Person['Inn'];
        $Query['kpp']           = $Person['Kpp'];
        $Query['address-r']     = SPrintF('%s, %s, %s, %s %s',$Person['jIndex'],$Person['jState'],$Person['jCity'],$Person['jType'],$Person['jAddress']);
        $Query['p-addr']        = SPrintF('%s, %s, %s, %s %s, %s "%s"',$Person['pIndex'],$Person['pState'],$Person['pCity'],$Person['pType'],$Person['pAddress'],$Person['CompanyForm'],$Person['CompanyName']);
        $Query['ogrn']          = $Person['Ogrn'];
        $Query['regdocuments']  = '';
        $Query['phone']         = $Person['Phone'];
	$Query['sms-phone']     = $Person['CellPhone'];
        $Query['fax-no']        = $Person['Fax'];
        $Query['e-mail']        = $Person['Email'];
      break;
      default:
        return new gException('WRONG_PROFILE_ID','Неверный идентификатор профиля');
    }
  }
  #-----------------------------------------------------------------------------
  $NsServers = Array();
  #-----------------------------------------------------------------------------
  $NsServers[] = ($Ns1IP?SPrintF('%s %s',$Ns1Name,$Ns1IP):$Ns1Name);
  $NsServers[] = ($Ns2IP?SPrintF('%s %s',$Ns1Name,$Ns2IP):$Ns2Name);
  #-----------------------------------------------------------------------------
  if($Ns3Name){
    #---------------------------------------------------------------------------
    $NsServers[] = ($Ns3IP?SPrintF('%s %s',$Ns3Name,$Ns3IP):$Ns3Name);
  }
  #-----------------------------------------------------------------------------
  if($Ns4Name){
    #---------------------------------------------------------------------------
    $NsServers[] = ($Ns4IP?SPrintF('%s %s',$Ns4Name,$Ns4IP):$Ns4Name);
  }
  #-----------------------------------------------------------------------------
  $Query['nserver'] = $NsServers;
  #-----------------------------------------------------------------------------
  $Result = HTTP_Send('/c/registrar',$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[NauNet_Domain_Register]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/NEW:\s([0-9]+).+in\sprogress/',$Result,$TicketID)){
    #---------------------------------------------------------------------------
    $Result = Array('TicketID'=>Next($TicketID));
    #---------------------------------------------------------------------------
    if(!$ContractID)
      $Result['ContractID'] = SPrintF('admin@%s',$Domain);
    #---------------------------------------------------------------------------
    return $Result;
  }
  #-----------------------------------------------------------------------------
  if(Preg_Match('/NEW:\s[0-9]+\s[a-z_]+\scanceled/',$Result))
    return new gException('CANCELED','От вас поступил запрос на отмену заявки');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/NEW:\s[0-9]+\s[a-z_]+\sabuse_domain_name/',$Result))
    return new gException('ABUSE_DOMAIN_NAME','Доменное имя противоречит принципам морали и т.п. Для уточнения деталей обратитесь к регистратору.');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/NEW:\s[0-9]+\sregistrar_error\sdocuments_wait/',$Result))
    return new gException('DOCUMENTS_WAIT','Истек срок ожидания документов для подтверждения (30 дней). Обратитесь к регистратору.');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/NEW:\s[0-9]+\s[a-z_]+\swrong_data/',$Result))
    return new gException('WRONG_DATA','Для выполнения заявки недостаточно данных');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/NEW:\s[0-9]+\s[a-z_]+\srequest_already_exists/',$Result))
    return new gException('REQUEST_ALREADY_EXISTS','Существует незавершенный запрос на регистрацию данного домена');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/NEW:\s[0-9]+\s[a-z_]+\swrong_client_id/',$Result))
    return new gException('WRONG_CLIENT_ID','Недостаточно данных для регистрации в cистеме управления услугами (наименование организации/ФИО иИНН/серия-номер паспорта)');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/NEW:\s[0-9]+\s[a-z_]+\swrong_domain/',$Result))
    return new gException('WRONG_DOMAIN','Название домена не указано или указано неверно');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/NEW:\s[0-9]+\s[a-z_]+\swrong_e-mail/',$Result))
    return new gException('WRONG_E_MAIL','Не удалось определить email администратора ни из самой заявки, ни в значениях по умолчанию');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/NEW:\s[0-9]+\s[a-z_]+\swrong_key_/',$Result))
    return new gException('WRONG_KEY','Ошибка синтаксиса в каком-либо поле');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/NEW:\s[0-9]+\s[a-z_]+\swrong_password_trycounter/',$Result))
    return new gException('WRONG_PASSWORD_TRYCOUNTER','Кол-во попыток ввода пароля превысило допустимое');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/NEW:\s[0-9]+\s[a-z_]+\swrong_password/',$Result))
    return new gException('WRONG_PASSWORD','Неверный пароль доступа к регистратору');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/NEW:\s[0-9]+\s[a-z_]+\swrong_ident/',$Result))
    return new gException('WRONG_IDENT','Ошибка идентификации пользователя');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/NEW:\s[0-9]+\s[a-z_]+\sunknown_login/',$Result))
    return new gException('WRONG_LOGIN','Указано неверное имя пользователя');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/NEW:\s[0-9]+\smoney_wait/',$Result))
    return new gException('MONEY_WAIT','Заявка будет обработана после поступления на счет денежных средств. Пополните баланс.');
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function NauNet_Domain_Prolong($Settings,$DomainName,$DomainZone,$Years,$ContractID,$DomainID){
  /****************************************************************************/
  $__args_types = Array('array','string','string','integer','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'KOI8-R',
    'IsLogging'=> $Settings['Params']['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'action' => 'PROLONG',
    'login'  => $Settings['Login'],
    'passwd' => $Settings['Password'],
    'domain' => SPrintF('%s.%s',$DomainName,$DomainZone),
    'mode'   => 'async'
  );
  #-----------------------------------------------------------------------------
  $Result = HTTP_Send('/c/registrar',$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[NauNet_Domain_Prolong]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/PROLONG:\s([0-9]+).+in\sprogress/',$Result,$TicketID))
    return Array('TicketID'=>Next($TicketID));
  #-----------------------------------------------------------------------------
  if(Preg_Match('/PROLONG:\s[0-9]+\s[a-z_]+\scanceled/',$Result))
    return new gException('CANCELED','От вас поступил запрос на отмену заявки');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/PROLONG:\s[0-9]+\s[a-z_]+\swrong_date/',$Result))
    return new gException('WRONG_DATE','Некорректная дата выполнения заявки. Заявка на продление подана слишком рано.');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/PROLONG:\s[0-9]+\sregistrar_error\sdocuments_wait/',$Result))
    return new gException('DOCUMENTS_WAIT','Истек срок ожидания документов для подтверждения (30 дней). Обратитесь к регистратору.');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/PROLONG:\s[0-9]+\s[a-z_]+\swrong_data/',$Result))
    return new gException('WRONG_DATA','Для выполнения заявки недостаточно данных');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/PROLONG:\s[0-9]+\s[a-z_]+\swrong_domain/',$Result))
    return new gException('WRONG_DOMAIN','Название домена не указано или указано неверно');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/PROLONG:\s[0-9]+\s[a-z_]+\sdomain_already_exist/',$Result))
    return new gException('DOMAIN_ALREADY_EXISTS','Запрошена регистрация уже существующего домена');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/PROLONG:\s[0-9]+\s[a-z_]+\srequest_already_exists/',$Result))
    return new gException('REQUEST_ALREADY_EXISTS','Существует незавершенный запрос на продление данного домена');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/PROLONG:\s[0-9]+\s[a-z_]+\swrong_date/',$Result))
    return new gException('WRONG_DATE','Заявка на продление домена подана слишком рано');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/PROLONG:\s[0-9]+\s[a-z_]+\swrong_key_/',$Result))
    return new gException('WRONG_KEY','Ошибка синтаксиса в каком-либо поле');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/PROLONG:\s[0-9]+\s[a-z_]+\sunknown_domain/',$Result))
    return new gException('UNKNOWN_DOMAIN','Заявка по домену, который не зарегистрирован у данного регистратора');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/PROLONG:\s[0-9]+\s[a-z_]+\swrong_password_trycounter/',$Result))
    return new gException('WRONG_PASSWORD_TRYCOUNTER','Кол-во попыток ввода пароля превысило допустимое');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/PROLONG:\s[0-9]+\s[a-z_]+\swrong_password/',$Result))
    return new gException('WRONG_PASSWORD','Неверный пароль доступа к регистратору');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/PROLONG:\s[0-9]+\s[a-z_]+\swrong_ident/',$Result))
    return new gException('WRONG_IDENT','Ошибка идентификации пользователя');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/PROLONG:\s[0-9]+\s[a-z_]+\sunknown_login/',$Result))
    return new gException('WRONG_LOGIN','Указано неверное имя пользователя');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/PROLONG:\s[0-9]+\smoney_wait/',$Result))
    return new gException('MONEY_WAIT','Заявка будет обработана после поступления на счет денежных средств. Пополните баланс.');
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function NauNet_Domain_Ns_Change($Settings,$DomainName,$DomainZone,$ContractID,$DomainID,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'KOI8-R',
    'IsLogging'=> $Settings['Params']['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'action' => 'UPDATE',
    'login'  => $Settings['Login'],
    'passwd' => $Settings['Password'],
    'domain' => SPrintF('%s.%s',$DomainName,$DomainZone),
    'mode'   => 'async'
  );
  #-----------------------------------------------------------------------------
  $Query['nserver'] = Array($Ns1Name,$Ns2Name);
  #-----------------------------------------------------------------------------
  if($Ns1IP && $Ns2IP)
    $Query['nserver'] = Array(SPrintF('%s %s',$Query['nserver'],$Ns1IP),SPrintF('%s %s',$Query['nserver'],$Ns2IP));
  #-----------------------------------------------------------------------------
  $Result = HTTP_Send('/c/registrar',$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[NauNet_Domain_Ns_Change]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/UPDATE:\s([0-9]+).+in\sprogress/',$Result,$TicketID))
    return Array('TicketID'=>Next($TicketID));
  #-----------------------------------------------------------------------------
  if(Preg_Match('/UPDATE:\s[0-9]+\s[a-z_]+\swrong_dns/',$Result))
    return new gException('WRONG_DNS','Ошибка в ответах ваших серверов DNS');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/UPDATE:\s[0-9]+\s[a-z_]+\scanceled/',$Result))
    return new gException('CANCELED','От вас поступил запрос на отмену заявки');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/UPDATE:\s[0-9]+\sregistrar_error\sdocuments_wait/',$Result))
    return new gException('DOCUMENTS_WAIT','Истек срок ожидания документов для подтверждения (30 дней). Обратитесь к регистратору.');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/UPDATE:\s[0-9]+\s[a-z_]+\swrong_data/',$Result))
    return new gException('WRONG_DATA','Для выполнения заявки недостаточно данных');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/UPDATE:\s[0-9]+\s[a-z_]+\swrong_domain/',$Result))
    return new gException('WRONG_DOMAIN','Название домена не указано или указано неверно');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/UPDATE:\s[0-9]+\s[a-z_]+\swrong_key_/',$Result))
    return new gException('WRONG_KEY','Ошибка синтаксиса в каком-либо поле');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/UPDATE:\s[0-9]+\s[a-z_]+\sunknown_domain/',$Result))
    return new gException('UNKNOWN_DOMAIN','Заявка по домену, который не зарегистрирован у данного регистратора');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/UPDATE:\s[0-9]+\s[a-z_]+\swrong_password_trycounter/',$Result))
    return new gException('WRONG_PASSWORD_TRYCOUNTER','Кол-во попыток ввода пароля превысило допустимое');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/UPDATE:\s[0-9]+\s[a-z_]+\supdate_already_exist/',$Result))
    return new gException('UPDATE_ALREADY_EXIST','Присланные в UPDATE изменения state/nserver не отличаются от текущих установок домена');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/UPDATE:\s[0-9]+\s[a-z_]+\sblocked_period/',$Result))
    return new gException('BLOCKED_PERIOD','Для домена, находящегося в периоде блокировки (месяц после окончания регистрации), была подана заявка, отличная от PROLONG');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/UPDATE:\s[0-9]+\s[a-z_]+\swrong_password/',$Result))
    return new gException('WRONG_PASSWORD','Неверный пароль доступа к регистратору');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/UPDATE:\s[0-9]+\s[a-z_]+\swrong_ident/',$Result))
    return new gException('WRONG_IDENT','Ошибка идентификации пользователя');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/UPDATE:\s[0-9]+\s[a-z_]+\sunknown_login/',$Result))
    return new gException('WRONG_LOGIN','Указано неверное имя пользователя');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/UPDATE:\s[0-9]+\smoney_wait/',$Result))
    return new gException('MONEY_WAIT','Заявка будет обработана после поступления на счет денежных средств. Пополните баланс');
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function NauNet_Check_Task($Settings,$TicketID){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $HTTP = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'KOI8-R',
    'IsLogging'=> $Settings['Params']['IsLogging']
  );
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'action'    => 'REQUEST',
    'login'     => $Settings['Login'],
    'passwd'    => $Settings['Password'],
    'requestid' => $TicketID
  );
  #-----------------------------------------------------------------------------
  $Result = HTTP_Send('/c/registrar',$HTTP,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[NauNet_Check_Task]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\sdone/',$Result))
    return Array('DomainID'=>0);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\s(registrar_error|dns_check|tc_wait|tc_response|checked|in_progress)/',$Result))
    return FALSE;
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\s[a-z_]+\scanceled/',$Result))
    return new gException('CANCELED','От вас поступил запрос на отмену заявки');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\s[a-z_]+\sabuse_domain_name/',$Result))
    return new gException('ABUSE_DOMAIN_NAME','Доменное имя противоречит принципам морали и т.п. Для уточнения деталей обратитесь к регистратору');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\sregistrar_error\sdocuments_wait/',$Result))
    return new gException('DOCUMENTS_WAIT','Истек срок ожидания документов для подтверждения (30 дней). Обратитесь к регистратору.');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\sregistrar_error\smoney_wait/',$Result))
    return new gException('DOCUMENTS_WAIT','Истек срок ожидания оплаты заявки (30 дней)');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\s[a-z_]+\swrong_data/',$Result))
    return new gException('WRONG_DATA','Для выполнения заявки недостаточно данных');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\s[a-z_]+\swrong_domain/',$Result))
    return new gException('WRONG_DOMAIN','Название домена не указано или указано неверно');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\s[a-z_]+\sdomain_already_exist/',$Result))
    return new gException('DOMAIN_ALREADY_EXISTS','Запрошена регистрация уже существующего домена');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\s[a-z_]+\sdomain_deleted/',$Result))
    return new gException('DOMAIN_DELETED','Ранее поступила заявка DELETE, по которой домен был удален. Выполение всех прочих заявок по этому домену отменено');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\s[a-z_]+\swrong_e-mail/',$Result))
    return new gException('WRONG_E_MAIL','Не удалось определить email администратора ни из самой заявки, ни в значениях по умолчанию');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\s[a-z_]+\swrong_date/',$Result))
    return new gException('WRONG_DATE','Заявка на продление домена подана слишком рано');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\s[a-z_]+\sblocked_period/',$Result))
    return new gException('BLOCKED_PERIOD','Для домена, находящегося в периоде блокировки (месяц после окончания регистрации), была подана заявка, отличная от PROLONG');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\s[a-z_]+\sdelete_period/',$Result))
    return new gException('DELETE_PERIOD','Для домена, находящегося в периоде блокировки (месяц после окончания регистрации), была подана заявка, отличная от PROLONG');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\s[a-z_]+\swrong_key_/',$Result))
    return new gException('WRONG_KEY','Ошибка синтаксиса в каком-либо поле');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\s[a-z_]+\swrong_dns/',$Result))
    return new gException('WRONG_DNS','Ошибка в ответах ваших серверов DNS');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\s[a-z_]+\sunknown_domain/',$Result))
    return new gException('UNKNOWN_DOMAIN','Заявка по домену, который не зарегистрирован у данного регистратора');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\s[a-z_]+\swrong_password_trycounter/',$Result))
    return new gException('WRONG_PASSWORD_TRYCOUNTER','Кол-во попыток ввода пароля превысило допустимое');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\s[a-z_]+\swrong_password/',$Result))
    return new gException('WRONG_PASSWORD','Неверный пароль доступа к регистратору');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\s[a-z_]+\swrong_ident/',$Result))
    return new gException('WRONG_IDENT','Ошибка идентификации пользователя');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\s[a-z_]+\sunknown_login/',$Result))
    return new gException('WRONG_LOGIN','Указано неверное имя пользователя');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/:\s[0-9]+\smoney_wait/',$Result))
    return new gException('MONEY_WAIT','Заявка будет обработана после поступления на счет денежных средств. Пополните баланс.');
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------

# added by lissyara, for JBS-1132, 2015-11-27 in 20:32 MSK
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function NauNet_Domain_GetPrice($Settings,$DomainName,$DomainZone){
	#-------------------------------------------------------------------------------
	return Array();
	#-------------------------------------------------------------------------------
}






?>
