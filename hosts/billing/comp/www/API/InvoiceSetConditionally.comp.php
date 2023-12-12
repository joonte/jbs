<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$InvoicesIDs  = (array) @$Args['RowsIDs'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Count($InvoicesIDs) < 1)
	return new gException('ACCOUNTS_NOT_SELECTED','Счёт для условной оплаты не выбран');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$AllowConditionally = $Config['Invoices']['AllowConditionally'];
#-------------------------------------------------------------------------------
if(!$AllowConditionally)
	return new gException('GLOBAL_DENY_CONDITIONALLY_INVOICES','Проведение условно оплаченных счетов запрещено');
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(SizeOf($InvoicesIDs) > 1)
	return new gException('CONDITIONALLY_PAYED_MORE_ONE_INVOICE','Условно зачислить можно лишь один счёт');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!SizeOf($GLOBALS['__USER']['ConfirmedWas']))
	return new gException('ACCOUNTS_NOT_CONFIRMED','Ваш аккаунт не подтверждён. Добавьте и подтвердите телефонный номер (SMS) в настройках');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# достаём счёт
$Invoice = DB_Select('InvoicesOwners',Array('ID','UserID','StatusID','IsPosted','Summ'),Array('UNIQ','ID'=>$InvoicesIDs[0]));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// неподтверждённые нельзя условно прводоить
if($Invoice['StatusID'] == 'NotConfirmed')
	return new gException('DENY_PAYED_MOT_COMPLETED_INVOICE','Этот счёт уже был ранее оплачен, вам необходимо добавить и подтвердить телефон для его проведения');
// если счёт был ранее оплачен ил условно проведён - не даём ему ставить статус
if($Invoice['IsPosted'])
	return new gException('DENY_PAYED_CONDITIONALLY_INVOICE','Этот счёт уже был ранее оплачен или условно оплачен, нельзя условно оплатить вторично');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем, имеет ли юзер отношение к этому счёту
$IsPermission = Permission_Check('InvoicesEdit',(integer)$GLOBALS['__USER']['ID'],(integer)$Invoice['UserID']);
#-------------------------------------------------------------------------
switch(ValueOf($IsPermission)){
case 'error':
  return ERROR | @Trigger_Error(500);
case 'exception':
  return ERROR | @Trigger_Error(400);
case 'false':
  return ERROR | @Trigger_Error(700);
case 'true':
  break;
default:
  return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем статус счёта - только неоплаченные или отменённые можно проводить условно
#-------------------------------------------------------------------------------
$Statuses = Array('Waiting','Rejected','Deleted');
if(!In_Array($Invoice['StatusID'],$Statuses))
  return new gException('BAD_INVOICE_STATUS','Провести условно можно только счета ожидающие оплаты');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем нету ли у юзера условных счетов
$Count = DB_Count('InvoicesOwners',Array('Where'=>SPrintF("`StatusID` = 'Conditionally' AND `UserID` = %u",$Invoice['UserID'])));
if($Count)
  return new gException('TOO_MANY_CONDITIONALLY_INVOICES','Вначале, оплатите предыдущий условно зачисленный счёт');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем не отрицательный ли у него балланс, на каком-либо договоре
$Count = DB_Count('ContractsOwners',Array('Where'=>SPrintF("`Balance` < 0 AND `UserID` = %u",$Invoice['UserID'])));
if($Count)
  return new gException('NEGATIVE_CONTRACT_BALANCE','У вас есть задолженность по одному из договоров. До её погашения, вы не сможете пользоваться нашими услугами в кредит');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем что он наоплачивал на ту сумму, начиная с которой можно проводить счета условно
$PayedSumm = DB_Select('InvoicesOwners',Array('SUM(Summ) AS `Summ`'),Array('UNIQ','Where'=>SPrintF("`StatusID` = 'Payed' AND `UserID` = %u",$Invoice['UserID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($PayedSumm)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
if($PayedSumm['Summ'] < $GLOBALS['__USER']['LayPayThreshold'])
	return new gException('TOO_SMALL_SUMM_PAYED_INVOICES',SPrintF('Сумма ваших оплаченных счетов (%01.2f) недостаточна для проведения счетов условно. Данная возможность будет доступна по достижении суммы оплат равной (%01.2f)',$PayedSumm['Summ'],$GLOBALS['__USER']['LayPayThreshold']));
#-------------------------------------------------------------------------------
# проверяем что сумма счёта не превышает сумму на которую юзер может проводить счета условно
if($Invoice['Summ'] > $GLOBALS['__USER']['LayPayMaxSumm'])
	return new gException('TOO_BIG_INVOICE_SUMM',SPrintF('Сумма счёта (%01.2f) слишком велика. Максимальная сумма которая может быть зачислена условно, равна (%01.2f)',$Invoice['Summ'],$GLOBALS['__USER']['LayPayMaxSumm']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем что именно оплачивается этим счётом - доступны не все услуги
$Columns = Array(
		'ServiceID','OrderID',
		'(SELECT `Name` FROM `ServicesOwners` WHERE `ServicesOwners`.`ID` = `InvoicesItems`.`ServiceID`) AS `ServiceName`',
		'(SELECT `Code` FROM `ServicesOwners` WHERE `ServicesOwners`.`ID` = `InvoicesItems`.`ServiceID`) AS `ServiceCode`',
		'(SELECT `IsConditionally` FROM `ServicesOwners` WHERE `ServicesOwners`.`ID` = `InvoicesItems`.`ServiceID`) AS `IsConditionally`',
                );
$InvoicesItems = DB_Select('InvoicesItems',$Columns,Array('Where'=>SPrintF('`InvoiceID` = %u',$Invoice['ID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($InvoicesItems)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('NO_INVOICES_ITEMS_FOR_INVOICE','Это не счёт на оплату услуг. Его нельзя провести условно');
  case 'array':
    break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
foreach($InvoicesItems as $InvoicesItem){
  Debug(SPrintF('[comp/www/API/InvoiceSetConditionally]: processing order (#%u) service (%s/%s)',$InvoicesItem['OrderID'],$InvoicesItem['ServiceCode'],$InvoicesItem['ServiceName']));
  if(!$InvoicesItem['IsConditionally'])
    return new gException('BAD_ITEM_IN_INVOICE',SPrintF('В этом счёте на оплату присутствует сервис (%s) который нельзя оплачивать условно',$InvoicesItem['ServiceName']));
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# вроде как все условия соблюдены... проводим счёт условно
$Number = Comp_Load('Formats/Invoice/Number',$Invoice['ID']);
if(Is_Error($Number))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Summ = Comp_Load('Formats/Currency',$Invoice['Summ']);
if(Is_Error($Summ))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
                 'www/API/StatusSet',
		 Array(
		      'ModeID'   => 'Invoices',
		      'StatusID' => 'Conditionally',
		      'RowsIDs'  => $Invoice['ID'],
		      'Comment'  => 'Клиент взял в кредит'
		      )
		);
#-----------------------------------------------------------------------
switch(ValueOf($Comp)){
case 'error':
  return ERROR | @Trigger_Error(500);
case 'exception':
  return ERROR | @Trigger_Error(400);
case 'array':
  break;
default:
  return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------
$Event = Array(
              'UserID'        => $Invoice['UserID'],
              'PriorityID'    => 'Billing',
              'Text'          => SPrintF('Пользователь \'%s\' (%s) провёл условно счёт (#%u) на сумму (%s)',$GLOBALS['__USER']['Name'],$GLOBALS['__USER']['Email'],$Number,$Summ)
              );
$Event = Comp_Load('Events/EventInsert',$Event);
if(!$Event)
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------
#-------------------------------------------------------------------
# выходим
return Array('Status'=>'Ok');


?>
