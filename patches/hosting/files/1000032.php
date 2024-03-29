<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
//if(HOST_ID == 'manager.host-food.ru')
//	return TRUE;
#-------------------------------------------------------------------------------
// чистим кэш
$IsFlush = CacheManager::flush();
if(!$IsFlush)
	@Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// добавляем колонку для информации о зависимом заказе
$IsQuery = DB_Query("ALTER TABLE `Orders` ADD `DependOrderID` int(11) default '0' AFTER `Params`;");
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём IP и перекладываем зависимые заказы в новую колонку
$ExtraIPOrders = DB_Select('ExtraIPOrders','*');
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	break;
case 'array':
	#-------------------------------------------------------------------------------
	// перебираем заказы
	foreach($ExtraIPOrders as $ExtraIPOrder){
		#-------------------------------------------------------------------------------
		// обновляем таблицу заказов, проставляем номер зависимого заказа
		$IsUpdate = DB_Update('Orders',Array('DependOrderID'=>$ExtraIPOrder['DependOrderID']),Array('ID'=>$ExtraIPOrder['OrderID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsQuery = DB_Query('ALTER TABLE `ExtraIPOrders` DROP `DependOrderID`');
if(Is_Error($IsQuery))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём все заказы на лицензии
$ISPswOrders = DB_Select('ISPswOrdersOwners','*');
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	break;
case 'array':
	#-------------------------------------------------------------------------------
	// перебираем заказы
	foreach($ISPswOrders as $ISPswOrder){
		#-------------------------------------------------------------------------------
		// выбираем виртуалки этого юзера
		$VPSOrders = DB_Select('VPSOrdersOwners','*',Array('Where'=>Array(SPrintF('`IP` = "%s"',$ISPswOrder['IP']),SPrintF('`UserID` = %u',$ISPswOrder['UserID']))));
		#-------------------------------------------------------------------------------
		switch(ValueOf($VPSOrders)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			break;
		case 'array':
			#-------------------------------------------------------------------------------
			// перебираем полученный список виртуалок
			foreach($VPSOrders as $VPSOrder){
				#-------------------------------------------------------------------------------
				// обновляем таблицу заказов, проставляем номер зависимого заказа
				$IsUpdate = DB_Update('Orders',Array('DependOrderID'=>$VPSOrder['OrderID']),Array('ID'=>$ISPswOrder['OrderID']));
				if(Is_Error($IsUpdate))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// перебираем выделенные сервера этого юзера
		$DSOrders = DB_Select('DSOrdersOwners','*',Array('Where'=>Array(SPrintF('`IP` = "%s"',$ISPswOrder['IP']),SPrintF('`UserID` = %u',$ISPswOrder['UserID']))));
		#-------------------------------------------------------------------------------
		switch(ValueOf($DSOrders)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			break;
		case 'array':
			#-------------------------------------------------------------------------------
			// перебираем полученный список виртуалок
			foreach($DSOrders as $DSOrder){
				#-------------------------------------------------------------------------------
				// обновляем таблицу заказов, проставляем номер зависимого заказа
				$IsUpdate = DB_Update('Orders',Array('DependOrderID'=>$DSOrder['OrderID']),Array('ID'=>$ISPswOrder['OrderID']));
				if(Is_Error($IsUpdate))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------

	}
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
/**/

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
