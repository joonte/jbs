<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$DSOrderID	= (integer) @$Args['DSOrderID'];
$OrderID        = (integer) @$Args['OrderID'];
$DaysPay        = (integer) @$Args['DaysPay'];
$IsChange       = (boolean) @$Args['IsChange'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/Tree.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','OrderID','ServiceID','IP','StatusID','UserID','SchemeID','DaysRemainded','(SELECT `TypeID` FROM `Contracts` WHERE `DSOrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractTypeID`','(SELECT `Balance` FROM `Contracts` WHERE `DSOrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractBalance`','(SELECT `GroupID` FROM `Users` WHERE `DSOrdersOwners`.`UserID` = `Users`.`ID`) as `GroupID`','(SELECT `IsPayed` FROM `Orders` WHERE `Orders`.`ID` = `DSOrdersOwners`.`OrderID`) as `IsPayed`','(SELECT SUM(`DaysReserved`*`Cost`*(1-`Discont`)) FROM `OrdersConsider` WHERE `OrderID`=`DSOrdersOwners`.`OrderID`) AS PayedSumm');
#-------------------------------------------------------------------------------
$Where = ($DSOrderID?SPrintF('`ID` = %u',$DSOrderID):SPrintF('`OrderID` = %u',$OrderID));
#-------------------------------------------------------------------------------
$DSOrder = DB_Select('DSOrdersOwners',$Columns,Array('UNIQ','Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($DSOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $UserID = (integer)$DSOrder['UserID'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('DSOrdersRead',(integer)$GLOBALS['__USER']['ID'],$UserID);
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
        $DOM = new DOM();
        #-----------------------------------------------------------------------
        $Links = &Links();
        # Коллекция ссылок
        $Links['DOM'] = &$DOM;
        #-----------------------------------------------------------------------
        if(Is_Error($DOM->Load('Window')))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form = new Tag('FORM',Array('name'=>'DSOrderPayForm','onsubmit'=>'return false;'));
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'name'  => 'DSOrderID',
            'value' => $DSOrder['ID'],
            'type'  => 'hidden'
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form->AddChild($Comp);
        #-----------------------------------------------------------------------
        $DOM->AddText('Title',SPrintF('Оплата заказа сервера, %s',$DSOrder['IP']));
        #-----------------------------------------------------------------------
        $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/OrderPay.js}')));
        #-----------------------------------------------------------------------
        if(!In_Array($DSOrder['StatusID'],Array('Waiting','Active','Suspended','OnService')))
          return new gException('ORDER_CAN_NOT_PAY','Аренда сервера не может быть оплачена');
        #-----------------------------------------------------------------------
        $__USER = $GLOBALS['__USER'];
        #-----------------------------------------------------------------------
        $DSScheme = DB_Select('DSSchemes',Array('ID','CostDay','CostInstall','MinDaysPay','MinDaysProlong','MaxDaysPay','IsActive','IsProlong'),Array('UNIQ','ID'=>$DSOrder['SchemeID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($DSScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            # проверяем, это первая оплата или нет? если не первая, то минимальное число дней MinDaysProlong
            Debug(SPrintF('[comp/www/DSOrderPay]: ранее оплачено за заказ %s',$DSOrder['PayedSumm']));
            if($DSOrder['PayedSumm'] > 0){
              $MinDaysPay = $DSScheme['MinDaysProlong'];
            }else{
              $MinDaysPay = $DSScheme['MinDaysPay'];
            }
	    #-------------------------------------------------------------------
	    Debug(SPrintF('[comp/www/DSOrderPay]: минимальное число дней %s',$MinDaysPay));
            #-------------------------------------------------------------------
            $Table = Array();
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Formats/Currency',$DSScheme['CostDay']);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Table[] = Array('Стоимость тарифа (в день)',$Comp);
            #-------------------------------------------------------------------
            if($DSOrder['IsPayed']){
              #-----------------------------------------------------------------
              if(!$DSScheme['IsProlong'])
                return new gException('SCHEME_NOT_ALLOW_PROLONG','Тарифный план аренды сервера не позволяет продление');
            }else{
              #-----------------------------------------------------------------
              if(!$DSScheme['IsActive'])
                return new gException('SCHEME_NOT_ACTIVE','Тарифный план аренды сервера не активен');
            }
            #-------------------------------------------------------------------
            if($DaysPay){
              #-----------------------------------------------------------------
              $Comp = Comp_Load(
                'Form/Input',
                Array(
                  'name'  => 'DaysPay',
                  'type'  => 'hidden',
                  'value' => $DaysPay
                )
              );
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Form->AddChild($Comp);
              #-----------------------------------------------------------------
              #-----------------------------------------------------------------
              if(Is_Error(DB_Transaction($TransactionID = UniqID('DSOrderPay'))))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Services/Politics',$DSOrder['UserID'],$DSOrder['GroupID'],$DSOrder['ServiceID'],$DSScheme['ID'],$DaysPay,SPrintF('Dedicated/%s',$DSOrder['IP']));
              if(Is_Error($Comp))
                 return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              #-----------------------------------------------------------------
	      $CostPay = 0.00;
	      #-----------------------------------------------------------------
              $DaysRemainded = $DaysPay;
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Services/Bonuses',$DaysRemainded,$DSOrder['ServiceID'],$DSScheme['ID'],$UserID,$CostPay,$DSScheme['CostDay'],FALSE);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $CostPay = $Comp['CostPay'];
              $Bonuses = $Comp['Bonuses'];
              #-----------------------------------------------------------------
              #-----------------------------------------------------------------
              if(Is_Error(DB_Roll($TransactionID)))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $CostPay = Round($CostPay,2);
              #-----------------------------------------------------------------
              $DaysRemainded = $DSOrder['DaysRemainded'];
              #-----------------------------------------------------------------
              if($DaysRemainded){
                #---------------------------------------------------------------
                $Comp = Comp_Load('/Formats/Date/Standard',Time() + $DaysRemainded*86400);
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Table[] = Array('Текущая дата окончания',$Comp);
              }
              #-----------------------------------------------------------------
              $Table[] = Array('Кол-во дней оплаты',SPrintF('%u дн.',$DaysPay));
              #-----------------------------------------------------------------
              $Comp = Comp_Load('/Formats/Date/Standard',Time() + ($DaysRemainded + $DaysPay)*86400);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Table[] = Array('Дата окончания после оплаты',$Comp);
	      #-----------------------------------------------------------------
	      if($DSScheme['CostInstall'] > 0){
	      	# need give installation payment
		if(!$DSOrder['IsPayed']){
		  # if it not prolongation
	      	  $Comp = Comp_Load('Formats/Currency',$DSScheme['CostInstall']);
		  if(Is_Error($Comp))
		    return ERROR | @Trigger_Error(500);
		  $Table[] = Array('Плата за установку',$Comp);
		  $CostPay += $DSScheme['CostInstall'];
		}
	      }
              #-----------------------------------------------------------------
              if(Count($Bonuses)){
                #---------------------------------------------------------------
                $Tr = new Tag('TR');
                #---------------------------------------------------------------
                foreach(Array('Дней','Скидка') as $Text)
                  $Tr->AddChild(new Tag('TD',Array('class'=>'Head'),$Text));
                #---------------------------------------------------------------
                Array_UnShift($Bonuses,$Tr);
                #---------------------------------------------------------------
                $Comp = Comp_Load('Tables/Extended',$Bonuses,'Бонусы');
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Table[] = new Tag('DIV',Array('align'=>'center'),$Comp);
              }
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Formats/Currency',$CostPay);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Table[] = Array('Всего к оплате',$Comp);
              #-----------------------------------------------------------------
              $Div = new Tag('DIV',Array('align'=>'right','class'=>'Standard'));
              #-------------------------------------------------------------------------------
              $Div->AddHTML(TemplateReplace('www.ServiceOrderPay',Array('ServiceCode'=>'DS')));
              #-----------------------------------------------------------------
              $Table[] = $Div;
              #-----------------------------------------------------------------
              $Table[] = new Tag('DIV',Array('align'=>'right','style'=>'font-size:10px;'),$CostPay > $DSOrder['ContractBalance']?'[заказ будет добавлен в корзину]':'[заказ будет оплачен с баланса договора]');
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
                  'onclick' => 'OrderPay("DS");',
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
              $Table = Array();
              #-----------------------------------------------------------------
              $DaysRemainded = $DSOrder['DaysRemainded'];
              #-----------------------------------------------------------------
              if($DaysRemainded){
                #---------------------------------------------------------------
                $Comp = Comp_Load('/Formats/Date/Standard',Time() + $DaysRemainded*86400);
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Table[] = Array('Текущая дата окончания',$Comp);
              }
              #-----------------------------------------------------------------
              $TimeRemainded = $DaysRemainded*86400;
              #-----------------------------------------------------------------
              $ExpirationDate = MkTime(0,0,0,Date('m'),Date('j'),Date('y')) + $TimeRemainded;
              #-----------------------------------------------------------------
              $sTime = MkTime(0,0,0,Date('m'),Date('j') + $MinDaysPay + $DaysRemainded,Date('Y'));
              $eTime = MkTime(0,0,0,Date('m'),Date('j') + $DSScheme['MaxDaysPay'] + $DaysRemainded,Date('Y'));
              #-----------------------------------------------------------------
              if($sTime >= $eTime){
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/DSOrderPay',Array('DSOrderID'=>$DSOrder['ID'],'DaysPay'=>$MinDaysPay));
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                return $Comp;
              }
              #-----------------------------------------------------------------
              $Script = Array('var Calendar = [];','var Periods = [];');
              #-----------------------------------------------------------------
              $Years = $Periods = Array();
              #-----------------------------------------------------------------
              for($Year=Date('Y',$sTime);$Year<=Date('Y',$eTime);$Year++){
                #---------------------------------------------------------------
                $Months = Array();
                #---------------------------------------------------------------
                $Script[] = SPrintF('Calendar[%u] = [];',$Year);
                #---------------------------------------------------------------
                for($Month=1;$Month<13;$Month++){
                  #-------------------------------------------------------------
                  $eDay = (integer)Date('t',MkTime(0,0,0,$Month,1,$Year));
                  #-------------------------------------------------------------
                  for($Day=1;$Day<=$eDay;$Day++){
                    #-----------------------------------------------------------
                    $CurrentStamp = MkTime(0,0,0,$Month,$Day,$Year);
                    #-----------------------------------------------------------
                    if($CurrentStamp >= $sTime && $CurrentStamp <= $eTime){
                      #---------------------------------------------------------
                      $Script[] = SPrintF('Calendar[%u][%u] = {Start:%u,Stop:%u}',$Year,$Month-1,$Day,MkTime(0,0,0,$Month,$eDay,$Year) > $eTime?Date('j',$eTime):$eDay);
                      #---------------------------------------------------------
                      break;
                    }
                  }
                  #-------------------------------------------------------------
                  $CurrentStamp = MkTime(0,0,0,$Month,Min(Date('j',$ExpirationDate),Date('t',MkTime(0,0,0,$Month,1,$Year))),$Year);
                  #-------------------------------------------------------------
                  if($CurrentStamp >= $sTime && $CurrentStamp <= $eTime){
                    #-----------------------------------------------------------
                    $Period = (Date('n',$CurrentStamp) + Date('Y',$CurrentStamp)*12) - (Date('n',$ExpirationDate) + Date('Y',$ExpirationDate)*12);
                    #-----------------------------------------------------------
                    if($Period < 4 || ($Period % 3 == 0 && $Period < 13) || $Period % 12 == 0){
                      #---------------------------------------------------------
                      $Script[] = SPrintF('Periods[%u] = {Year:%u,Month:%u,Day:%u}',$Period,$Year,$Month-1,Date('j',$ExpirationDate));
                      #---------------------------------------------------------
                      $Periods[$Period] = SPrintF('%u мес.',$Period);
                    }
                  }
                }
                #---------------------------------------------------------------
                $Years[] = $Year;
              }
              #-----------------------------------------------------------------
              if(!Count($Years))
                return new gException('PERIODS_NOT_DEFINED','Периоды оплаты не определены');
              #-----------------------------------------------------------------
              $IsPeriods = (boolean)Count($Periods);
              #-----------------------------------------------------------------
              if($IsPeriods){
                #---------------------------------------------------------------
                $Comp = Comp_Load('Form/Input',Array('onclick'=>'form.Period.disabled = false;form.Year.disabled = true;form.Month.disabled = true;form.Day.disabled = true;','name'=>'Calendar','type'=>'radio','checked'=>'true'));
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Table[] = new Tag('TD',Array('class'=>'Separator','colspan'=>2),$Comp,new Tag('SPAN','Выбор периода оплаты'));
                #---------------------------------------------------------------
                $Comp = Comp_Load('Form/Select',Array('name'=>'Period','onchange'=>'PeriodUpdate("DS");'),$Periods,12);
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Table[] = Array('Период оплаты',$Comp);
              }
              #-----------------------------------------------------------------
              $DOM->AddChild('Head',new Tag('SCRIPT',Implode("\n",$Script)));
              #-----------------------------------------------------------------
              $DOM->AddAttribs('Body',Array('onload'=>'PeriodInit("DS");'));
              #-----------------------------------------------------------------
              if($IsPeriods){
                #---------------------------------------------------------------
                $Comp = Comp_Load('Form/Input',Array('onclick'=>'form.Period.disabled = true;form.Year.disabled = false;form.Month.disabled = false;form.Day.disabled = false;','name'=>'Calendar','type'=>'radio'));
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
              }
              #-----------------------------------------------------------------
              $Table[] = new Tag('TD',Array('class'=>'Separator','colspan'=>2),$Comp,new Tag('SPAN','Выбор даты окончания'));
              #-----------------------------------------------------------------
              $Options = Array();
              #-----------------------------------------------------------------
              foreach($Years as $Year)
                $Options[$Year] = $Year;
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Form/Select',Array('name'=>'Year','onchange'=>'CalendarUpdateMonth("DS");'),$Options);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              if($IsPeriods)
                $Comp->AddAttribs(Array('disabled'=>'true'));
              #-----------------------------------------------------------------
              $Div = new Tag('DIV',$Comp);
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Form/Select',Array('name'=>'Month','onchange'=>'CalendarUpdateDay("DS");','value'=>'init'),Array('init'=>'-'));
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              if($IsPeriods)
                $Comp->AddAttribs(Array('disabled'=>'true'));
              #-----------------------------------------------------------------
              $Div->AddChild($Comp);
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Form/Select',Array('name'=>'Day','value'=>'init'),Array('init'=>'-'));
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              if($IsPeriods)
                $Comp->AddAttribs(Array('disabled'=>'true'));
              #-----------------------------------------------------------------
              $Div->AddChild($Comp);
              #-----------------------------------------------------------------
              $Table[] = Array('Дата окончания',$Div);
              #-----------------------------------------------------------------
	      #-----------------------------------------------------------------
              if($DSScheme['CostDay'] > 0){
                $DaysFromBallance = Floor($DSOrder['ContractBalance'] / $DSScheme['CostDay']);
		#-------------------------------------------------------------------------------
		$DaysFromBallance = Comp_Load('Bonuses/DaysCalculate',$DaysFromBallance,$DSScheme,$DSOrder,$UserID);
		if(Is_Error($DaysFromBallance))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
                if($MinDaysPay <= $DaysFromBallance){
                  if($IsPeriods){
                    #---------------------------------------------------------------
                    $Comp = Comp_Load('Form/Input',Array('onclick'=>'form.Period.disabled = true;form.Year.disabled = true;form.Month.disabled = true;form.Day.disabled = true;','name'=>'Calendar','type'=>'radio'));
                    if(Is_Error($Comp))
                      return ERROR | @Trigger_Error(500);
                  }
                  #---------------------------------------------------------------
                  $Table[] = new Tag('TD',Array('class'=>'Separator','colspan'=>2),$Comp,new Tag('SPAN','Остаток денег на балансе (' . $DSOrder['ContractBalance'] . ' руб.)'));
                  #---------------------------------------------------------------
                  $Table[] = Array('Остатка на счету хватит на ',$DaysFromBallance . ' дней');
                  #---------------------------------------------------------------
                  #---------------------------------------------------------------
                  $Comp = Comp_Load(
                                    'Form/Input',
                                    Array(
                                          'name'  => 'DaysPayFromBallance',
                                          'value' => $DaysFromBallance,
                                          'type'  => 'hidden'
                                         )
                                   );
                  if(Is_Error($Comp))
                    return ERROR | @Trigger_Error(500);
                  #-----------------------------------------------------------------
                  $Form->AddChild($Comp);
                  #-----------------------------------------------------------------
                }
              }
	      #-----------------------------------------------------------------
	      #-----------------------------------------------------------------
              $Comp = Comp_Load(
                'Form/Input',
                Array(
                  'name'  => 'DaysPay',
                  'value' => 31,
                  'type'  => 'hidden'
                )
              );
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Form->AddChild($Comp);
              #-----------------------------------------------------------------
              $Comp = Comp_Load(
                'Form/Input',
                Array(
                  'type'    => 'button',
		  'onclick' => SPrintF("if(typeof(form.Calendar[2]) != 'undefined' && form.Calendar[2].checked == true){form.DaysPay.value = form.DaysPayFromBallance.value;}else{form.DaysPay.value = Math.ceil((new Date(form.Year.value,form.Month.value,form.Day.value) - new Date(%u,%u,%u) - %u*1000)/86400000);};ShowWindow('/DSOrderPay',FormGet(form));",Date('Y'),Date('n')-1,Date('j'),$TimeRemainded),
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
