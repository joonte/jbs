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
$DSOrders = DB_Select('DSOrdersOwners',Array('ID','IP','DaysRemainded','(SELECT `Name` FROM `DSSchemes` WHERE `DSOrdersOwners`.`SchemeID` = `DSSchemes`.`ID`) as `SchemeName`'),Array('Where'=>"`UserID` = @local.__USER_ID AND (`DaysRemainded` < 15 OR `DaysRemainded` IS NULL) AND `StatusID` = 'Active'"));
#-------------------------------------------------------------------------------
switch(ValueOf($DSOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($DSOrders as $DSOrder){
      #-------------------------------------------------------------------------
      $NoBody = new Tag('NOBODY');
      #-------------------------------------------------------------------------
      $DaysRemainded = $DSOrder['DaysRemainded'];
#-------------------------------------------------------------------------------
$Parse = <<<EOD
<NOBODY>
<SPAN>Обращаем Ваше внимание, что истекает срок аренды заказанного Вами сервера, IP адрес </SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
<SPAN> с тарифным планом </SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
<SPAN> и в случае не поступления оплаты в течение </SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s</SPAN>
<SPAN> дня(ей) он будет заблокирован. Для того, чтобы осуществить оплату сейчас, нажмите на кнопку </SPAN>
<A style="font-size:14px;font-weight:bold;" href="javascript:ShowWindow('/DSOrderPay',{DSOrderID:%u});">[оплатить]</A>
</NOBODY>
EOD;
#-------------------------------------------------------------------------------
      $NoBody->AddHTML(SPrintF($Parse,$DSOrder['IP'],$DSOrder['SchemeName'],$DaysRemainded?$DaysRemainded:'сегодняшнего',$DSOrder['ID']));
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
