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
$VPSOrderID	= (integer) @$Args['VPSOrderID'];
$OrderID        = (integer) @$Args['OrderID'];
$DaysPay        = (integer) @$Args['DaysPay'];
$IsChange       = (boolean) @$Args['IsChange'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/Tree.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','StatusID','UserID','SchemeID','DaysRemainded','(SELECT `TypeID` FROM `Contracts` WHERE `VPSOrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractTypeID`','(SELECT `Balance` FROM `Contracts` WHERE `VPSOrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractBalance`','(SELECT `GroupID` FROM `Users` WHERE `VPSOrdersOwners`.`UserID` = `Users`.`ID`) as `GroupID`','(SELECT `IsPayed` FROM `Orders` WHERE `Orders`.`ID` = `VPSOrdersOwners`.`OrderID`) as `IsPayed`');
#-------------------------------------------------------------------------------
$Where = ($VPSOrderID?SPrintF('`ID` = %u',$VPSOrderID):SPrintF('`OrderID` = %u',$OrderID));
#-------------------------------------------------------------------------------
$VPSOrder = DB_Select('VPSOrdersOwners',$Columns,Array('UNIQ','Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $UserID = (integer)$VPSOrder['UserID'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('VPSOrdersRead',(integer)$GLOBALS['__USER']['ID'],$UserID);
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
        $Form = new Tag('FORM',Array('name'=>'VPSOrderPayForm','onsubmit'=>'return false;'));
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'name'  => 'VPSOrderID',
            'value' => $VPSOrder['ID'],
            'type'  => 'hidden'
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form->AddChild($Comp);
        #-----------------------------------------------------------------------
        $DOM->AddText('Title','Оплата заказа');
        #-----------------------------------------------------------------------
        $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/VPSOrderPay.js}')));
        #-----------------------------------------------------------------------
        if(!In_Array($VPSOrder['StatusID'],Array('Waiting','Active','Suspended')))
          return new gException('ORDER_CAN_NOT_PAY','Заказ виртуального сервера не может быть оплачен');
        #-----------------------------------------------------------------------
        $__USER = $GLOBALS['__USER'];
        #-----------------------------------------------------------------------
        $VPSScheme = DB_Select('VPSSchemes',Array('ID','CostDay','CostInstall','MinDaysPay','MaxDaysPay','IsActive','IsProlong'),Array('UNIQ','ID'=>$VPSOrder['SchemeID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($VPSScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            $Table = Array();
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Formats/Currency',$VPSScheme['CostDay']);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Table[] = Array('Стоимость тарифа (в день)',$Comp);
            #-------------------------------------------------------------------
            if($VPSOrder['IsPayed']){
              #-----------------------------------------------------------------
              if(!$VPSScheme['IsProlong'])
                return new gException('SCHEME_NOT_ALLOW_PROLONG','Тарифный план заказа виртуального сервера не позволяет продление');
            }else{
              #-----------------------------------------------------------------
              if(!$VPSScheme['IsActive'])
                return new gException('SCHEME_NOT_ACTIVE','Тарифный план заказа виртуального сервера не активен');
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
              $CostPay = 0.00;
              #-----------------------------------------------------------------
              if(Is_Error(DB_Transaction($TransactionID = UniqID('VPSOrderPay'))))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Entrance = Tree_Path('Groups',(integer)$VPSOrder['GroupID']);
              #-----------------------------------------------------------------
              switch(ValueOf($Entrance)){
                case 'error':
                  return ERROR | @Trigger_Error(500);
                case 'exception':
                  return ERROR | @Trigger_Error(400);
                case 'array':
                  #-------------------------------------------------------------
                  $Where = SPrintF('(`GroupID` IN (%s) OR `UserID` = %u) AND (`SchemeID` = %u OR ISNULL(`SchemeID`)) AND `DaysPay` <= %u',Implode(',',$Entrance),$VPSOrder['UserID'],$VPSScheme['ID'],$DaysPay);
                  #-------------------------------------------------------------
                  $VPSPolitic = DB_Select('VPSPolitics','*',Array('UNIQ','Where'=>$Where,'SortOn'=>'Discont','IsDesc'=>TRUE,'Limits'=>Array(0,1)));
                  #-------------------------------------------------------------
                  switch(ValueOf($VPSPolitic)){
                    case 'error':
                      return ERROR | @Trigger_Error(500);
                    case 'exception':
                      # No more...
                    break 2;
                    case 'array':
                      #---------------------------------------------------------
                      $IsInsert = DB_Insert('VPSBonuses',Array('UserID'=>$UserID,'SchemeID'=>$VPSScheme['ID'],'DaysReserved'=>$DaysPay,'Discont'=>$VPSPolitic['Discont']));
                      if(Is_Error($IsInsert))
                        return ERROR | @Trigger_Error(500);
                    break 2;
                    default:
                      return ERROR | @Trigger_Error(101);
                  }
                default:
                  return ERROR | @Trigger_Error(101);
              }
              #-----------------------------------------------------------------
              $VPSBonuses = Array();
              #-----------------------------------------------------------------
              $DaysRemainded = $DaysPay;
              #-----------------------------------------------------------------
              while($DaysRemainded){
                #---------------------------------------------------------------
                $Where = SPrintF('`UserID` = %u AND (`SchemeID` = %u OR ISNULL(`SchemeID`)) AND `DaysRemainded` > 0',$UserID,$VPSScheme['ID']);
                #---------------------------------------------------------------
                $VPSBonus = DB_Select('VPSBonuses','*',Array('IsDesc'=>TRUE,'SortOn'=>'Discont','Where'=>$Where));
                #---------------------------------------------------------------
                switch(ValueOf($VPSBonus)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    #-----------------------------------------------------------
                    $CostPay += $VPSScheme['CostDay']*$DaysRemainded;
                    #-----------------------------------------------------------
                    $DaysRemainded = 0;
                  break;
                  case 'array':
                    #-----------------------------------------------------------
                    $VPSBonus = Current($VPSBonus);
                    #-----------------------------------------------------------
                    $Discont = (1 - $VPSBonus['Discont']);
                    #-----------------------------------------------------------
                    if($VPSBonus['DaysRemainded'] - $DaysRemainded < 0){
                      #---------------------------------------------------------
                      $CostPay += $VPSScheme['CostDay']*$VPSBonus['DaysRemainded']*$Discont;
                      #---------------------------------------------------------
                      $UVPSBonus = Array('DaysRemainded'=>0);
                      #---------------------------------------------------------
                      $DaysRemainded -= $VPSBonus['DaysRemainded'];
                      #---------------------------------------------------------
                      $Comp = Comp_Load('Formats/Percent',$VPSBonus['Discont']);
                      if(Is_Error($Comp))
                        return ERROR | @Trigger_Error(500);
                      #---------------------------------------------------------
                      $Tr = new Tag('TR');
                      #---------------------------------------------------------
                      foreach(Array($VPSBonus['DaysRemainded'],$Comp) as $Text)
                        $Tr->AddChild(new Tag('TD',Array('class'=>'Standard','align'=>'right'),$Text));
                      #---------------------------------------------------------
                      $VPSBonuses[] = $Tr;
                    }else{
                      #---------------------------------------------------------
                      $CostPay += $VPSScheme['CostDay']*$DaysRemainded*$Discont;
                      #---------------------------------------------------------
                      $UVPSBonus = Array('DaysRemainded'=>$VPSBonus['DaysRemainded'] - $DaysRemainded);
                      #---------------------------------------------------------
                      $Comp = Comp_Load('Formats/Percent',$VPSBonus['Discont']);
                      if(Is_Error($Comp))
                        return ERROR | @Trigger_Error(500);
                      #---------------------------------------------------------
                      $Tr = new Tag('TR');
                      #---------------------------------------------------------
                      foreach(Array($DaysRemainded,$Comp) as $Text)
                        $Tr->AddChild(new Tag('TD',Array('class'=>'Standard','align'=>'right'),$Text));
                      #---------------------------------------------------------
                      $VPSBonuses[] = $Tr;
                      #---------------------------------------------------------
                      $DaysRemainded = 0;
                    }
                    #-----------------------------------------------------------
                    $IsUpdate = DB_Update('VPSBonuses',$UVPSBonus,Array('ID'=>$VPSBonus['ID']));
                    if(Is_Error($IsUpdate))
                      return ERROR | @Trigger_Error(500);
                  break;
                  default:
                    return ERROR | @Trigger_Error(101);
                }
              }
              #-----------------------------------------------------------------
              if(Is_Error(DB_Roll($TransactionID)))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $CostPay = Round($CostPay,2);
              #-----------------------------------------------------------------
              $DaysRemainded = $VPSOrder['DaysRemainded'];
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
	      #-----------------------------------------------------------------
              $Table[] = Array('Дата окончания после оплаты',$Comp);
	      if($VPSScheme['CostInstall'] > 0){
		# need give installation payment
		if(!$VPSOrder['IsPayed']){
		  # if it not prolongation
		  $Comp = Comp_Load('Formats/Currency',$VPSScheme['CostInstall']);
		  if(Is_Error($Comp))
		    return ERROR | @Trigger_Error(500);
		  $Table[] = Array('Плата за установку',$Comp);
		  $CostPay += $VPSScheme['CostInstall'];
		}
	      }
              #-----------------------------------------------------------------
              if(Count($VPSBonuses)){
                #---------------------------------------------------------------
                $Tr = new Tag('TR');
                #---------------------------------------------------------------
                foreach(Array('Дней','Скидка') as $Text)
                  $Tr->AddChild(new Tag('TD',Array('class'=>'Head'),$Text));
                #---------------------------------------------------------------
                Array_UnShift($VPSBonuses,$Tr);
                #---------------------------------------------------------------
                $Comp = Comp_Load('Tables/Extended',$VPSBonuses,'Бонусы');
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
$Parse = <<<EOD
<NOBODY>
 <SPAN>C </SPAN>
 <A href="/Clause?ClauseID=Contracts/Enclosures/Types/VPSRules/Content" target="blank">условиями</A>
 <SPAN> оказания услуг ознакомлен</SPAN>
</NOBODY>
EOD;
#-------------------------------------------------------------------------------
              $Div->AddHTML($Parse);
              #-----------------------------------------------------------------
              $Table[] = $Div;
              #-----------------------------------------------------------------
              $Table[] = new Tag('DIV',Array('align'=>'right','style'=>'font-size:10px;'),$CostPay > $VPSOrder['ContractBalance']?'[заказ будет добавлен в корзину]':'[заказ будет оплачен с баланса договора]');
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
                  'onclick' => 'VPSOrderPay();',
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
              $DaysRemainded = $VPSOrder['DaysRemainded'];
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
              $sTime = MkTime(0,0,0,Date('m'),Date('j') + $VPSScheme['MinDaysPay'] + $DaysRemainded,Date('Y'));
              $eTime = MkTime(0,0,0,Date('m'),Date('j') + $VPSScheme['MaxDaysPay'] + $DaysRemainded,Date('Y'));
              #-----------------------------------------------------------------
              if($sTime >= $eTime){
                #---------------------------------------------------------------
                $Comp = Comp_Load('www/VPSOrderPay',Array('VPSOrderID'=>$VPSOrder['ID'],'DaysPay'=>$VPSScheme['MinDaysPay']));
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                return $Comp;
              }
              #-----------------------------------------------------------------
              $Script = Array('var Calendar = [];','var Periods = [];');
              #-----------------------------------------------------------------
              $Years = $Periods = Array();;
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
                $Comp = Comp_Load('Form/Select',Array('name'=>'Period','onchange'=>'PeriodUpdate();'),$Periods,12);
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Table[] = Array('Период оплаты',$Comp);
              }
              #-----------------------------------------------------------------
              $DOM->AddChild('Head',new Tag('SCRIPT',Implode("\n",$Script)));
              #-----------------------------------------------------------------
              $DOM->AddAttribs('Body',Array('onload'=>'PeriodInit();'));
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
              $Comp = Comp_Load('Form/Select',Array('name'=>'Year','onchange'=>'CalendarUpdateMonth();'),$Options);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              if($IsPeriods)
                $Comp->AddAttribs(Array('disabled'=>'true'));
              #-----------------------------------------------------------------
              $Div = new Tag('DIV',$Comp);
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Form/Select',Array('name'=>'Month','onchange'=>'CalendarUpdateDay();','value'=>'init'),Array('init'=>'-'));
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
              if($VPSScheme['CostDay'] > 0){
                $DaysFromBallance = Floor($VPSOrder['ContractBalance'] / $VPSScheme['CostDay']);
                if($VPSScheme['MinDaysPay'] < $DaysFromBallance){
                  if($IsPeriods){
                    #---------------------------------------------------------------
                    $Comp = Comp_Load('Form/Input',Array('onclick'=>'form.Period.disabled = true;form.Year.disabled = true;form.Month.disabled = true;form.Day.disabled = true;','name'=>'Calendar','type'=>'radio'));
                    if(Is_Error($Comp))
                      return ERROR | @Trigger_Error(500);
                  }
                  #---------------------------------------------------------------
                  $Table[] = new Tag('TD',Array('class'=>'Separator','colspan'=>2),$Comp,new Tag('SPAN','Остаток денег на балансе (' . $VPSOrder['ContractBalance'] . ' руб.)'));
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
		  'onclick' => SPrintF("if(typeof(form.Calendar[2]) != 'undefined' && form.Calendar[2].checked == true){form.DaysPay.value = form.DaysPayFromBallance.value;}else{form.DaysPay.value = Math.ceil((new Date(form.Year.value,form.Month.value,form.Day.value) - new Date(%u,%u,%u) - %u*1000)/86400000);};ShowWindow('/VPSOrderPay',FormGet(form));",Date('Y'),Date('n')-1,Date('j'),$TimeRemainded),
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
