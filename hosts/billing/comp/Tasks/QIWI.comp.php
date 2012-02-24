<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Invoices']['PaymentSystems']['QIWI'];
#-------------------------------------------------------------------------------
# проверяем, активна ли платёжная система. если нет - следующая проверка через час
if($Settings['IsActive'] != 1){return (time() + 3600);}
#-------------------------------------------------------------------------------
$NumInvoices = 0;
$NumPayed = 0;
#-------------------------------------------------------------------------------
# грузим либы
if(Is_Error(System_Load('libs/Http.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# исключаем отменённые счета, если число минут не кратно 5 (исполняться это будет редко - раз в час, примерно)
$Where = Array(
		"`PaymentSystemID`='QIWI'",
		"`StatusID`!='Payed'",
		SPrintF('(UNIX_TIMESTAMP() - `CreateDate`) < (%u * 3600)', $Settings['Send']['lifetime']),
		);
if(date('i', time())%5 == 0){
	Debug("[Tasks/QIWI]: processing QIWI with rejected invoices");
}else{
	$Where[] = "`StatusID`!='Rejected'";
	Debug("[Tasks/QIWI]: processing QIWI without rejected invoices");
}
# выбираем счета QIWI где дней < 45 и статус отличается от "оплачен"
$Items = DB_Select('Invoices',Array('ID','Summ','StatusID'),Array('SortOn'=>'CreateDate', 'IsDesc'=>TRUE, 'Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($Items)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
		break;
	case 'array':
		#Debug(print_r($Items, true));
		#---------------------------------------------------------------------------
		$bill_list = "";
		foreach($Items as $Item){
			#-------------------------------------------------------------------------
			#Debug("[Tasks/QIWI]: processing invoice #" . $Item['ID']);
			$bill_list .= "\t\t" . SPrintF('<bill txn-id="%s"/>', $Item['ID']) . "\n";
			#-------------------------------------------------------------------------
			$NumInvoices++;
		}
		# create request
		$Result = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<request>
	<protocol-version>4.00</protocol-version>
	<request-type>33</request-type>
	<extra name="password">%s</extra>
	<terminal-id>%s</terminal-id>
	<bills-list>
	%s
	</bills-list>
</request>
EOD;
		$Result = SPrintF(trim($Result), $Settings['Hash'], $Settings['Send']['from'], $bill_list);
		#Debug("[Tasks/QIWI]: " . $Send);
		# calculate encrypt key
		$passwordMD5 = md5($Settings['Hash'], true);
		$salt = md5($Settings['Send']['from'] . bin2hex($passwordMD5), true);
		$key = str_pad($passwordMD5, 24, '\0');
		# XOR calculating
		for ($i = 8; $i < 24; $i++) {
			if ($i >= 16) {
			$key[$i] = $salt[$i-8];
			} else {
				$key[$i] = $key[$i] ^ $salt[$i-8];
			}
		}
		# create message
		$n = 8 - strlen($Result) % 8;
		$pad = str_pad($Result, strlen($Result) + $n, ' ');
		# crypt message
		$crypted = mcrypt_encrypt(MCRYPT_3DES, $key, $pad, MCRYPT_MODE_ECB, "\0\0\0\0\0\0\0\0");
		$Result = "qiwi" . str_pad($Settings['Send']['from'], 10, "0", STR_PAD_LEFT) . "\n";
		$Result .= base64_encode($crypted);
		# send message to QIWI server
		$Http = Array('Protocol'=>'ssl','Port'=>'443','Address'=>'ishop.qiwi.ru','Host'=>'ishop.qiwi.ru');
		$Send = Http_Send('/xml',$Http,Array(),$Result,Array('Content-type: text/xml; encoding=utf-8'));
		if(Is_Error($Send))
			return (time() + 60 * $Settings['RequestPeriod']);
		#-------------------------------------------------------------------------
		# parse XML
		$XML = String_XML_Parse($Send['Body']);
		if(Is_Exception($XML))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		# TODO need use internal functions for work with XML
		# 2011-06-17 in 13:56 MSK, lissyara
		#-------------------------------------------------------------------------------
		$Send = Trim($Send['Body']);
		$xml = simplexml_load_string('<?xml version="1.0" encoding="utf-8"?>' . $Send);
		if ($xml->{'result-code'}){
			$result = array();
			foreach ($xml->{'bills-list'}->children() as $bill) {
				#-------------------------------------------------------------------------
				Debug("[Tasks/QIWI]: status for #" . $bill['id'] . " = '" . $bill['status'] . "', summ = " . $bill['sum']);
				if($bill['status'] == 60){
					#-------------------------------------------------------------------------
					$Invoice = DB_Select('Invoices',Array('ID','Summ'),Array('UNIQ','ID'=>$bill['id']));
					switch(ValueOf($Invoice)){
					case 'error':
						return ERROR | @Trigger_Error(500);
					case 'exception':
						return ERROR | @Trigger_Error(400);
					case 'array':
						#-------------------------------------------------------------------------
						$summ = $Invoice['Summ'];
						$InvoiceID = $Invoice['ID'];
						#-------------------------------------------------------------------------
						if($summ == $bill['sum']){
							#-------------------------------------------------------------------------
							Debug("[Tasks/QIWI]: summ compare success for #" . $Invoice['ID']);
							#-------------------------------------------------------------------------
							$Comp = Comp_Load('Users/Init',100);
							if(Is_Error($Comp))
								return ERROR | @Trigger_Error(500);
							#-------------------------------------------------------------------------
							$Comp = Comp_Load('www/API/StatusSet',
									Array(	'ModeID'	=>'Invoices',
										'IsNotNotify'	=>TRUE,
										'IsNoTrigger'	=>FALSE,
										'StatusID'	=>'Payed',
										'RowsIDs'	=>$InvoiceID,
										'Comment'	=>'Автоматическое зачисление'
										)
									);
							#-------------------------------------------------------------------------
							switch(ValueOf($Comp)){
							case 'error':
								return ERROR | @Trigger_Error(500);
							case 'exception':
								return ERROR | @Trigger_Error(400);
							case 'array':
								Debug("[Tasks/QIWI]: Payment #$InvoiceID success");
								break;
							default:
								return ERROR | @Trigger_Error(101);
							}
						}else{
							Debug("[Tasks/QIWI]: Incorrect summ for #" . $InvoiceID . ", billing have " . $summ . ", QIWI return " . $bill['sum']);
						}
						#-------------------------------------------------------------------------
						break;
						#-------------------------------------------------------------------------
					default:
					      return ERROR | @Trigger_Error(101);
					}
					#-------------------------------------------------------------------------
					$NumPayed++;
				}elseif($bill['status'] == 150){
					# проверяем, не отменён ли он уже в биллинге.
					# иначе будет каждый запуск его отменять, и так 40 дней
					foreach($Items as $Item){
						if($Item['ID'] == $bill['id'] && $Item['StatusID'] != 'Rejected'){
					
							#-------------------------------------------------------------------------
							$Comp = Comp_Load('Users/Init',100);
							if(Is_Error($Comp))
								return ERROR | @Trigger_Error(500);
							#-------------------------------------------------------------------------
							$Comp = Comp_Load('www/API/StatusSet',
									Array(  'ModeID'        =>'Invoices',
										'IsNotNotify'   =>TRUE,
										'IsNoTrigger'   =>FALSE,
										'StatusID'      =>'Rejected',
										'RowsIDs'       =>$bill['id'],
										'Comment'       =>'Клиент отказался от оплаты счёта в терминале'
										)
									);
							#-------------------------------------------------------------------------
							switch(ValueOf($Comp)){
							case 'error':
								return ERROR | @Trigger_Error(500);
							case 'exception':
								return ERROR | @Trigger_Error(400);
							case 'array':
								Debug("[Tasks/QIWI]: Payment #" . $bill['id'] . " canceled, using terminal");
								break;
							default:
								return ERROR | @Trigger_Error(101);
							}
						}
					}
				}
			}
		}else{
			Debug("[Tasks/QIWI]: XML error, result code '" . $xml->{'result-code'} . "'");
			#return ERROR | @Trigger_Error(500);
			# service error, go executing to later
			return (time() + 3600);
		}
		break;
	default:
		return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($NumInvoices > 0){
	$GLOBALS['TaskReturnInfo'] = Array(SPrintF('Invoices: %u',$NumInvoices));
	if($NumPayed > 0){
		$GLOBALS['TaskReturnInfo'][] = SPrintF('Payed: %u',$NumPayed);
	}
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$RequestPeriod = $Config['Tasks']['Types']['QIWI']['RequestPeriod'];
# set next run +$Settings['RequestPeriod'] min to future
if(intval($RequestPeriod) < 1){
	# time not set, or incorrect. go to default value - 5 minutes
	return(time() +300);
}else{
	return (time() + 60 * $RequestPeriod);
}

# functions, from https://ishop.qiwi.ru/docs/qiwi-php-xml/simple_crypt.php
# deleted, because from cron it redeclared on second cron run


?>
