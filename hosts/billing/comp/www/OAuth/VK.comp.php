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
$Settings = $Config['Interface']['User']['OAuth']['VK'];
#-------------------------------------------------------------------------------
if(!$Settings['IsActive'])
	return TemplateReplace('OAuth.Error',Array('TEXT'=>$Messages['Errors']['OAuth']['Disabled']),FALSE);
#-------------------------------------------------------------------------------
if(!$Settings['ClientSecret'] || !$Settings['ClientID'])
	return TemplateReplace('OAuth.Error',Array('TEXT'=>$Messages['Errors']['OAuth']['NoSettings']),FALSE);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// код для получения токена меняем на токен, при этом ВК отдаёт сразу и данные юзера
$HTTP = Array(
		'Address'	=> 'oauth.vk.com',
		'Port'		=> 443,
		'Host'		=> 'oauth.vk.com',
		'Protocol'	=> 'ssl',
		);
#-------------------------------------------------------------------------------
$Body = Array(
		'code'		=> $Args['code'],
		'client_id'	=> $Settings['ClientID'],
		'client_secret'	=> $Settings['ClientSecret'],
		'redirect_uri'	=> SPrintF('%s://%s/OAuth/VK',URL_SCHEME,HOST_ID),
		);
#-------------------------------------------------------------------------------
$Result = HTTP_Send('/access_token',$HTTP,Array(),$Body,Array());
if(Is_Error($Result))
	return ERROR | @Trigger_Error('[API]: не удалось выполнить запрос к серверу oauth.vk.com');
#-------------------------------------------------------------------------------
$Result = Json_Decode(Trim($Result['Body']),TRUE);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!IsSet($Result['email']))
	return TemplateReplace('OAuth.Error',Array('TEXT'=>$Messages['Errors']['OAuth']['NoEmail']),FALSE);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# TODO портрет юзера
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// данные для аккаунта
$Address= $Result['email'];
$UserID	= $Result['user_id'];
$Name	= FALSE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsUser = Comp_Load('OAuth/ManageAccount','VK',$Address,$UserID,$Name);
#-------------------------------------------------------------------------------
//Debug(SPrintF('[comp/www/OAuth/VK]: IsUser = %s',print_r($IsUser,true)));
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
