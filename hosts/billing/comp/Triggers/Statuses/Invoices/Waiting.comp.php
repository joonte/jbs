<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Invoice');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Comp = Comp_Load('Invoices/Build',$Invoice['ID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Interface']['User']['YandexMetrika'];
#-------------------------------------------------------------------------------
// если метрика НЕ включена, то всё
if(!$Settings['IsActive'] || !$Settings['YandexCounterId'] || !$Settings['Token'])
	return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Query = Array(
		'id'			=> $Invoice['ID'],
		'client_uniq_id'	=> $Invoice['UserID'],
		'client_type'		=> 'CONTACT',
		'create_date_time'	=> SPrintF('%s %s',Date('Y-m-d',$Invoice['CreateDate']),Date('G:i:s',$Invoice['CreateDate'])),
		'order_status'		=> 'IN_PROGRESS',
		'revenue'		=> $Invoice['Summ']
		);
#-------------------------------------------------------------------------------
$IsInsert = DB_Insert('TmpData',Array('UserID'=>$Invoice['UserID'],'AppID'=>'YandexMetrika','Col1'=>'Orders','Params'=>$Query));
if(Is_Error($IsInsert))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
