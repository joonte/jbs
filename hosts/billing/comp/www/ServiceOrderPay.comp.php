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
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$ServiceOrderID = (integer) @$Args['ServiceOrderID'];
$OrderID        = (integer) @$Args['OrderID'];
$AmountPay      = (integer) @$Args['AmountPay'];
$IsChange       = (boolean) @$Args['IsChange'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','ServiceID','ExpirationDate','IsPayed','StatusID','(SELECT `Balance` FROM `Contracts` WHERE `Contracts`.`ID` = `ContractID`) as `ContractBalance`');
#-------------------------------------------------------------------------------
$ServiceOrder = DB_Select('OrdersOwners',$Columns,Array('UNIQ','ID'=>$ServiceOrderID?$ServiceOrderID:$OrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ServiceOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('ServicesOrdersPay',(integer)$__USER['ID'],(integer)$ServiceOrder['UserID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsPermission)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'false':
        return ERROR | @Trigger_Error(700);
      case 'true':
        #-----------------------------------------------------------------------
        $StatusID = $ServiceOrder['StatusID'];
        #-----------------------------------------------------------------------
        if(!In_Array($StatusID,Array('Waiting','Active','Suspended')))
          return new gException('SERVICE_ORDER_CAN_NOT_PAY','Заказ не может быть оплачен');
        #-----------------------------------------------------------------------
        $Service = DB_Select('Services',Array('Name','ConsiderTypeID','Measure','CostOn','Cost','IsActive','IsProlong'),Array('UNIQ','ID'=>$ServiceOrder['ServiceID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($Service)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            $IsPayed = $ServiceOrder['IsPayed'];
            #-------------------------------------------------------------------
            if($IsPayed){
              #-----------------------------------------------------------------
              if(!$Service['IsProlong'])
                return new gException('SERVICE_NOT_ALLOW_PROLONG','Услуга не позволяет продление');
            }else{
              #-----------------------------------------------------------------
              if(!$Service['IsActive'])
                return new gException('SERVICE_NOT_ACTIVE','Услуга не активна');
            }
            #-------------------------------------------------------------------
            $DOM = new DOM();
            #-------------------------------------------------------------------
            $Links = &Links();
            # Коллекция ссылок
            $Links['DOM'] = &$DOM;
            #-------------------------------------------------------------------
            if(Is_Error($DOM->Load('Window')))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $DOM->AddText('Title','Оплата заказа');
            #-------------------------------------------------------------------
            $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/ServiceOrderPay.js}')));
            #-------------------------------------------------------------------
            $Form = new Tag('FORM',Array('name'=>'ServiceOrderPayForm'));
            #-------------------------------------------------------------------
            $Table = Array();
            #-------------------------------------------------------------------
            $Table[] = Array('Название услуги',$Service['Name']);
            #-------------------------------------------------------------------
            if(!$IsPayed && $Service['CostOn']){
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Formats/Currency',$Service['CostOn']);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Table[] = Array('Стоимость подключения',$Comp);
            }
            #-------------------------------------------------------------------
            $Cost = $Service['Cost'];
            #-------------------------------------------------------------------
            $ServiceOrderFields = DB_Select('OrdersFields',Array('ID','ServiceFieldID','Value','FileName'),Array('Where'=>SPrintF('`OrderID` = %u',$ServiceOrderID)));
            #-------------------------------------------------------------------
            switch(ValueOf($ServiceOrderFields)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                # No more...
              break;
              case 'array':
                #---------------------------------------------------------------
                foreach($ServiceOrderFields as $ServiceOrderField){
                  #-------------------------------------------------------------
                  $Value = $ServiceOrderField['Value'];
                  #-------------------------------------------------------------
                  $ServiceField = DB_Select('ServicesFields',Array('Name','TypeID','Options'),Array('UNIQ','ID'=>$ServiceOrderField['ServiceFieldID']));
                  #-------------------------------------------------------------
                  switch(ValueOf($ServiceField)){
                    case 'error':
                      return ERROR | @Trigger_Error(500);
                    case 'exception':
                      return ERROR | @Trigger_Error(400);
                    case 'array':
                      #---------------------------------------------------------
                      if($ServiceField['TypeID'] != 'Select')
                        continue;
                      #---------------------------------------------------------
                      $Options = Explode("\n",$ServiceField['Options']);
                      #---------------------------------------------------------
                      if(Count($Options)){
                        #-------------------------------------------------------
                        foreach($Options as $Option){
                          #-----------------------------------------------------
                          $Option = Explode("=",$Option);
                          #-----------------------------------------------------
                          if(Current($Option) == $Value)
                            $Cost += (double)End($Option);
                        }
                      }
                    break;
                    default:
                      return ERROR | @Trigger_Error(101);
                  }
                }
              break;
              default:
                return ERROR | @Trigger_Error(101);
            }
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Formats/Currency',$Cost);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Table[] = Array(SPrintF('Стоимость (%s)',$Service['Measure']),$Comp);
            #-------------------------------------------------------------------
            $ExpirationDate = $ServiceOrder['ExpirationDate'];
            #-------------------------------------------------------------------
            if($ExpirationDate){
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Formats/Date/Standard',$ExpirationDate);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Table[] = Array('Текущая дата окончания',$Comp);
            }
            #-------------------------------------------------------------------
            if($AmountPay){
              #-----------------------------------------------------------------
              $Comp = Comp_Load(
                'Form/Input',
                Array(
                  'type'  => 'hidden',
                  'name'  => 'AmountPay',
                  'value' => $AmountPay
                )
              );
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Form->AddChild($Comp);
              #-----------------------------------------------------------------
              $Table[] = Array('Количество',SPrintF('%u %s',$AmountPay,$Service['Measure']));
              #-----------------------------------------------------------------
              if(!$ExpirationDate)
                $ExpirationDate = Time();
              #-----------------------------------------------------------------
              switch($Service['ConsiderTypeID']){
                case 'Upon':
                  $ExpirationDate = 0;
                break;
                case 'Daily':
                  $ExpirationDate = MkTime(0,0,0,Date('n',$ExpirationDate),Date('j',$ExpirationDate)+$AmountPay,Date('Y',$ExpirationDate));
                break;
                case 'Monthly':
                  $ExpirationDate = MkTime(0,0,0,Date('n',$ExpirationDate)+$AmountPay,Date('j',$ExpirationDate),Date('Y',$ExpirationDate));
                break;
                case 'Yearly':
                  $ExpirationDate = MkTime(0,0,0,Date('n',$ExpirationDate),Date('j',$ExpirationDate),Date('Y',$ExpirationDate)+$AmountPay);
                break;
                default:
                  return ERROR | @Trigger_Error(101);
              }
              #-----------------------------------------------------------------
              if($ExpirationDate){
                #---------------------------------------------------------------
                $Comp = Comp_Load('Formats/Date/Standard',$ExpirationDate);
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Table[] = Array('Дата окончания после оплаты',$Comp);
              }
              #-----------------------------------------------------------------
              $CostPay = $Cost*$AmountPay;
              #-----------------------------------------------------------------
              if(!$IsPayed && $Service['CostOn'])
                $CostPay += $Service['CostOn'];
              #-----------------------------------------------------------------
#              $Comp = Comp_Load('Services/Bonuses',$DaysRemainded,10000,$HostingScheme['ID'],$UserID,$CostPay,$HostingScheme['CostDay']);
#              if(Is_Error($Comp))
#                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
#              $CostPay = $Comp['CostPay'];
#              $Bonuses = $Comp['Bonuses'];
              #-----------------------------------------------------------------
              #-----------------------------------------------------------------
              $CostPay = Round($CostPay,2);
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Formats/Currency',$CostPay);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Table[] = Array('Всего к оплате',$Comp);
              #-----------------------------------------------------------------
              $Table[] = new Tag('DIV',Array('align'=>'right','style'=>'font-size:10px;'),$CostPay > $ServiceOrder['ContractBalance']?'[заказ будет добавлен в корзину]':'[заказ будет оплачен с баланса договора]');
              #-----------------------------------------------------------------
              $Div = new Tag('DIV',Array('align'=>'right'));
              #-----------------------------------------------------------------
              if($IsChange){
                #---------------------------------------------------------------
                $Comp = Comp_Load(
                  'Form/Input',
                  Array(
                    'type'    => 'button',
                    'onclick' => 'WindowPrev();',
                    'value'   => 'Изменить период'
                  )
                );
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Div->AddChild($Comp);
              }
              #-----------------------------------------------------------------
              $Comp = Comp_Load(
                'Form/Input',
                Array(
                  'type'    => 'button',
                  'onclick' => 'ServiceOrderPay();',
                  'value'   => 'Продолжить'
                )
              );
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Div->AddChild($Comp);
              #-----------------------------------------------------------------
              $Table[] = $Div;
            }else{
              #-----------------------------------------------------------------
              switch($Service['ConsiderTypeID']){
                case 'Upon':
                  #-------------------------------------------------------------
                  if($IsPayed)
                    return new gException('SERVICE_ORDER_PAYED','Заказ уже оплачен');
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('www/ServiceOrderPay',Array('ServiceOrderID'=>$ServiceOrderID,'OrderID'=>$OrderID,'AmountPay'=>1));
                  if(Is_Error($Comp))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  return $Comp;
                break;
                case 'Daily':
                  #-------------------------------------------------------------
                  $Options = Array();
                  #-------------------------------------------------------------
                  for($i=1;$i<=31;$i++)
                    $Options[$i] = SPrintF('%u %s',$i,$Service['Measure']);
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('Form/Select',Array('name'=>'AmountPay'),$Options);
                  if(Is_Error($Comp))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  $Table[] = Array('Период оплаты',$Comp);
                break;
                case 'Monthly':
                  #-------------------------------------------------------------
                  $Options = Array();
                  #-------------------------------------------------------------
                  for($i=1;$i<=12;$i++)
                    $Options[$i] = SPrintF('%u %s',$i,$Service['Measure']);
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('Form/Select',Array('name'=>'AmountPay'),$Options);
                  if(Is_Error($Comp))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  $Table[] = Array('Период оплаты',$Comp);
                  #-------------------------------------------------------------
                break;
                case 'Yearly':
                  #-------------------------------------------------------------
                  $Options = Array();
                  #-------------------------------------------------------------
                  for($i=1;$i<=5;$i++)
                    $Options[$i] = SPrintF('%u %s',$i,$Service['Measure']);
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('Form/Select',Array('name'=>'AmountPay'),$Options);
                  if(Is_Error($Comp))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  $Table[] = Array('Период оплаты',$Comp);
                  #-------------------------------------------------------------
                break;
                default:
                  return ERROR | @Trigger_Error(101);
              }
              #-----------------------------------------------------------------
              $Comp = Comp_Load(
                'Form/Input',
                Array(
                  'type'    => 'button',
                  'onclick' => "ShowWindow('/ServiceOrderPay',FormGet(form));",
                  'value'   => 'Продолжить'
                )
              );
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Table[] = $Comp;
            }
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Tables/Standard',$Table);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Form->AddChild($Comp);
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'type'  => 'hidden',
                'name'  => 'ServiceOrderID',
                'value' => $ServiceOrderID
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Form->AddChild($Comp);
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'type'  => 'hidden',
                'name'  => 'IsChange',
                'value' => 'true'
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Form->AddChild($Comp);
            #-------------------------------------------------------------------
            $DOM->AddChild('Into',$Form);
            #-------------------------------------------------------------------
            if(Is_Error($DOM->Build(FALSE)))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            return Array('Status'=>'Ok','DOM'=>$DOM->Object);
            #-------------------------------------------------------------------
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
