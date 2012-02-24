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
$VPSOrders = DB_Select('VPSOrdersOwners',Array('ID','Login','DaysRemainded','(SELECT `Name` FROM `VPSSchemes` WHERE `VPSOrdersOwners`.`SchemeID` = `VPSSchemes`.`ID`) as `SchemeName`'),Array('Where'=>"`UserID` = @local.__USER_ID AND (`DaysRemainded` < 15 OR `DaysRemainded` IS NULL) AND `StatusID` = 'Active'"));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($VPSOrders as $VPSOrder){
      #-------------------------------------------------------------------------
      $NoBody = new Tag('NOBODY');
      #-------------------------------------------------------------------------
      $DaysRemainded = $VPSOrder['DaysRemainded'];
#-------------------------------------------------------------------------------
$Parse = <<<EOD
<NOBODY>
<SPAN>Обращаем Ваше внимание, что истекает срок действия заказа на виртуальный сервер </SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
<SPAN> с тарифным планом </SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
<SPAN> и в случае не поступления оплаты в течение </SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
<SPAN> дня(ей) он будет заблокирован. Для того, чтобы осуществить оплату сейчас, нажмите на кнопку </SPAN>
<A style="font-size:14px;font-weight:bold;" href="javascript:ShowWindow('/VPSOrderPay',{VPSOrderID:%u});">[оплатить]</A>
</NOBODY>
EOD;
#-------------------------------------------------------------------------------
      $NoBody->AddHTML(SPrintF($Parse,$VPSOrder['Login'],$VPSOrder['SchemeName'],$DaysRemainded?$DaysRemainded:'сегодняшнего',$VPSOrder['ID']));
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
