<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(IsSet($Args)){
	#Debug("[comp/www/Administrator/API/DomainOrderWhoIsUpdate]: internal request");
	$IsInternal = TRUE;
}else{
	#Debug("[comp/www/Administrator/API/DomainOrderWhoIsUpdate]: external request");
	$Args = Args();
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DomainOrderID = (integer) @$Args['DomainOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/WhoIs.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','DomainName','(SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `SchemeID`) as `SchemeName`');
#-------------------------------------------------------------------------------
$DomainOrder = DB_Select('DomainsOrders',$Columns,Array('UNIQ','ID'=>$DomainOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('DOMAIN_ORDER_NOT_FOUND','Заказ домена не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $WhoIs = WhoIs_Check($DomainOrder['DomainName'],$DomainOrder['SchemeName']);
    #---------------------------------------------------------------------------
    switch(ValueOf($WhoIs)){
      case 'error':
        return new gException('WHOIS_SERVER_ERROR','Ошибка сервера WhoIs');
      case 'exception':
        return new gException('CAN_NOT_GET_WHOIS_DATA','Не удалось получить данные WhoIs',$WhoIs);
      case 'false':
        return new gException('DOMAIN_ZONE_NOT_SUPPORTED','Доменная зона не поддерживается');
      case 'array':
        #-----------------------------------------------------------------------
        $UDomainOrder = Array('UpdateDate'=>Time(),'WhoIs'=>$WhoIs['Info']);
        #-----------------------------------------------------------------------
        $ExpirationDate = $WhoIs['ExpirationDate'];
        #-----------------------------------------------------------------------
        if($ExpirationDate)
          $UDomainOrder['ExpirationDate'] = $ExpirationDate;
        #-----------------------------------------------------------------------
        for($i=1;$i<5;$i++){
          #---------------------------------------------------------------------
          $NsName = SPrintF('Ns%uName',$i);
          #---------------------------------------------------------------------
          if(IsSet($WhoIs[$NsName]))
            $UDomainOrder[$NsName] = $WhoIs[$NsName];
          #---------------------------------------------------------------------
          $NsIP = SPrintF('Ns%uIP',$i);
          #---------------------------------------------------------------------
          if(IsSet($WhoIs[$NsIP]))
            $UDomainOrder[$NsIP] = $WhoIs[$NsIP];
        }
        #-----------------------------------------------------------------------
        $IsUpdate = DB_Update('DomainsOrders',$UDomainOrder,Array('ID'=>$DomainOrder['ID']));
        if(Is_Error($IsUpdate))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        return Array('Status'=>'Ok');
      case 'true':
        if(IsSet($IsInternal)){
	  #-----------------------------------------------------------------------
	  # add admin message
          $Event = Array(
			'UserID'        => 1,
			'PriorityID'    => 'Error',
			'Text'          => SPrintF('Домен %s.%s является свободным, невозможно обновить информацию WhoIs',$DomainOrder['DomainName'],$DomainOrder['SchemeName']),
			'IsReaded'      => FALSE
			);
	  $Event = Comp_Load('Events/EventInsert',$Event);
          if(!$Event)
             return ERROR | @Trigger_Error(500);
	  #-----------------------------------------------------------------------
	  # update last whois update date
	  $IsUpdate = DB_Update('DomainsOrders',Array('UpdateDate'=>Time()),Array('ID'=>$DomainOrder['ID']));
          if(Is_Error($IsUpdate))
            return ERROR | @Trigger_Error(500);
	  #-----------------------------------------------------------------------
	  return TRUE;
	}else{
          return new gException('DOMAIN_IS_FREE',SPrintF('Доменное имя %s.%s является свободным',$DomainOrder['DomainName'],$DomainOrder['SchemeName']));
	}
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
