<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Result = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Interface']['Administrator']['Notes']['CheckTablesEngines'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Settings['MakeCheckTablesEngines'])
	return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$CacheID = 'CheckTablesEngines';
#-------------------------------------------------------------------------------
$Out = CacheManager::get($CacheID);
#-------------------------------------------------------------------------------
if($Out || Is_Array($Out))
	return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$MyISAM = Array('Events','RequestLog','ServersUpTime','StatusesHistory','OrdersHistory');
#-------------------------------------------------------------------------------
// приводим к нижнему регистру все имена таблиц, т.к. @@GLOBAL.lower_case_table_names;
$MyISAM = Array_Map('StrToLower', $MyISAM);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Tables = DB_Select('`INFORMATION_SCHEMA`.`TABLES`',Array('TABLE_NAME','ENGINE'),Array('Where'=>SPrintF("`TABLE_SCHEMA` = '%s' AND `ENGINE` IS NOT NULL",$Config['DBConnection']['DbName'])));
#-------------------------------------------------------------------------------
switch(ValueOf($Tables)){
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
foreach($Tables as $Table){
	#-------------------------------------------------------------------------------
	if($Table['ENGINE'] != 'InnoDB' && !In_Array(StrToLower($Table['TABLE_NAME']),$MyISAM)){
		#-------------------------------------------------------------------------------
		$Note = TRUE;
		Debug(SPrintF('[comp/Notes/Administrator/CheckTablesEngines]: Incorrect Table Engine, table = %s',$Table['TABLE_NAME']));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!IsSet($Note)){
	#-------------------------------------------------------------------------------
	CacheManager::add($CacheID,$Result,600);
	#-------------------------------------------------------------------------------
	return $Result;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('P','Структура базы данных некорректна. Для корректной работы биллинговой системы, таблицы должны быть InnoDB, в противном случае, будет некорректный рассчёт оставшихся оплаченных периодов и нарушение ссылочной целостности базы данных. Если вы восстановили биллинг из бэкапа, восстановите заново, включив InnoDB, если это чистая инсталляция - проведите её заново, включив InnoDB.'));
$NoBody->AddChild(new Tag('P','Список таблиц с некорректным Storage Engine:'));
#-------------------------------------------------------------------------------
foreach($Tables as $Table){
	#-------------------------------------------------------------------------------
	if($Table['ENGINE'] != 'InnoDB' && !In_Array(StrToLower($Table['TABLE_NAME']),$MyISAM)){
		#-------------------------------------------------------------------------------
		$NoBody->AddChild(new Tag('P',SPrintF('Таблица: "%s"; Storage Engine: "%s"',$Table['TABLE_NAME'],$Table['ENGINE'])));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Result[] = $NoBody;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
