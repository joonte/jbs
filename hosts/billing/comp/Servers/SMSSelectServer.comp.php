<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Mobile');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/Server.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ServersSettings = SelectServerSettingsByTemplate('SMS',FALSE);
#-------------------------------------------------------------------------------
switch(ValueOf($ServersSettings)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo'] = 'server with template: SMS, params: IsActive, IsDefault not found';
	#-------------------------------------------------------------------------------
	if(IsSet($GLOBALS['IsCron']))
		return 3600;
	#-------------------------------------------------------------------------------
	return $ServersSettings;
	#-------------------------------------------------------------------------------
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#Debug(SPrintF('[comp/Servers/SMSSelectServer]: $ServersSettings =  %s', print_r($ServersSettings,true)));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
$MobileCountry = 'PriceDefault';
#-------------------------------------------------------------------------------
$RegCountrys = Array(
			'PriceRu'       => $Regulars['SMSPriceRu'],
			'PriceUa'       => $Regulars['SMSPriceUa'],
			'PriceSng'      => $Regulars['SMSPriceSng'],
			'PriceZone1'    => $Regulars['SMSPriceZone1'],
			'PriceZone2'    => $Regulars['SMSPriceZone2']
			);
#-------------------------------------------------------------------------------
foreach($RegCountrys as $RegCountryKey => $RegCountry)
        if(Preg_Match($RegCountry, $Mobile))
                $MobileCountry = $RegCountryKey;
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/Servers/SMSSelectServer]: Страна определена (%s)', $MobileCountry));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# перебираем сервера, сразу выставляем использовать первый же из списка и его цену для этой страны
$Server = 0;
#-------------------------------------------------------------------------------
$Price = $ServersSettings[$Server]['Params'][$MobileCountry];
#-------------------------------------------------------------------------------
foreach($ServersSettings as $ServerKey => $Key){
	#-------------------------------------------------------------------------------
	# сравниваем цену сообщения для этой страны, с ранее найденной минимальной ценой
	if($ServersSettings[$ServerKey]['Params'][$MobileCountry] < $Price){
		#-------------------------------------------------------------------------------
		$Server = $ServerKey;
		#-------------------------------------------------------------------------------
		$Price = $ServersSettings[$ServerKey]['Params'][$MobileCountry];
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$ServerSettings = $ServersSettings[$Server];
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/Servers/SMSSelectServer]: найден самый дешовый сервер (%s) для страны (%s), цена (%s)',$ServerSettings['Address'],$MobileCountry,$ServerSettings['Params'][$MobileCountry]));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Currency',Str_Replace(',','.',$ServerSettings['Params'][$MobileCountry]));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array($ServerSettings,$MobileCountry,$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>





