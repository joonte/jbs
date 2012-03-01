<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$ContractID = (integer) @$Args['ContractID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','CreateDate','TypeID','Customer','IsUponConsider','ProfileID','Balance','StatusID','StatusDate');
#-------------------------------------------------------------------------------
$Contract = DB_Select('Contracts',$Columns,Array('UNIQ','ID'=>$ContractID));
#-------------------------------------------------------------------------------
switch(ValueOf($Contract)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('ContractRead',(integer)$__USER['ID'],(integer)$Contract['UserID']);
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
        $DOM->AddText('Title','Договор');
        #-----------------------------------------------------------------------
        $Table = Array('Общая информация');
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Contract/Number',$Contract['ID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Номер',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Date/Standard',$Contract['CreateDate']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Дата создания',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Contract/Type',$Contract['TypeID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Тип',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Currency',$Contract['Balance']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Баланс',$Comp);
        #-----------------------------------------------------------------------
        $Table[] = 'Заказчик';
        #-----------------------------------------------------------------------
        $Table[] = Array('Имя',$Contract['Customer']);
        #-----------------------------------------------------------------------
        $Table[] = Array('Способ отчетности',$Contract['IsUponConsider']?'Ежемесячный':'По факту');
        #-----------------------------------------------------------------------
        $ProfileID = $Contract['ProfileID'];
        #-----------------------------------------------------------------------
        if($ProfileID){
          #---------------------------------------------------------------------
          $Comp = Comp_Load(
            'Form/Input',
            Array(
              'type'    => 'button',
              'onclick' => SPrintF("ShowWindow('/ProfileInfo',{ProfileID:%u});",$ProfileID),
              'value'   => 'Просмотреть'
            )
          );
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $NoBody = new Tag('NOBODY',$Comp,new Tag('SPAN','|'));
          #---------------------------------------------------------------------
          $Comp = Comp_Load(
            'Form/Input',
            Array(
              'type'    => 'button',
              'onclick' => SPrintF("ShowWindow('/ProfileEdit',{ProfileID:%u});",$ProfileID),
              'value'   => 'Редактировать'
            )
          );
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $NoBody->AddChild($Comp);
          #---------------------------------------------------------------------
          $Table[] = Array('Данные профиля',$NoBody);
        }
        #-----------------------------------------------------------------------
        $Table[] = 'Взаиморасчеты';
        #-----------------------------------------------------------------------
        $Summ = DB_Select('Invoices','SUM(`Summ`) as `Summ`',Array('UNIQ','Where'=>SPrintF('`ContractID` = %u',$Contract['ID'])));
        #-----------------------------------------------------------------------
        switch(ValueOf($Summ)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Formats/Currency',$Summ['Summ']);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Table[] = Array('Сумма оплаченных счетов',$Comp);
            #-------------------------------------------------------------------
            $Summ = DB_Select('WorksComplite','SUM((`Amount`*`Cost`)*(1 - `Discont`)) as `Summ`',Array('UNIQ','Where'=>SPrintF('`ContractID` = %u',$Contract['ID'])));
            #-------------------------------------------------------------------
            switch(ValueOf($Summ)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return ERROR | @Trigger_Error(400);
              case 'array':
                #---------------------------------------------------------------
                $Comp = Comp_Load('Formats/Currency',$Summ['Summ']);
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Table[] = Array('Выполнено работ',$Comp);
                #---------------------------------------------------------------
                $Comp = Comp_Load('Statuses/State','Contracts',$Contract);
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Table = Array_Merge($Table,$Comp);
                #---------------------------------------------------------------
                $Comp = Comp_Load('Tables/Standard',$Table);
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $DOM->AddChild('Into',$Comp);
                #---------------------------------------------------------------
                if(Is_Error($DOM->Build(FALSE)))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
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
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
