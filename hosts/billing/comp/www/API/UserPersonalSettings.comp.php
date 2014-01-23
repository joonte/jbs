<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$EdesksDisplay			=  (string) @$Args['EdesksDisplay'];
$EdeskNoPreview			=  (string) @$Args['EdeskNoPreview'];
$EdeskOnlyMyButtons		=  (string) @$Args['EdeskOnlyMyButtons'];
$NotSendEdeskFilesToEmail	= (boolean) @$Args['NotSendEdeskFilesToEmail'];
$NotCreateInvoicesAutomatically = (boolean) @$Args['NotCreateInvoicesAutomatically'];
$CreateInvoicesAutomatically	=   (array) @$Args['CreateInvoicesAutomatically'];
$SMSBeginTime			= (integer) @$Args['SMSBeginTime'];
$SMSEndTime			= (integer) @$Args['SMSEndTime'];
#-------------------------------------------------------------------------------
#Debug(SPrintF('[comp/www/API/UserPersonalSettings]: CreateInvoicesAutomatically = %s',print_r($CreateInvoicesAutomatically,true)));
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/Session.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$Settings = $__USER['Params'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Settings['CreateInvoicesAutomatically'] = $CreateInvoicesAutomatically;
#-------------------------------------------------------------------------------
$Settings['NotSendEdeskFilesToEmail'] = $NotSendEdeskFilesToEmail;
#-------------------------------------------------------------------------------
$Settings['NotCreateInvoicesAutomatically'] = $NotCreateInvoicesAutomatically;
#-------------------------------------------------------------------------------
$Settings['SMSTime'] = Array('SMSBeginTime'=>$SMSBeginTime,'SMSEndTime'=>$SMSEndTime);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# перебрать все типы договоров, проверить $Settings['CreateInvoicesAutomatically']
$Config = Config();
$ContractsTypes = $Config['Contracts']['Types'];
#-------------------------------------------------------------------------------
$Array = Array();
#-------------------------------------------------------------------------------
foreach(Array_Keys($ContractsTypes) as $Type)
	$Array[] = $Type;
#-------------------------------------------------------------------------------
foreach(Array_Keys($Settings['CreateInvoicesAutomatically']) as $Type)
	if(!In_Array($Type,$Array))
		UnSet($Settings['CreateInvoicesAutomatically'][$Type]);
#-------------------------------------------------------------------------------
# перебрать все типы платёжных систем, проверить $Settings['CreateInvoicesAutomatically']
$PaymentSystems = $Config['Invoices']['PaymentSystems'];
#-------------------------------------------------------------------------------
$Array = Array();
#-------------------------------------------------------------------------------
foreach(Array_Keys($PaymentSystems) as $PaymentSystemID){
	#-------------------------------------------------------------------------------
	if(!$PaymentSystems[$PaymentSystemID]['IsActive'])
		continue;
	#-------------------------------------------------------------------------------
	$Array[] = $PaymentSystemID;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
foreach(Array_Keys($Settings['CreateInvoicesAutomatically']) as $Type)
	if(!In_Array($Settings['CreateInvoicesAutomatically'][$Type],$Array))
		UnSet($Settings['CreateInvoicesAutomatically'][$Type]);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsUpdate = DB_Update('Users',Array('Params'=>$Settings),Array('ID'=>$__USER['ID']));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$SessionID = (IsSet($_COOKIE['SessionID']) && StrLen($_COOKIE['SessionID'])?$_COOKIE['SessionID']:UniqID('SESSION'));
#-------------------------------------------------------------------------------
$Session = new Session($SessionID);
#-------------------------------------------------------------------------------
$IsLoad = $Session->Load();
if(Is_Error($IsLoad))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($EdesksDisplay && !SetCookie('EdesksDisplay',$EdesksDisplay,Time() + 2678400,'/'))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Expired = $EdeskNoPreview?(Time() + 2678400):(Time() - 2678400);
if(!SetCookie('EdeskNoPreview',$EdeskNoPreview,$Expired,'/'))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Expired = $EdeskOnlyMyButtons?(Time() + 2678400):(Time() - 2678400);
if(!SetCookie('EdeskOnlyMyButtons',$EdeskOnlyMyButtons,$Expired,'/'))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error($Session->Save()))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
