<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Params');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#-------------------------------------------------------------------------------
# выхлоп для сотрудников бухгалтерии
$Out = "";
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = "`StatusID` = 'Conditionally'";
#-------------------------------------------------------------------------------
$Invoices = DB_Select('InvoicesOwners',Array('ID','UserID','ContractID','StatusDate','Summ'),Array('SortOn'=>'UserID', 'IsDesc'=>TRUE, 'Where'=>$Where));
switch(ValueOf($Invoices)){
case 'error':
  return ERROR | @Trigger_Error(500);
case 'exception':
  return TRUE;
case 'array':
  break;
default:
  return ERROR | @Trigger_Error(101);
}
#---------------------------------------------------------------------------
foreach($Invoices as $Invoice){
  # added by lissyara 2012-09-30 in 20:20 MSK, for JBS-109
  # перебираем всех юзеров с условными инвойсами, смотрим сколько от статуса
  # если от статуса больше 31 дня:
  if($Invoice['StatusDate'] < Time() - 31*24*3600){
    #---------------------------------------------------------------------------
    # 1. откатываем инвойс в статус "Удалён"
    $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Invoices','StatusID'=>'Rejected','RowsIDs'=>$Invoice['ID'],'Comment'=>'Отмена условно оплаченного счёта'));
    #---------------------------------------------------------------------------
    switch(ValueOf($Comp)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return $StatusSet;
    case 'array':
      # No more...
      break;
    default:
      return ERROR | @Trigger_Error(101);
    }
    #---------------------------------------------------------------------------
    # 2. вычитаем сумму счёта из договора, на который счёт.
    $Contract = DB_Select('ContractsOwners','Balance',Array('UNIQ','ID'=>$Invoice['ContractID']));
    switch(ValueOf($Contract)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return ERROR | @Trigger_Error(400);
    case 'array':
      break;
    default:
      return ERROR | @Trigger_Error(101);
    }
    #---------------------------------------------------------------------------
    $After = $Contract['Balance'] - $Invoice['Summ'];
    #---------------------------------------------------------------------------
    $IsUpdate = DB_Update('Contracts',Array('Balance'=>$After),Array('ID'=>$Invoice['ContractID']));
    if(Is_Error($IsUpdate))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    # заносим запись в историю операций с контрактами
     $Comp = Comp_Load('Formats/Invoice/Number',$Invoice['ID']);
     if(Is_Error($Comp))
       return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $IPosting = Array(
                      #---------------------------------------------------------
                     'ContractID' => $Invoice['ContractID'],
                     'ServiceID'  => 2000,
                     'Comment'    => SPrintF('Возврат средств условно зачисленных по счёту #%u',$Comp),
                     'Before'     => $Contract['Balance'],
                     'After'      => $After
                     );
    #-------------------------------------------------------------------
    $PostingID = DB_Insert('Postings',$IPosting);
    if(Is_Error($PostingID))
      return ERROR | @Trigger_Error(500);
    #-------------------------------------------------------------------
    # 3. если балланс получился отрицательный - лочим все услуги этого договора
    if($After < 0){
      $Columns = Array('ID','ServiceID',
                       '(SELECT `Services`.`Code` FROM `Services` WHERE `OrdersOwners`.`ServiceID` = `Services`.`ID`) AS `OrderTypeCode`'
                       );
      #-----------------------------------------------------------------
      #$Orders = DB_Select('OrdersOwners',$Columns,Array('Where'=>SPrintF('`StatusID` = "Active" AND `ContractID` = %u',$Invoice['ContractID'])));
      $Orders = DB_Select('OrdersOwners',$Columns,Array('Where'=>SPrintF('`StatusID` = "Active" AND `UserID` = %u',$Invoice['UserID'])));
      switch(ValueOf($Orders)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        break;
      case 'array':
        foreach($Orders as $Order){
          # как-то дохрена сложно получается...
	  # проще может событие вешать... и отдавать на откуп администратору.
	  # подумать надо.
	  Debug(SPrintF('[comp/Tasks/GC/NotifyConditionallyInvoice]: Необходимо залочить услугу (%s), номер заказа (#%u)',$Order['OrderTypeCode'],$Order['ID']));
          #-----------------------------------------------------------------
          $Event = Array(
                        'UserID'        => $Invoice['UserID'],
                        'PriorityID'    => 'Warning',
                        'Text'          => SPrintF('Условно оплаченный счёт не был оплачен 31 день. Необходимо заблокировать услугу (%s), номер заказа (#%u)',$Order['OrderTypeCode'],$Order['ID']),
                        'IsReaded'      => FALSE
                        );
          $Event = Comp_Load('Events/EventInsert',$Event);
          if(!$Event)
            return ERROR | @Trigger_Error(500);
          }
	  #-----------------------------------------------------------------
        break;
      default:
        return ERROR | @Trigger_Error(101);
      }
    #-----------------------------------------------------------------
    }
  }
}
#-------------------------------------------------------------------------
#-------------------------------------------------------------------------
# досатаём условные счета ещё раз - уже без тех которые были отменены
$Where = "`StatusID` = 'Conditionally'";
#-------------------------------------------------------------------------------
$Invoices = DB_Select('InvoicesOwners',Array('ID','UserID','CreateDate','Summ','(SELECT `Email` FROM `Users` WHERE `Users`.`ID` = `InvoicesOwners`.`UserID`) AS `UserEmail`'),Array('SortOn'=>'UserID', 'IsDesc'=>TRUE, 'Where'=>$Where));
switch(ValueOf($Invoices)){
case 'error':
  return ERROR | @Trigger_Error(500);
case 'exception':
  return TRUE;
case 'array':
  break;
default:
  return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
foreach($Invoices as $Invoice){
  #-------------------------------------------------------------------------
  $Out = $Out . "Неоплаченный счёт на сумму " . $Invoice['Summ'] . " от пользователя " . $Invoice['UserEmail'] . "\n";
  #-------------------------------------------------------------------------
  Debug(SPrintF("[Tasks/GC/NotifyConditionallyInvoice]: Уведомление о условно оплаченном счете #%d.",$Invoice['ID']));
  #----------------------------------TRANSACTION----------------------------
  if(Is_Error(DB_Transaction($TransactionID = UniqID('Tasks/GC/NotifyConditionallyInvoice'))))
  return ERROR | @Trigger_Error(500);
  #-------------------------------------------------------------------------
  $msg = new ConditionallyPayedInvoiceMsg($Invoice, (integer)$Invoice['UserID']);
  $IsSend = NotificationManager::sendMsg($msg);
  #-------------------------------------------------------------------------
  switch(ValueOf($IsSend)){
  case 'true':
    $Event = Array(
                   'UserID'	=> $Invoice['UserID'],
                   'PriorityID'	=> 'Billing',
                   'Text'	=> SPrintF('Уведомление о условно оплаченном счете #%d, неоплачен более %d дней',$Invoice['ID'],$Params['DaysBeforeNotice'])
                  );
    $Event = Comp_Load('Events/EventInsert',$Event);
    if(!$Event)
      return ERROR | @Trigger_Error(500);
    break;
  #-------------------------------------------------------------------------
  case 'exception':
    $Event = Array(
                  'UserID'	=> $Invoice['UserID'],
                  'PriorityID'	=> 'Billing',
                  'Text'	=> SPrintF('Уведомление о условно оплаченном счете #%d не доставлено. Не удалось оповестить пользователя ни одним из методов.',$Invoice['ID'])
                  );
    $Event = Comp_Load('Events/EventInsert',$Event);
    if(!$Event)
      return ERROR | @Trigger_Error(500);
    break;
    #-------------------------------------------------------------------------
  default:
    return ERROR | @Trigger_Error(500);
  }
  #-------------------------------------------------------------------------
  #-------------------------------------------------------------------------
  if(Is_Error(DB_Commit($TransactionID)))
    return ERROR | @Trigger_Error(500);
  #-------------------------------------------------------------------------
}
#-------------------------------------------------------------------
#-------------------------------------------------------------------
# 4. достаём все договора с отрицательным баллансом - добавляем в отчёт для бухов
#-------------------------------------------------------------------------------
$Users = DB_Select('ContractsOwners',Array('DISTINCT(`UserID`) AS `UserID`','Balance','(SELECT `Email` FROM `Users` WHERE `Users`.`ID` = `ContractsOwners`.`UserID`) AS `UserEmail`'),Array('GroupBy'=>'UserID', 'Where'=>'`Balance` < 0'));
switch(ValueOf($Users)){
case 'error':
  return ERROR | @Trigger_Error(500);
case 'exception':
  return TRUE;
case 'array':
  break;
default:
  return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
foreach($Users as $User){
  $Out = $Out . SPrintF("Отрицательный балланс (%s) у клиента %s\n",$User['Balance'],$User['UserEmail']);
  #-------------------------------------------------------------------------------
  $Event = Array(
                'UserID'	=> $User['UserID'],
                'PriorityID'	=> 'Billing',
                'Text'		=> SPrintF('У пользователя отрицательный баланс (%s)',$User['Balance']),
		'IsReaded'      => FALSE
                );
  $Event = Comp_Load('Events/EventInsert',$Event);
  if(!$Event)
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------
#-------------------------------------------------------------------
# 5. Обновляем лимиты пользователя
$Result = DB_Query('UPDATE `Users` SET `LayPayMaxSumm`=((SELECT SUM(`Summ`) FROM `InvoicesOwners` WHERE `InvoicesOwners`.`UserID`=`Users`.`ID` AND `StatusID` = "Payed") / 10) WHERE ((SELECT SUM(`Summ`) FROM `InvoicesOwners` WHERE `InvoicesOwners`.`UserID`=`Users`.`ID` AND `StatusID` = "Payed") / 10) > `LayPayMaxSumm`');
if(Is_Error($Result))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------
$Result = DB_Query('UPDATE `Users` SET `LayPayMaxSumm` = 0 WHERE `LayPayMaxSumm` IS NULL');
if(Is_Error($Result))
  return ERROR | @Trigger_Error(500);
#Debug(SPrintF("[Tasks/GC/NotifyConditionallyInvoice]: отчёт для бухгалтерии %s",$Out));
#-------------------------------------------------------------------
#-------------------------------------------------------------------
# ищщем сотрудников бухгалтерии
$Entrance = Tree_Entrance('Groups',3200000);
#-------------------------------------------------------------------
switch(ValueOf($Entrance)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#---------------------------------------------------------------
	$String = Implode(',',$Entrance);
	#---------------------------------------------------------------
	$Employers = DB_Select('Users','ID',Array('Where'=>SPrintF('`GroupID` IN (%s)',$String)));
	#---------------------------------------------------------------
	switch(ValueOf($Employers)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# найти всех сотрудников, раз нет сотрудников в бухгалтерии
		$Entrance = Tree_Entrance('Groups',3000000);
		#-------------------------------------------------------------------
		switch(ValueOf($Entrance)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			#---------------------------------------------------------------
			$String = Implode(',',$Entrance);
			#---------------------------------------------------------------
			$Employers = DB_Select('Users','ID',Array('Where'=>SPrintF('`GroupID` IN (%s)',$String)));
			#---------------------------------------------------------------
			switch(ValueOf($Employers)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				Debug(SPrintF("[Tasks/GC/NotifyConditionallyInvoice]: найдено %s сотрудников любых отделов",SizeOf($Employers)));
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		break;
	case 'array':
		Debug(SPrintF("[Tasks/GC/NotifyConditionallyInvoice]: найдено %s сотрудников отдела бухгалтерии",SizeOf($Employers)));
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#---------------------------------------------------------
#---------------------------------------------------------
foreach($Employers as $Employer){
	if($Employer['ID'] > 2000 || $Employer['ID'] == 100){
		#---------------------------------------------------------
        $msg = new DispatchMsg(Array('Theme'=>'Список условно оплаченных счетов','Message'=>$Out), (integer)$Employer['ID']);
		$IsSend = NotificationManager::sendMsg($msg);
		#---------------------------------------------------------
		switch(ValueOf($IsSend)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			# No more...
		case 'true':
			# No more...
			Debug(SPrintF("[Tasks/GC/NotifyConditionallyInvoice]: Сообщение для сотрудника #%s отослано",$Employer['ID']));
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
	}
}
#---------------------------------------------------------
#---------------------------------------------------------
return TRUE;



?>
