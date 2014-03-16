<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','ExtraIPOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/ExtraIPServer.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ExtraIPOrder = DB_Select('ExtraIPOrdersOwners',Array('ID','UserID','OrderType','DependOrderID','SchemeID','(SELECT `AddressType` FROM `ExtraIPSchemes` WHERE `ExtraIPSchemes`.`ID` = `ExtraIPOrdersOwners`.`SchemeID`) AS `AddressType`'),Array('UNIQ','ID'=>$ExtraIPOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    # проверяем, активен ли заказ к которому надо прицепить заказ адреса
    # вот тока - а надо ли? может заказ ручной какой-то.... думать надо...
    # думаю, определять надо позже, в случае если используется АСУ
    #
    # надо определить систему управления и тип панели на сервере, для этого заказа
    #---------------------------------------------------------------------------
    $ExtraIPServer = new ExtraIPServer();
    #---------------------------------------------------------------------------
    $IsSelected = $ExtraIPServer->FindSystem((integer)$ExtraIPOrderID,(string)$ExtraIPOrder['OrderType'],(integer)$ExtraIPOrder['DependOrderID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsSelected)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'true':
	# находим данные для этого заказа Хостинга/ВПС
	$DependOrder = DB_Select($ExtraIPOrder['OrderType'] . 'OrdersOwners',Array('ID','UserID','Login','Password','Domain','SchemeID'),Array('UNIQ','ID'=>$ExtraIPOrder['DependOrderID']));
	switch(ValueOf($DependOrder)){
	case 'error':
	  return ERROR | @Trigger_Error(500);
	case 'exception':
	  return ERROR | @Trigger_Error(400);
	case 'array':
          #-----------------------------------------------------------------------
          $DependScheme = DB_Select($ExtraIPOrder['OrderType'] . 'Schemes','*',Array('UNIQ','ID'=>$DependOrder['SchemeID']));
          #-----------------------------------------------------------------------
          switch(ValueOf($DependScheme)){
            case 'error':
              return ERROR | @Trigger_Error(500);
            case 'exception':
              return ERROR | @Trigger_Error(400);
            case 'array':
              #-------------------------------------------------------------------
              $IPsPool = Explode("\n",(($ExtraIPOrder['OrderType'] != 'VPS')?($ExtraIPServer->Settings['IPsPool']):($ExtraIPServer->Settings['Params']['IPsPool'])));
              #-------------------------------------------------------------------
              $IP = $IPsPool[Rand(0,Count($IPsPool) - 1)];
              #-------------------------------------------------------------------
              $Args = Array($DependOrder['Login'],$ExtraIPOrder['ID'],$DependOrder['Domain'],$IP,$ExtraIPOrder['AddressType']);
              #-------------------------------------------------------------------
              $User = DB_Select('Users','*',Array('UNIQ','ID'=>$ExtraIPOrder['UserID']));
              #-------------------------------------------------------------------
              switch(ValueOf($User)) {
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  break;
                  case 'array':
                    #-------------------------------------------------------------
                    #$Args[] = $User['Email'];
                  break;
                  default:
                    return ERROR | @Trigger_Error(101);
              }
              #-------------------------------------------------------------------
              #-------------------------------------------------------------------
              $IsCreate = Call_User_Func_Array(Array($ExtraIPServer,'AddIP'),$Args);
              #-------------------------------------------------------------------
              switch(ValueOf($IsCreate)){
                case 'error':
                  return ERROR | @Trigger_Error(500);
                case 'exception':
                  return $IsCreate;
                case 'true':
		  #---------------------------------------------------------------
		  # достаём собсно адрес из БД
                  $ExtraIP = DB_Select('ExtraIPOrdersOwners',Array('Login'),Array('UNIQ','ID'=>$ExtraIPOrderID));
                  switch(ValueOf($ExtraIP)){
		  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
		    break;
		  default:
		    return ERROR | @Trigger_Error(101);
		  }
                  #---------------------------------------------------------------
                  $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ExtraIPOrders','StatusID'=>'Active','RowsIDs'=>$ExtraIPOrder['ID'],'Comment'=>'Дополнительный IP успешно добавлен'));
                  #---------------------------------------------------------------
                  switch(ValueOf($Comp)){
                    case 'error':
                      return ERROR | @Trigger_Error(500);
                    case 'exception':
                      return ERROR | @Trigger_Error(400);
                    case 'array':
                      #-----------------------------------------------------------
		      $Event = Array(
		      			'UserID'	=> $ExtraIPOrder['UserID'],
					'PriorityID'	=> 'Billing',
					'Text'		=> SPrintF('На сервере (%s) для логина (%s) успешно добавлен дополнительный IP (%s) c обратной зоной (%s)',$ExtraIPServer->Settings['Address'],$DependOrder['Login'],$ExtraIP['Login'],$DependOrder['Domain'])
		                    );
                      $Event = Comp_Load('Events/EventInsert',$Event);
                      if(!$Event)
                        return ERROR | @Trigger_Error(500);
                      #-----------------------------------------------------------
                      $GLOBALS['TaskReturnInfo'] = Array($ExtraIPServer->Settings['Address'],$DependOrder['Login'],$ExtraIP['Login']);
		      #-----------------------------------------------------------
                      return TRUE;
                    default:
                      return ERROR | @Trigger_Error(101);
                  }
                default:
                  return ERROR | @Trigger_Error(101);
              }
            default:
              return ERROR | @Trigger_Error(101);
          }
        default:
          return ERROR | @Trigger_Error(101);
      }
    default:
      return ERROR | @Trigger_Error(101);
  }
default:
  return ERROR | @Trigger_Error(101);
}

#-------------------------------------------------------------------------------

?>
