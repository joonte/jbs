<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$ISPswLicenseID	= (integer) @$Args['ISPswLicenseID'];
$Flag		=  (string) @$Args['Flag'];
#$pricelist_id	= (integer) @$Args['pricelist_id'];
#$period	=  (string) @$Args['period'];
#$addon		= (integer) @$Args['addon'];
$IsInternal	= (boolean) @$Args['IsInternal'];
$IsUsed		= (boolean) @$Args['IsUsed'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Count = DB_Count('ISPswLicenses',Array('ID'=>$ISPswLicenseID));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
	return new gException('LICENSE_NOT_FOUND','Лицензия не найдена');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IISPswLicense = Array(
			#'pricelist_id'	=> $pricelist_id,
			#'period'	=> $period,
			#'addon'	=> $addon,
			'Flag'		=> $Flag,
			'IsInternal'	=> $IsInternal,
			'IsUsed'	=> $IsUsed,
			);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsUpdate = DB_Update('ISPswLicenses',$IISPswLicense,Array('ID'=>$ISPswLicenseID));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
