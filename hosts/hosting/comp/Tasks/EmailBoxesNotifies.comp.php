<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/Server.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$NotifyedCount = 0;
#-------------------------------------------------------------------------------
$HostingServers = DB_Select('HostingServers',Array('ID','Address'));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingServers)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($HostingServers as $HostingServer){
      #-------------------------------------------------------------------------
      $Server = new Server();
      #-------------------------------------------------------------------------
      $IsSelected = $Server->Select((integer)$HostingServer['ID']);
      #-------------------------------------------------------------------------
      switch(ValueOf($IsSelected)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return ERROR | @Trigger_Error(400);
        case 'true':
          #---------------------------------------------------------------------
          $Users = $Server->GetEmailBoxes();
          #---------------------------------------------------------------------
          switch(ValueOf($Users)){
            case 'error':
              # No more...
            case 'exception':
              # No more...
            break 2;
            case 'array':
              #-----------------------------------------------------------------
              if(Count($Users)){
                #---------------------------------------------------------------
                $Array = Array();
                #---------------------------------------------------------------
                foreach(Array_Keys($Users) as $UserID)
                  $Array[] = SPrintF("'%s'",$UserID);
                #---------------------------------------------------------------
                $Where = SPrintF('`ServerID` = %u AND `Login` IN (%s)',$HostingServer['ID'],Implode(',',$Array));
                #---------------------------------------------------------------
                $HostingOrders = DB_Select('HostingOrdersOwners',Array('ID','UserID','Login'),Array('Where'=>$Where));
                #---------------------------------------------------------------
                switch(ValueOf($HostingOrders)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    # No more...
                  break;
                  case 'array':
                    #-----------------------------------------------------------
                    $Heads = Array(SPrintF('From: admin@%s',$HostingServer['Address']),'MIME-Version: 1.0','Content-Type: text/plain; charset=UTF-8','Content-Transfer-Encoding: 8bit');
                    #-----------------------------------------------------------
                    foreach($HostingOrders as $HostingOrder){
                      #---------------------------------------------------------
                      $Boxes = $Users[$HostingOrder['Login']];
                      #---------------------------------------------------------
                      foreach($Boxes as $Email=>$Box){
                        #-------------------------------------------------------
                        $Total = Next($Box);
                        if(!$Total)
                          continue;
                        #-------------------------------------------------------
                        $Used = Prev($Box);
                        #-------------------------------------------------------
                        $Usage = ($Used/$Total)*100;
                        #-------------------------------------------------------
                        if($Usage > 80){
			  #-------------------------------------------------------
			  $NotifyedCount++;
#-------------------------------------------------------------------------------
$Message = <<<EOD
Вас беспокоет почтовая система.
Уведомляем Вас, о том, что оканчивается квота для Вашего почтового ящика %s.
На данный момент Ваш почтовый ящик заполнен на %u%%.
Пожалуйста, примите меры, иначе Ваш почтовый ящик будет заполнен и Вы не сможете принимать сообщения.
EOD;
#-------------------------------------------------------------------------------
                          $IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$HostingOrder['UserID'],'TypeID'=>'Email','Params'=>Array($Email,'Квота почтового ящика',SPrintF($Message,$Email,$Usage),Implode("\n",$Heads))));
                          #-----------------------------------------------------
                          switch(ValueOf($IsAdd)){
                            case 'error':
                              return ERROR | @Trigger_Error(500);
                            case 'exception':
                              return ERROR | @Trigger_Error(400);
                            case 'array':
                              # No more...
                            break;
                            default:
                              return ERROR | @Trigger_Error(101);
                          }
                        }
                      }
                    }
                  break;
                  default:
                    return ERROR | @Trigger_Error(101);
                }
              }
            break 2;
            default:
              return ERROR | @Trigger_Error(101);
          }
        default:
          return ERROR | @Trigger_Error(101);
      }
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($NotifyedCount > 0)
	$GLOBALS['TaskReturnInfo'] = SPrintF('Notified %u email accounts',$NotifyedCount);
#-------------------------------------------------------------------------------
return MkTime(6,45,0,Date('n'),Date('j')+1,Date('Y'));
#-------------------------------------------------------------------------------

?>
