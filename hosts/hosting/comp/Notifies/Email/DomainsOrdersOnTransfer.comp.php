<?php


#-------------------------------------------------------------------------------
/** @author Sergey Sedov (for www.host-food.ru) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Replace');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Replace['DomainOrder'] = $Replace['Row'];
#-------------------------------------------------------------------------------
$DomainOrder = &$Replace['DomainOrder'];
#-------------------------------------------------------------------------------
$StatusDate = Comp_Load('Formats/Date/Standard',$DomainOrder['UpdateDate']);
if(Is_Error($StatusDate))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DomainOrder['StatusDate'] = $StatusDate;
#-------------------------------------------------------------------------------
$Number = Comp_Load('Formats/Order/Number',$DomainOrder['OrderID']);
if(Is_Error($Number))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DomainOrder['Number'] = $Number;
#-------------------------------------------------------------------------------
# Достаем registrar и его префикс
if(Preg_Match('/registrar:\s+((\w+|-)+)/',$DomainOrder['WhoIs'],$String)){
  $Registrar = Next($String);
  if(Preg_Match('/((\w+|-)+)-REG/',$Registrar,$String))
    $PrefixRegistrar = Next($String);
}
# Получаем параметры для формирования Event
$Where = SPrintF('`OrderID`=%s',$DomainOrder['OrderID']);
$UserID = DB_Select('DomainsOrdersOwners','UserID as ID',Array('UNIQ','Where'=>$Where));
$SchemeName = DB_Select('DomainsSchemes','Name as SchemeName', Array('UNIQ','ID'=>$DomainOrder['SchemeID']));
$DomainOrder['SchemeName'] = $SchemeName['SchemeName'];
#-------------------------------------------------------------------------------
if(IsSet($Registrar) && IsSet($PrefixRegistrar)){
  # Получаем параметры регистратора к которому осуществляется трансфер
  $RegistratorID = DB_Select('DomainsSchemes','RegistratorID as ID',Array('UNIQ','ID'=>$DomainOrder['SchemeID']));
  $Settings = DB_Select('Registrators','*',Array('UNIQ','ID'=>$RegistratorID['ID']));
  #-----------------------------------------------------------------------------
  Debug("[Notifies/Email/DomainsOrdersOnTransfer]: Registrators[TypeID] - " . $Settings['TypeID']);
  Debug("[Notifies/Email/DomainsOrdersOnTransfer]: Settings[PrefixNic] - " . $Settings['PrefixNic']);
  Debug("[Notifies/Email/DomainsOrdersOnTransfer]: Registrar - " . $Registrar);
  Debug("[Notifies/Email/DomainsOrdersOnTransfer]: PrefixRegistrar - " . $PrefixRegistrar);
  #-----------------------------------------------------------------------------
  # Проверяем является ли регистрар нашим регистратором
  if($PrefixRegistrar == $Settings['PrefixNic']){
    #---------------------------------------------------------------------------
    Debug("[Notifies/Email/DomainsOrdersOnTransfer]: IsOurRegistrar - TRUE");
    Debug("[Notifies/Email/DomainsOrdersOnTransfer]: Инструкция по трансферу в пределах регистратора");
    #---------------------------------------------------------------------------
    # Формируем инструкцию по трансферу в пределах регистратора
    $TransferMsg = <<<EOT
Сейчас Ваш домен находится на обслуживании у регистратора %s, партнером которого является наш хостинг.\n
Для переноса его к нам, администратор домена (физ. или юр. лицо, на которое производилась регистрация) должен направить официальную просьбу регистратору о переносе домена под управление партнера "%s" (договор №%s).\n
Если Вы производили регистрацию домена через какого-либо партнера регистратора %s (возможно это был Ваш старый хостинг-провайдер), то достаточно обратиться к нему с просьбой перенести Ваш домен под наш партнерский аккаунт (данные от него указаны выше). В этом случае Вам не нужно будет оформлять никаких документов.
EOT;
    $TransferMsg = SPrintF($TransferMsg, $Settings['TypeID'], $Settings['PartnerLogin'], $Settings['PartnerContract'], $Settings['TypeID']);
    # Достаем статью с информацией о шаблонах документов и контактами регистратора
    $Where = SPrintF('`Partition`=\'Registrators/%s/internal\'',$PrefixRegistrar);
    $Clause = DB_Select('Clauses','*',Array('UNIQ','Where'=>$Where));
    switch(ValueOf($Clause)){
      case 'array':
        $TransferMsg .= <<<EOT
\n\nНиже приведена информация по оформления писем Вашему регистратору и его контактные данные. (ВНИМАНИЕ! Данная информация носит справочный характер и возможно требует уточнения у Вашего регистратора.):\n\n
EOT;
        $TransferDoc = trim(Strip_Tags($Clause['Text']));
        break;
      default:
        #-----------------------------------------------------------------------
        Debug(SPrintF('[Notifies/Email/DomainsOrdersOnTransfer]: Статья не найдена. Ожидалась Registrators/%s/internal',$PrefixRegistrar));
        #-----------------------------------------------------------------------
        $TransferDoc = "\n\nДля получения информации об оформлении писем Вашему текущему регистратору и его контактах перейдите на его сайт.";
        #-----------------------------------------------------------------------
        # Уведомление об ошибке статьи
	$Event = Array(
			'UserID'	=> $UserID['ID'],
			'PriorityID'	=> 'Error',
			'Text'		=> SPrintF('Статья по переносу домена не найдена. Ожидалась Registrators/%s/internal',$PrefixRegistrar),
			'IsReaded'	=> FALSE
	              );
        $Event = Comp_Load('Events/EventInsert',$Event);
        if(!$Event)
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
    }
      $TransferMsg .= $TransferDoc;
  }
  else{
    #---------------------------------------------------------------------------
    Debug("[Notifies/Email/DomainsOrdersOnTransfer]: Инструкция по трансферу от стороннего регистратора");
    #---------------------------------------------------------------------------
    # Формируем постфикс идентификатора
    switch ($SchemeName['SchemeName']){
      case 'su':
        $PostfixNic = 'FID';
      break;
      case 'рф':
        $PostfixNic = 'RF';
      break;
      default:
        $PostfixNic = 'RIPN';
    }
    # Формируем инструкцию по трансферу от стороннего регистратора
    $TransferMsg = <<<EOT
Сейчас Ваш домен находится на обслуживании у регистратора %s.\n
Для переноса его к нам, администратор домена (физ. или юр. лицо, на которое производилась регистрация) должен уведомить его о решении перенести обслуживание домена к регистратору %s.\n
Для этого необходимо направить текущему регистратору письмо в котором указать следующие реквизиты нового регистратора:
  - наименование - %s;
  - идентификатор - %s-REG-%s (для домена в зоне %s).\n
К письму от физического лица должна быть приложена копия первого информативного разворота паспорта, письмо должно быть заверено нотариально. Письмо от юридического лица должно быть составлено на фирменном бланке организации.\n
Обращаем Ваше внимание, что процедура переноса может длиться до одного месяца. Для избежания возможных проблем, перенос нужно начинать не позднее чем за 2 месяца до окончания срока его регистрации. В ином случае Вам возможно придется продлевать его еще минимум на один год по тарифам старого регистратора.
EOT;
    $TransferMsg = SPrintF($TransferMsg, $Registrar, $Settings['TypeID'], $Settings['JurName'], $Settings['PrefixNic'], $PostfixNic, strtoupper($SchemeName['SchemeName']));
    # Достаем статью с информацией о шаблонах документов и контактами регистратора
    $Where = SPrintF('`Partition`=\'Registrators/%s/external\'',$PrefixRegistrar);
    $Clause = DB_Select('Clauses','*',Array('UNIQ','Where'=>$Where));
    switch(ValueOf($Clause)){
      case 'array':
        $TransferMsg .= <<<EOT
\n\nНиже приведена информация по оформлению писем Вашему регистратору, и, его контактные данные. (ВНИМАНИЕ! Данная информация носит справочный характер и возможно требует уточнения у Вашего регистратора.):\n\n
EOT;
        $TransferDoc = trim(Strip_Tags($Clause['Text']));
        break;
      default:
        #-----------------------------------------------------------------------
        Debug(SPrintF('[Notifies/Email/DomainsOrdersOnTransfer]: Статья не найдена. Ожидалась Registrators/%s/external',$PrefixRegistrar));
        #-----------------------------------------------------------------------
        $TransferDoc = "\n\nДля получения информации об оформлении писем Вашему текущему регистратору и его контактах перейдите на его сайт.";
        #-----------------------------------------------------------------------
        # Уведомление об ошибке статьи
	$Event = Array(
			'UserID'	=> $UserID['ID'],
			'PriorityID'	=> 'Error',
			'Text'		=> SPrintF('Статья по переносу домена не найдена. Ожидалась Registrators/%s/external',$PrefixRegistrar),
			'IsReaded'	=> FALSE
	              );
        $Event = Comp_Load('Events/EventInsert',$Event);
        if(!$Event)
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
    }
    $TransferMsg .= $TransferDoc;
  }
}
else {
  #-----------------------------------------------------------------------------
  Debug("[Notifies/Email/DomainsOrdersOnTransfer]: Registrar или PrefixRegistrar не был определён.");
  #-----------------------------------------------------------------------------
  $TransferMsg = <<<EOT
К сожалению информацию о Вашем регистраторе не удалось получить в автоматическом режиме и поэтому мы не смогли автоматически выслать Вам подробную инструкцию по переносу домена.\n
Уведомление об этой ошибке уже отправлено нашим программистам и в скором времени она обязательно будет исправлена. А пока, для получения помощи по переносу домена под наше управление обратитесь в наш центр поддержки.
EOT;
  #-----------------------------------------------------------------------------
  # Уведомление об ошибке формирования инструкции
  $Event = Array(
  		'UserID'	=> $UserID['ID'],
		'PriorityID'	=> 'Error',
		'Text'		=> SPrintF('Ошибка автоматического формирования инструкции по переносу домена к нам. Домен %s.%s.',$DomainOrder['DomainName'],$SchemeName['SchemeName']),
		'IsReaded'	=> FALSE
                );
  $Event = Comp_Load('Events/EventInsert',$Event);
  if(!$Event)
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Message = <<<EOT
Здравствуйте, %User.Name%!

Уведомляем Вас о том, что %DomainOrder.StatusDate% от Вас поступила заявка на перенос домена №%DomainOrder.Number% (%DomainOrder.DomainName%.%DomainOrder.SchemeName%) под наше управление.

$TransferMsg

%From.Sign%
EOT;
#-------------------------------------------------------------------------------
$Replace = Array_ToLine($Replace,'%');
#-------------------------------------------------------------------------------
foreach(Array_Keys($Replace) as $Key)
  $Message = Str_Replace($Key,$Replace[$Key],$Message);
#-------------------------------------------------------------------------------
return Array('Theme'=>'Перенос домена','Message'=>$Message);
#-------------------------------------------------------------------------------

?>
