<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('StatusID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Statuses = $Config['Statuses']['HostingOrders'];
#-------------------------------------------------------------------------------
$Color = IsSet($Statuses[$StatusID]['Color'])?$Statuses[$StatusID]['Color']:999999;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('bgcolor'=>SPrintF('#%s',$Color));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
