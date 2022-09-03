<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/HTTP.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Messages = Messages();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!IsSet($Args['code']))
	return TemplateReplace('OAuth.Error',Array('TEXT'=>$Messages['Errors']['OAuth']['NoCode']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Interface']['User']['OAuth']['Yandex'];
#-------------------------------------------------------------------------------
if(!$Settings['IsActive'])
	return TemplateReplace('OAuth.Error',Array('TEXT'=>$Messages['Errors']['OAuth']['Disabled']));
#-------------------------------------------------------------------------------
if(!$Settings['ClientSecret'] || !$Settings['ClientID'])
	return TemplateReplace('OAuth.Error',Array('TEXT'=>$Messages['Errors']['OAuth']['NoSettings']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// код для получения токена, меняем на токен
$HTTP = Array(
		'Address'	=> 'oauth.yandex.ru',
		'Port'		=> 443,
		'Host'		=> 'oauth.yandex.ru',
		'Protocol'	=> 'ssl',
		);
#-------------------------------------------------------------------------------
$Body = Array(
		'grant_type'	=> 'authorization_code',
		'code'		=> $Args['code'],
		'client_id'	=> $Settings['ClientID'],
		'client_secret'	=> $Settings['ClientSecret'],
		);
#-------------------------------------------------------------------------------
$Result = HTTP_Send('/token',$HTTP,Array(),$Body,Array());
if(Is_Error($Result))
	return ERROR | @Trigger_Error('[API]: не удалось выполнить запрос к серверу oauth.yandex.ru');
#-------------------------------------------------------------------------------
$Result = Json_Decode(Trim($Result['Body']),TRUE);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// по полученному токену, достаём данные клиента
$HTTP = Array(
		'Address'	=> 'login.yandex.ru',
		'Port'		=> 443,
		'Host'		=> 'login.yandex.ru',
		'Protocol'	=> 'ssl',
		);
#-------------------------------------------------------------------------------
$Query = Array('format'=>'json','oauth_token'=>$Result['access_token']);
#-------------------------------------------------------------------------------
$Result = HTTP_Send('/info',$HTTP,$Query,Array(),Array());
#-------------------------------------------------------------------------------
if(Is_Error($Result))
	return ERROR | @Trigger_Error('[API]: не удалось выполнить запрос к серверу login.yandex.ru');
#-------------------------------------------------------------------------------
$Result = Json_Decode(Trim($Result['Body']),TRUE);
#-------------------------------------------------------------------------------
if(!$Result['default_email'])
	return TemplateReplace('OAuth.Error',Array('TEXT'=>$Messages['Errors']['OAuth']['NoEmail']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# TODO портрет юзера
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// данные для аккаунта
$Address= $Result['default_email'];
$UserID	= $Result['id'];
$Name	= $Result['display_name'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsUser = Comp_Load('OAuth/ManageAccount','Yandex',$Address,$UserID,$Name);
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/www/OAuth/Yandex]: IsUser = %s',print_r($IsUser,true)));
switch(ValueOf($IsUser)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return TemplateReplace('OAuth.Error',Array('TEXT'=>$IsUser->String));
        return $IsUser->String;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// делаем Logon юзера
$User = Comp_Load('www/API/Logon',Array('Email'=>$Address));
if(Is_Error($User))
        return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// закрываем окно
return TemplateReplace('OAuth.Error',Array('TEXT'=>$Messages['Errors']['OAuth']['NoEmail'],'CLOSE'=>1));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
