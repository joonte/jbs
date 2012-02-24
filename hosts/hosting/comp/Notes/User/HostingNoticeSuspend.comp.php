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
$Columns = Array(
			'HostingOrdersOwners.ID',
			'Login',
			'`DaysRemainded`',
			'(SELECT `Name` FROM `HostingSchemes` WHERE `HostingOrdersOwners`.`SchemeID` = `HostingSchemes`.`ID`) as `SchemeName`',
			'(SELECT `IsProlong` FROM `HostingSchemes` WHERE `HostingOrdersOwners`.`SchemeID` = `HostingSchemes`.`ID`) as `IsProlong`'
		);
$HostingOrders = DB_Select('HostingOrdersOwners',$Columns,Array('Where'=>"`UserID` = @local.__USER_ID AND (`DaysRemainded` < 15 OR `DaysRemainded` IS NULL) AND HostingOrdersOwners.`StatusID` = 'Active'"));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($HostingOrders as $HostingOrder){
      #-------------------------------------------------------------------------
      $NoBody = new Tag('NOBODY');
      #-------------------------------------------------------------------------
      $DaysRemainded = $HostingOrder['DaysRemainded'];
#-------------------------------------------------------------------------------
      if($HostingOrder['IsProlong'] == "yes"){
$Parse = <<<EOD
<NOBODY>
<SPAN>Обращаем Ваше внимание, что истекает срок действия заказа на хостинг </SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
<SPAN> с тарифным планом </SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
<SPAN> и в случае не поступления оплаты в течение </SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
<SPAN> дня(ей) он будет заблокирован. Для того, чтобы осуществить оплату сейчас, нажмите на кнопку </SPAN>
<A style="font-size:14px;font-weight:bold;" href="javascript:ShowWindow('/HostingOrderPay',{HostingOrderID:%u});">[оплатить]</A>
</NOBODY>
EOD;
      }else{
#-------------------------------------------------------------------------------
$Parse = <<<EOD
<NOBODY>
<SPAN>Обращаем Ваше внимание, что истекает срок действия заказа на хостинг </SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
<SPAN> с тарифным планом </SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>.
<SPAN>Через </SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
<SPAN> дня(ей) он будет заблокирован. Используемый тарифный план не позволяет продление, но, вы можете сменить его на другой. Для смены тарифного плана, нажмите на кнопку </SPAN>
<A style="font-size:14px;font-weight:bold;" href="javascript:ShowWindow('/HostingOrderSchemeChange',{HostingOrderID:%u});">[сменить тариф]</A>
</NOBODY>
EOD;
      }
#-------------------------------------------------------------------------------
      $NoBody->AddHTML(SPrintF($Parse,$HostingOrder['Login'],$HostingOrder['SchemeName'],$DaysRemainded?$DaysRemainded:'сегодняшнего',$HostingOrder['ID']));
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
