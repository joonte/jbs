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
$ISPswOrders = DB_Select('ISPswOrdersOwners',Array('ID','DaysRemainded','IP',
	'(SELECT `Name` FROM `ISPswSchemes` WHERE `ISPswOrdersOwners`.`SchemeID` = `ISPswSchemes`.`ID`) as `SchemeName`',
	'(SELECT `IsProlong` FROM `ISPswSchemes` WHERE `ISPswOrdersOwners`.`SchemeID` = `ISPswSchemes`.`ID`) as `IsProlong`'
	),Array('Where'=>"`UserID` = @local.__USER_ID AND (`DaysRemainded` < 15 OR `DaysRemainded` IS NULL) AND `StatusID` = 'Active'"));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($ISPswOrders as $ISPswOrder){
      #-------------------------------------------------------------------------
      $NoBody = new Tag('NOBODY');
      #-------------------------------------------------------------------------
      $DaysRemainded = $ISPswOrder['DaysRemainded'];
#-------------------------------------------------------------------------------
      if($ISPswOrder['IsProlong'] == "yes"){
$Parse = <<<EOD
<NOBODY>
<SPAN>Обращаем Ваше внимание, что истекает срок действия заказа на программное обеспечение </SPAN>
<SPAN> ISPsystem, </SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
<SPAN> и в случае не поступления оплаты в течение </SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
<SPAN> дня(ей) заказ будет заблокирован. Для того, чтобы осуществить оплату сейчас, нажмите на кнопку </SPAN>
<A style="font-size:14px;font-weight:bold;" href="javascript:ShowWindow('/ISPswOrderPay',{ISPswOrderID:%u});">[оплатить]</A>
</NOBODY>
EOD;
      }else{
#-------------------------------------------------------------------------------
$Parse = <<<EOD
<NOBODY>
<SPAN>Обращаем Ваше внимание, что истекает срок действия заказа на программное обеспечение </SPAN>
<SPAN> ISPsystem, </SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>.
<SPAN>Через </SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
<SPAN> дня(ей) заказ будет заблокирован. Используемый тарифный план не позволяет продление, но, вы можете сменить его на другой. Для смены тарифного плана, нажмите на кнопку </SPAN>
<A style="font-size:14px;font-weight:bold;" href="javascript:ShowWindow('/ISPswOrderSchemeChange',{ISPswOrderID:%u});">[сменить тариф]</A>
</NOBODY>
EOD;
      }
#-------------------------------------------------------------------------------
      $NoBody->AddHTML(SPrintF($Parse,$ISPswOrder['SchemeName'],$DaysRemainded?$DaysRemainded:'сегодняшнего',$ISPswOrder['ID']));
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
