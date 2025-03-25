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
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['GC']['CleanTablesSettings'];
#-------------------------------------------------------------------------------
if(!$Settings['IsActive'])
	return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// тычемся к таблице TmpData, проверяем наличие строки GC
// 1. строки нет, создаём.
// 2. строка есть, перебираем, что перебрали удаляем и сохраняем обратно в TmpData
// если нечего перебирать, возвращаем что задача выполнена
#-------------------------------------------------------------------------------
$TmpData = DB_Select('TmpData','*',Array('UNIQ','Where'=>Array('`AppID` = "GC.CleanTables"'),'SortOn'=>'CreateDate','Limits'=>Array(0,1)));
#-------------------------------------------------------------------------------
switch(ValueOf($TmpData)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	//Debug(SprintF('[comp/Tasks/GC/CleanTables]: первый запуск, вносим массив в БД'));
	#-------------------------------------------------------------------------------
	// первый запуск. все действия вне запроса вынесены
	break;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
case 'array':
	#-------------------------------------------------------------------------------
	$Params = $TmpData['Params'];
	#-------------------------------------------------------------------------------
	// НЕ первый запуск, првоеряем размерность массива, может уже всё сделали
	if(SizeOf($Params) < 1)
		return TRUE;
	#-------------------------------------------------------------------------------
	//Debug(SprintF('[comp/Tasks/GC/CleanTables]: Params = %s',print_r($Params,true)));
	#-------------------------------------------------------------------------------
	// запросы
	if(IsSet($Params['Queries'])){
		#-------------------------------------------------------------------------------
		foreach(Array_Keys($Params['Queries']) as $QueryKey){
			#-------------------------------------------------------------------------------
			//Debug(SprintF('[comp/Tasks/GC/CleanTables]: Params[Queries][$QueryKey][Query] = %s',$Params['Queries'][$QueryKey]['Query']));
			#-------------------------------------------------------------------------------
			// есть необработанные запросы
			if(SizeOf($Params['Queries'][$QueryKey]['Patterns']) > 0){
				#-------------------------------------------------------------------------------
				foreach(Array_Keys($Params['Queries'][$QueryKey]['Patterns']) as $PatternKey){
					#-------------------------------------------------------------------------------
					//Debug(SprintF('[comp/Tasks/GC/CleanTables]: Params[Queries][$QueryKey][Patterns][$PatternKey] = %s',$Params['Queries'][$QueryKey]['Patterns'][$PatternKey]));
					#-------------------------------------------------------------------------------
					// заменяем в запросе шаблоны
					$IsQuery = DB_Query(SPrintF($Params['Queries'][$QueryKey]['Query'],$Params['Queries'][$QueryKey]['Patterns'][$PatternKey]));
					if(Is_Error($IsQuery))
						return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
					#-------------------------------------------------------------------------------
					// удаляем
					UnSet($Params['Queries'][$QueryKey]['Patterns'][$PatternKey]);
					#-------------------------------------------------------------------------------
					// может всё перебрали?
					if(SizeOf($Params['Queries'][$QueryKey]['Patterns']) < 1)
						UnSet($Params['Queries'][$QueryKey]);
					#-------------------------------------------------------------------------------
					#-------------------------------------------------------------------------------
					// выходим. один заход - один запрос
					break;
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				// удаляем, все шаблоны обработаны
				UnSet($Params['Queries'][$QueryKey]);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			// выходим из цикла перебора запросов
			break;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		// проверяем размер массива
		if(SizeOf($Params['Queries']) < 1)
			UnSet($Params['Queries']);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	// чистка таблиц
	if(IsSet($Params['Tables'])){
		#-------------------------------------------------------------------------------
		foreach(Array_Keys($Params['Tables']) as $TableKey){
			#-------------------------------------------------------------------------------
			//Debug(SprintF('[comp/Tasks/GC/CleanTables]: Params[Tables][$TableKey][Table] = %s',$Params['Tables'][$TableKey]['Table']));
			#-------------------------------------------------------------------------------
			// есть необработанные запросы
			if(SizeOf($Params['Tables'][$TableKey]['Wheres']) > 0){
				#-------------------------------------------------------------------------------
				if(IsSet($GLOBALS['TaskReturnInfo']))
					$GLOBALS['TaskReturnInfo'] = Array($GLOBALS['TaskReturnInfo'],SPrintF('Tables: %s',$Params['Tables'][$TableKey]['Table']));
				#-------------------------------------------------------------------------------
				foreach(Array_Keys($Params['Tables'][$TableKey]['Wheres']) as $WhereKey){
					#-------------------------------------------------------------------------------
					//Debug(SprintF('[comp/Tasks/GC/CleanTables]: Params[Tables][$TableKey][Wheres][$WhereKey] = %s',$Params['Tables'][$TableKey]['Wheres'][$WhereKey]));
					#-------------------------------------------------------------------------------
					$IsDelete = DB_Delete($Params['Tables'][$TableKey]['Table'],Array('Where'=>$Params['Tables'][$TableKey]['Wheres'][$WhereKey]));
					if(Is_Error($IsDelete))
						return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
					#-------------------------------------------------------------------------------
					// удаляем
					UnSet($Params['Tables'][$TableKey]['Wheres'][$WhereKey]);
					#-------------------------------------------------------------------------------
					// может всё перебрали?
					if(SizeOf($Params['Tables'][$TableKey]['Wheres']) < 1)
						UnSet($Params['Tables'][$TableKey]);
					#-------------------------------------------------------------------------------
					#-------------------------------------------------------------------------------
					// выходим. один заход - один запрос
					break;
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				// удаляем, все условия обработаны
				UnSet($Params['Tables'][$TableKey]);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			// выходим из цикла перебора таблиц
			break;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		// проверяем размер массива
		if(SizeOf($Params['Tables']) < 1)
			UnSet($Params['Tables']);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	// првоеряем размерность массива, может уже всё сделали
	if(SizeOf($Params) < 1){
		#-------------------------------------------------------------------------------
		// удаляем запись из таблицы
		$IsDelete = DB_Delete('TmpData',Array('ID'=>$TmpData['ID']));
		if(Is_Error($IsDelete))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		return TRUE;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// сохраняем обновлённый массив
	$IsUpdate = DB_Update('TmpData',Array('Params'=>$Params),Array('ID'=>$TmpData['ID']));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// продолжаем в след. цикле
	return 20;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// список таблиц и запросов
$Params = Array('Tables'=>Array(),'Queries'=>Array());
#-------------------------------------------------------------------------------
$Params['Queries'][] = Array(
				'Query'		=> "DELETE FROM `Events` WHERE `Text` LIKE '%s' AND `CreateDate` < UNIX_TIMESTAMP() - 90*24*60*60 /* старше 90 дней */;",
				'Patterns'	=> Array(
							'Пользователь вошел в систему %',
							'Вход в систему %',
							'Сообщение для %',
							'Пользователь вышел %',
							'Не удалость автоматически оплатить %',
							'Добавлено новое сообщение %',
							'Владелец для заказа %',
							'Создан новый запрос %',
							'Автоматическая отмена заказа %',
							'Отмененный заказ %',
							'SMS сообщение для %',
							'Не удалось отправить SMS сообщение %',
							'Замена основного сервера группы %',
							'Задание % вернуло ошибку выполнения',
							'Уведомление о условно оплаченном счете %',
							'Уведомление о условно оплаченном счёте %',
							'Создан запрос в службу поддержки %',
							'Задание % не может быть выполнено в автоматическом режиме',
							'Удалено сообщение %',
							'Найден %/% отсутствующий в биллинге',
							'% подтверждён',
							'Зарегистрирован новый пользователь %',
							'Сформирован новый договор %',
							'Сформирован договор %',
							'% автопродление для заказа %',
							'Контактный адрес (%) подтверждён через %',
							'Не удалость автоматически оплатить заказ %',
							'Автоматическое списание денег (%) у неактивного пользователя',
							'Автоматическое списание средств (%) у неактивного пользователя',
							'Соотрудником % удалён пользователь %',
							'Удалён пользователь (%) не заходивший в биллинг %',
							'Доменная зона "%" не обнаружена в базе данных WhoIs%',
							'%/%: цена % изменена %',
							'Ошибка опроса сервера %',
							'Не удалось сменить именные сервера заказу домена %',
							'Не удалось сменить тарифный план заказу хостинга %',
							'Уведомление о неоплаченном счёте %',
							'Автоматическая отмена счёта %',
							'Выписан счёт % по договору (%), платежная система %',
							'Отменённый счёт % автоматически удалён%',
							'Отменённый счёт % удалён',
							'Не удалось произвести автоматическую оплату заказа % причина %',
							'Удалено почтовое сообщение с нецензурной лексикой %',
							'Промокод (%s) успешно активирован',
							'Запущена миграция виртуальной машины %',
							'Миграция виртуальной машины %',
							'Счет % успешно оплачен',
							'Заказ домена % не найден у регистратора%',
							'Оплачен счёт %, на сумму %, платежная система %',
							'Отмененный % автоматически удален',
							'Заказ VPS %',
							'Заказ на прокси-сервер %, тариф % удален',
							'Сформирована заявка на заказ %',
							'Заказ домена % оплачен на период %',
							'Именные сервера для заказа домена (%) %',
							'Успешно изменён тарифный план %',
							'Ошибка автоматического формирования инструкции по переносу домена %',
							'Определён владелец для заказа домена %',
							'Пользователь (%) не найден на сервере (%)',
							'На сервере (%) найден пользователь (%) отсутствующий в биллинге',
							'Домен % является свободным, невозможно обновить информацию WhoIs',
							'Удалён пользователь (%) не заходивший в биллинг более года',
							'Зарегистрирован пользователь (%)',
							'Заказ домена % не был продлен до окончания срока регистрации. Заказ заблокирован.',
							'Автоматическое удаление домена (%)%',
							'Заказ ExtraIP (%) %',
							'Заказ DS (%) %',
							'Заказ ПО ISPsystem (%) %',
							'На сервере (%) для логина (%) успешно добавлен %',
							'У регистратора % найден лишний домен %',
							'Осуществлён возврат средств за заказ %',
							'% изменён почтовый адрес пользователя %',
							'% условно оплачен',
							'Найдена неучтённая лицензия %',
							'Сформирована заявка на аренду выделенного сервера %',
							'Отключены оповещения для %',
							'Регистрация через OAuth %',
							)
						);
#-------------------------------------------------------------------------------
// Tasks
$Params['Tables'][] = Array(
				'Table'	=> 'Tasks',
				'Wheres'=> Array(
						'`ExecuteDate` < UNIX_TIMESTAMP() - 32*24*3600 AND `UserID` != 1 AND `TypeID` != "Dispatch"',
						'`CreateDate` < UNIX_TIMESTAMP() - 24*3600 AND `TypeID` = "SMS"'
						)
				);
#-------------------------------------------------------------------------------
// ServersUpTime
$Params['Tables'][] = Array(
				'Table'	=> 'ServersUpTime',
				'Wheres'=> Array('`TestDate` < UNIX_TIMESTAMP() - 3650*24*3600')
				);
#-------------------------------------------------------------------------------
// RequestLog
$Params['Tables'][] = Array(
				'Table'	=> 'RequestLog',
				'Wheres'=> Array('`CreateDate` < UNIX_TIMESTAMP() - 10*24*3600')
				);
#-------------------------------------------------------------------------------
// UsersIPs
$Params['Tables'][] = Array(
				'Table'	=> 'UsersIPs',
				'Wheres'=> Array('`CreateDate` < UNIX_TIMESTAMP() - 2*366*24*3600')
				);
#--------------------------------------------------------------------------------
// Events
$Params['Tables'][] = Array(
				'Table'	=> 'Events',
				'Wheres'=> Array('(SELECT `ID` FROM `Users` WHERE `Events`.`UserID`=`Users`.`ID`) IS NULL')
				);
#--------------------------------------------------------------------------------
// Bonuses
$Params['Tables'][] = Array(
				'Table'	=> 'Bonuses',
				'Wheres'=> Array(
						'`ExpirationDate` < UNIX_TIMESTAMP() - 365*24*60*60',
						'`DaysRemainded` = 0 AND `CreateDate` < UNIX_TIMESTAMP() - 365*24*60*60'
						)
				);
#--------------------------------------------------------------------------------
// TmpData
$Params['Tables'][] = Array(
				'Table'	=> 'TmpData',
				'Wheres'=> Array(
						'`AppID` = "SberBank" AND `CreateDate` < UNIX_TIMESTAMP() - 120*24*60*60',
						'`AppID` = "Telegram" AND `CreateDate` < UNIX_TIMESTAMP() - 365*24*60*60',
						'`AppID` = "VK" AND `CreateDate` < UNIX_TIMESTAMP() - 365*24*60*60',
						'`AppID` = "Taxation" AND `CreateDate` < UNIX_TIMESTAMP() - 5*365*24*60*60',
						'`AppID` = "YandexMetrika" AND `CreateDate` < UNIX_TIMESTAMP() - 365*24*60*60',
						)
				);

#--------------------------------------------------------------------------------
// чистим таблицы с историей заказов от мусора
$Params['Queries'][] = Array(                            
				'Query'		=> 'UPDATE `OrdersHistory` SET `Parked` = REPLACE(`Parked`,",,",",");',
				'Patterns'	=> Array()
				);
#--------------------------------------------------------------------------------
$Params['Queries'][] = Array(                            
				'Query'		=> 'UPDATE `OrdersHistory` SET `Parked` = RIGHT(`Parked`,LENGTH(`Parked`)-1) WHERE `Parked` LIKE ",%";',
				'Patterns'	=> Array()
				);
#--------------------------------------------------------------------------------
// проставляем тикеты как оповещённые, если больше недели прошло
$Params['Queries'][] = Array(                            
				'Query'		=> 'UPDATE `EdesksMessages` SET `IsNotify` = "yes" WHERE `CreateDate` < UNIX_TIMESTAMP() - 7*24*3600;',
				'Patterns'	=> Array()
				);
#--------------------------------------------------------------------------------
// удаляем коды подтверждения у аккаунтов которые добавлены более ... ну года например
$Params['Queries'][] = Array(                            
				'Query'		=> 'UPDATE `Contacts` SET `Confirmation` = "" WHERE `CreateDate` <  UNIX_TIMESTAMP() - 365*24*60*60;',
				'Patterns'	=> Array()
				);
#--------------------------------------------------------------------------------
#--------------------------------------------------------------------------------
$IsInsert = DB_Insert('TmpData',Array('AppID'=>'GC.CleanTables','Params'=>$Params));
if(Is_Error($IsInsert))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
// верёнмся к задаче в след. цикл
return 20;
#--------------------------------------------------------------------------------
#--------------------------------------------------------------------------------

?>
