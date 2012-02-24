<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Result = Array();
#-------------------------------------------------------------------------------
$ExtraIPOrders = DB_Select('ExtraIPOrdersOwners',Array('ID','DependOrderID','DaysRemainded','OrderType','(SELECT `Name` FROM `ExtraIPSchemes` WHERE `ExtraIPOrdersOwners`.`SchemeID` = `ExtraIPSchemes`.`ID`) as `SchemeName`'),Array('Where'=>"`UserID` = @local.__USER_ID AND (`DaysRemainded` < 15 OR `DaysRemainded` IS NULL) AND `StatusID` = 'Active'"));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($ExtraIPOrders as $ExtraIPOrder){
	#-------------------------------------------------------------------------
	$NoBody = new Tag('NOBODY');
	#-------------------------------------------------------------------------
	$DaysRemainded = $ExtraIPOrder['DaysRemainded'];
	#-------------------------------------------------------------------------------
	if($ExtraIPOrder['OrderType'] == "Hosting"){
		$part = "подключенного к заказу хостинга";
	}elseif($ExtraIPOrder['OrderType'] == "VPS"){
		$part = "подключенного к заказу VPS";
	}elseif($ExtraIPOrder['OrderType'] == "DS"){
		$part = "подключенного к заказу арендованного сервера";
	}elseif($ExtraIPOrder['OrderType'] == "Manual"){
		$part = " ";
	}else{
		$part = " ";
	}

$Parse = <<<EOD
<NOBODY>
<SPAN>Обращаем Ваше внимание, что истекает срок действия заказа на выделенный IP адрес, </SPAN>
<SPAN>%s</SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
<SPAN> и, в случае не поступления оплаты в течение </SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
<SPAN> дня(ей), он будет заблокирован. Для того, чтобы осуществить оплату сейчас, нажмите на кнопку </SPAN>
<A style="font-size:14px;font-weight:bold;" href="javascript:ShowWindow('/ExtraIPOrderPay',{ExtraIPOrderID:%u});">[оплатить]</A>
</NOBODY>
EOD;
	#-------------------------------------------------------------------------------
	if($ExtraIPOrder['DependOrderID'] == 0){$ExtraIPOrder['DependOrderID'] = " ";}
	$NoBody->AddHTML(SPrintF($Parse, $part, $ExtraIPOrder['DependOrderID'],$DaysRemainded?$DaysRemainded:'сегодняшнего',$ExtraIPOrder['ID']));
	#-------------------------------------------------------------------------
	$Result[] = $NoBody;
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
return $Result;
#-------------------------------------------------------------------------------


?>
