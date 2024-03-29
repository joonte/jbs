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
	return TemplateReplace('OAuth.Error',Array('TEXT'=>$Messages['Errors']['OAuth']['NoCode']),FALSE);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Interface']['User']['OAuth']['Google'];
#-------------------------------------------------------------------------------
if(!$Settings['IsActive'])
	return TemplateReplace('OAuth.Error',Array('TEXT'=>$Messages['Errors']['OAuth']['Disabled']),FALSE);
#-------------------------------------------------------------------------------
if(!$Settings['ClientSecret'] || !$Settings['ClientID'])
	return TemplateReplace('OAuth.Error',Array('TEXT'=>$Messages['Errors']['OAuth']['NoSettings']),FALSE);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// google скидывает нам код для получения токена, меняем его на токен
$HTTP = Array(
		'Address'	=> 'accounts.google.com',
		'Port'		=> 443,
		'Host'		=> 'accounts.google.com',
		'Protocol'	=> 'ssl',
		);
#-------------------------------------------------------------------------------
$Body = Array(
		'grant_type'	=> 'authorization_code',
		'code'		=> $Args['code'],
		'client_id'	=> $Settings['ClientID'],
		'client_secret'	=> $Settings['ClientSecret'],
		'redirect_uri'	=> SPrintF('%s://%s/OAuth/Google',URL_SCHEME,HOST_ID),
		);
#-------------------------------------------------------------------------------
$Result = HTTP_Send('/o/oauth2/token',$HTTP,Array(),$Body,Array());
if(Is_Error($Result))
	return ERROR | @Trigger_Error('[API]: не удалось выполнить запрос к серверу accounts.google.com');
#-------------------------------------------------------------------------------
$Result = Json_Decode(Trim($Result['Body']),TRUE);
#-------------------------------------------------------------------------------
if(!IsSet($Result['access_token']))
	return TemplateReplace('OAuth.Error',Array('TEXT'=>$Messages['Errors']['OAuth']['NoAccessToken']),FALSE);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// по полученному токену, достаём данные клиента
$HTTP = Array(
		'Address'	=> 'www.googleapis.com',
		'Port'		=> 443,
		'Host'		=> 'www.googleapis.com',
		'Protocol'	=> 'ssl',
		);
#-------------------------------------------------------------------------------
$Query = Array('format'=>'json','access_token'=>$Result['access_token']);
#-------------------------------------------------------------------------------
$Result = HTTP_Send('/oauth2/v1/userinfo',$HTTP,$Query,Array(),Array());
#-------------------------------------------------------------------------------
if(Is_Error($Result))
	return ERROR | @Trigger_Error('[API]: не удалось выполнить запрос к серверу www.googleapis.com');
#-------------------------------------------------------------------------------
$Result = Json_Decode(Trim($Result['Body']),TRUE);
#-------------------------------------------------------------------------------
if(!IsSet($Result['email']))
	return TemplateReplace('OAuth.Error',Array('TEXT'=>$Messages['Errors']['OAuth']['NoEmail']),FALSE);
#-------------------------------------------------------------------------------
// почта может быть не верифицирована, судя по параметрам ответа
if(!$Result['verified_email'])
	return TemplateReplace('OAuth.Error',Array('TEXT'=>SPrintF($Messages['Errors']['OAuth']['NoVerifiedEmail'],'Google')),FALSE);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# TODO портрет юзера
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// данные для аккаунта
$Address= $Result['email'];
$UserID	= $Result['id'];
$Name	= $Result['name'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsUser = Comp_Load('OAuth/ManageAccount','Google',$Address,$UserID,$Name);
#-------------------------------------------------------------------------------
//Debug(SPrintF('[comp/www/OAuth/Google]: IsUser = %s',print_r($IsUser,true)));
switch(ValueOf($IsUser)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return TemplateReplace('OAuth.Error',Array('TEXT'=>$IsUser->String),FALSE);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// закрываем окно
return TemplateReplace('OAuth.Error',Array('TEXT'=>$Messages['Errors']['OAuth']['Completed'],'CLOSE'=>1),FALSE);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
