<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/

class YandexMetrika
{
	#-------------------------------------------------------------------------------
	// параметры
	public $Address		= 'api-metrika.yandex.net';
	public $Host		= 'api-metrika.yandex.net';
	public $Token		= '00-000-00';
	public $YandexCounterId	= '01234567890';
	#-------------------------------------------------------------------------------
	public function __construct($Token,$YandexCounterId){
		$this->Token		= $Token;
		$this->YandexCounterId	= $YandexCounterId;
	}

	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
        // АПИ
        private function API($Action,$Mode,$Query = Array()){
		#-------------------------------------------------------------------------------
		$HTTP = Array(
				'Address'	=> $this->Address,
				'Port'		=> 443,
				'Host'		=> $this->Host,
				'Protocol'	=> 'ssl',
				'Charset'	=> 'UTF-8',
				'IsLogging'	=> TRUE
				);
		#-------------------------------------------------------------------------------
		if($Action == 'Contacts'){
			#-------------------------------------------------------------------------------
			$URL = SPrintF('/cdp/api/v1/counter/%s/data/contacts?merge_mode=UPDATE',$this->YandexCounterId);
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			if($Mode == 'SAVE'){
				#-------------------------------------------------------------------------------
				$URL = SPrintF('/cdp/api/v1/counter/%s/data/orders?merge_mode=SAVE',$this->YandexCounterId);
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				$URL = SPrintF('/cdp/api/v1/counter/%s/data/orders?merge_mode=UPDATE',$this->YandexCounterId);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Headers = Array(SPrintF('Authorization: OAuth %s',$this->Token),'Content-Type: application/json');
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Result = HTTP_Send($URL,$HTTP,Array(),Json_Encode($Query),$Headers);
		#-------------------------------------------------------------------------------
		if(Is_Error($Result))
			return ERROR | @Trigger_Error('[libs/YandexMetrika]: не удалось выполнить запрос к серверу');
		#-------------------------------------------------------------------------------
		$Result = Trim($Result['Body']);
		#-------------------------------------------------------------------------------
		$Result = Json_Decode($Result,TRUE);
		#-------------------------------------------------------------------------------
		// вообще, надо разобраться на этом этапе с результатом, и вернуть уже итог, и в случае ошибки - параметры
		Debug('[libs/YandexMetrika]: Result = %s',print_r($Result,true));
		#-------------------------------------------------------------------------------
		// х.з. какие у них там ограничения
		Sleep(1);
		#-------------------------------------------------------------------------------
		return TRUE;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	}

	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// отправка клиентов
	public function SendClients($Contacts = Array()){
		#-------------------------------------------------------------------------------
		if(!SizeOf($Contacts))
			return TRUE;
		#-------------------------------------------------------------------------------
		$Result = $this->API('Contacts','UPDATE',Array('contacts'=>$Contacts));
		#-------------------------------------------------------------------------------
		if(Is_Error($Result))
			return ERROR | @Trigger_Error('[libs/YandexMetrika]: ошибка отправки списка клиентов в метрику');
		#-------------------------------------------------------------------------------
		return TRUE;
		#-------------------------------------------------------------------------------
	}

	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// отправка оплат
	public function SendOrders($Orders = Array()){
		#-------------------------------------------------------------------------------
		if(SizeOf($Orders['IN_PROGRESS']) > 0){
			#-------------------------------------------------------------------------------
			$Result = $this->API('Orders','SAVE',Array('orders'=>$Orders['IN_PROGRESS']));
			#-------------------------------------------------------------------------------
			if(Is_Error($Result))
				return ERROR | @Trigger_Error('[libs/YandexMetrika]: ошибка отправки списка выписанных счетов в метрику');
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		if(SizeOf($Orders['PAID']) > 0){
			#-------------------------------------------------------------------------------
			$Result = $this->API('Orders','UPDATE',Array('orders'=>$Orders['PAID']));
			#-------------------------------------------------------------------------------
			if(Is_Error($Result))
				return ERROR | @Trigger_Error('[libs/YandexMetrika]: ошибка отправки списка оплаченных счетов в метрику');
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		if(SizeOf($Orders['CANCELLED']) > 0){
			#-------------------------------------------------------------------------------
			$Result = $this->API('Orders','UPDATE',Array('orders'=>$Orders['CANCELLED']));
			#-------------------------------------------------------------------------------
			if(Is_Error($Result))
				return ERROR | @Trigger_Error('[libs/YandexMetrika]: ошибка отправки списка отменённых счетов в метрику');
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		return TRUE;
		#-------------------------------------------------------------------------------
	}

	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// выбираем клиентов для загрузки
	public function SelectClients(){
                #-------------------------------------------------------------------------------
		$Columns = Array(
				'*','(SELECT `Name` FROM `Users` WHERE `Users`.`ID` = `UserID`) AS `Name`',
				'(SELECT FROM_UNIXTIME(`RegisterDate`) FROM `Users` WHERE `Users`.`ID` = `UserID`) AS `RegisterDate`',
				'(SELECT `Email` FROM `Users` WHERE `Users`.`ID` = `UserID`) AS `Email`',
				'(SELECT `Params` FROM `Users` WHERE `Users`.`ID` = `UserID`) AS `Attribs`'	// чтою Json декодировало
				);
		#-------------------------------------------------------------------------------
		$Lines = DB_Select('TmpData',$Columns,Array('Where'=>Array('`AppID` = "YandexMetrika"','`Col1` = "Contacts"'),'SortOn'=>'CreateDate'));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Lines)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return Array('Contacts'=>Array(),'Deleted'=>Array());
		case 'array':
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}       
		#-------------------------------------------------------------------------------
		// перебираем юзеров, строим массив с контактами и массив на удаление записей
		$Contacts = $Deleted = $Sended = Array();
		#-------------------------------------------------------------------------------
		foreach($Lines as $Line){
			#-------------------------------------------------------------------------------
			// на удаление
			$Deleted[] = $Line['ID'];
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			// проверяем, не внесли ли этот контакт на отправку
			if(In_Array($Line['UserID'],$Sended))
				continue;
			#-------------------------------------------------------------------------------
			$Sended[] = $Line['UserID'];
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			// в яндекс отправлять
			$Contacts[] = Array(
						'uniq_id'		=> $Line['UserID'],
						'name'			=> $Line['Name'],
						'create_date_time'	=> $Line['RegisterDate'],
						'client_ids'		=> $Line['Attribs']['YM'],
						'emails'		=> Array($Line['Email']),
						);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		return Array('Contacts'=>$Contacts,'Deleted'=>$Deleted);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	}

	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// выбираем заказы для загрузки
	public function SelectOrders(){
                #-------------------------------------------------------------------------------
		$Orders = Array('PAID'=>Array(),'IN_PROGRESS'=>Array(),'CANCELLED'=>Array());
		#-------------------------------------------------------------------------------
		$Columns = Array(
				'*','(SELECT `Name` FROM `Users` WHERE `Users`.`ID` = `UserID`) AS `Name`',
				'(SELECT FROM_UNIXTIME(`RegisterDate`) FROM `Users` WHERE `Users`.`ID` = `UserID`) AS `RegisterDate`',
				'(SELECT `Email` FROM `Users` WHERE `Users`.`ID` = `UserID`) AS `Email`',
				'(SELECT `Params` FROM `Users` WHERE `Users`.`ID` = `UserID`) AS `Attribs`'	// чтою Json декодировало
				);
		#-------------------------------------------------------------------------------
		$Lines = DB_Select('TmpData',$Columns,Array('Where'=>Array('`AppID` = "YandexMetrika"','`Col1` = "Orders"'),'SortOn'=>'CreateDate'));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Lines)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return Array('Orders'=>$Orders,'Deleted'=>Array());
		case 'array':
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}       
		#-------------------------------------------------------------------------------
		// перебираем юзеров, строим массив с контактами и массив на удаление записей
		$Deleted = Array();
		#-------------------------------------------------------------------------------
		foreach($Lines as $Line){
			#-------------------------------------------------------------------------------
			// на удаление
			$Deleted[] = $Line['ID'];
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			// в яндекс отправлять
			if($Line['Params']['order_status'] == 'PAID'){
				#-------------------------------------------------------------------------------
				$Orders['PAID'][] = $Line['Params'];
				#-------------------------------------------------------------------------------
			}elseif($Line['Params']['order_status'] == 'IN_PROGRESS'){
				#-------------------------------------------------------------------------------
				$Orders['IN_PROGRESS'][] = $Line['Params'];
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				$Orders['CANCELLED'][] = $Line['Params'];
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		return Array('Orders'=>$Orders,'Deleted'=>$Deleted);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	}

	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// удаление загруженных записей из таблицы временных данных
	public function DeleteRecords($Deleted = Array()){
		#-------------------------------------------------------------------------------
		if(!SizeOf($Deleted))
			return TRUE;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$IsDelete = DB_Delete('TmpData',Array('Where'=>SPrintF('`ID` IN (%s)',Implode(',',$Deleted))));
		if(Is_Error($IsDelete))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		return TRUE;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	}





}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>