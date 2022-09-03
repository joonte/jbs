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
$Settings = $Config['Interface']['User']['OAuth']['VK'];
#-------------------------------------------------------------------------------
if(!$Settings['IsActive'])
	return TemplateReplace('OAuth.Error',Array('TEXT'=>$Messages['Errors']['OAuth']['Disabled']));
#-------------------------------------------------------------------------------
if(!$Settings['ClientSecret'] || !$Settings['ClientID'])
	return TemplateReplace('OAuth.Error',Array('TEXT'=>$Messages['Errors']['OAuth']['NoSettings']));
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
if(!$Result['email'])
	return TemplateReplace('OAuth.Error',Array('TEXT'=>$Messages['Errors']['OAuth']['NoEmail']));
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
Debug(SPrintF('[comp/www/OAuth/VK]: IsUser = %s',print_r($IsUser,true)));
switch(ValueOf($IsUser)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return TemplateReplace('OAuth.Error',Array('TEXT'=>$IsUser->String));
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
