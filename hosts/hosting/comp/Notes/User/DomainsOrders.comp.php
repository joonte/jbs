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
$DomainsOrders = DB_Select('DomainsOrdersOwners',Array('ID','DomainName','(SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsOrdersOwners`.`SchemeID` = `DomainsSchemes`.`ID`) as `DomainZone`'),Array('Where'=>SPrintF("`UserID` = @local.__USER_ID AND `StatusID` IN ('Waiting','ClaimForRegister') AND `PersonID` = '' AND ISNULL(`ProfileID`)")));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainsOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($DomainsOrders as $DomainOrder){
      #-------------------------------------------------------------------------
      $NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$Parse = <<<EOD
<NOBODY>
<SPAN>Обращаем Ваше внимание, что Вами пока еще не определён владелец для заказа домена </SPAN>
<SPAN style="font-size:14px;font-weight:bold;">%s.%s</SPAN>
<SPAN> и в связи с этим мы не можем выполнить данный заказ. Однако, Вы можете произвести эту операцию позже, например, после оплаты счета. Для того, чтобы определить владельца сейчас, пожалуйста, нажмите на кнопку</SPAN>
<A style="font-size:14px;font-weight:bold;" href="javascript:ShowWindow('/DomainSelectOwner',{DomainOrderID:%u});">[определить]</A>
</NOBODY>
EOD;
#-------------------------------------------------------------------------------
      $NoBody->AddHTML(SPrintF($Parse,$DomainOrder['DomainName'],$DomainOrder['DomainZone'],$DomainOrder['ID']));
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
