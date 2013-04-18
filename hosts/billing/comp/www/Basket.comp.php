<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$IsUpdate = (boolean) @$Args['IsUpdate'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($IsUpdate){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Basket/Update',$GLOBALS['__USER']['ID']);
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Base')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Корзина заказов');
#-------------------------------------------------------------------------------
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Basket.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Columns = Array('ID','ServiceID','ContractID','OrderID','Summ','Amount','Comment','(SELECT `Measure` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) as `Measure`','(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) as `ServiceCode`','(SELECT `Customer` FROM `Contracts` WHERE `Contracts`.`ID` = `ContractID`) as `Customer`');
#-------------------------------------------------------------------------------
$Basket = DB_Select('BasketOwners',$Columns,Array('Where'=>SPrintF('`UserID` = %u',$GLOBALS['__USER']['ID']),'SortOn'=>'ContractID'));
#-------------------------------------------------------------------------------
switch(ValueOf($Basket)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    #---------------------------------------------------------------------------
    $NoBody = new Tag('NOBODY');
    #---------------------------------------------------------------------------
    $NoBody->AddHTML(TemplateReplace('www.Basket'));
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Information',$NoBody,'Notice');
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $DOM->AddChild('Into',$Comp);
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $ContractID = UniqID();
    #---------------------------------------------------------------------------
    $Rows = Array();
    #---------------------------------------------------------------------------
    for($i=0;$i<Count($Basket);$i++){
      #-------------------------------------------------------------------------
      $Item = $Basket[$i];
      #-------------------------------------------------------------------------
      $Service = DB_Select('Services',Array('Name','Measure'),Array('UNIQ','ID'=>$Item['ServiceID']));
      #-------------------------------------------------------------------------
      switch(ValueOf($Service)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return ERROR | @Trigger_Error(400);
        case 'array':
          #---------------------------------------------------------------------
          if($ContractID != $Item['ContractID']){
            #-------------------------------------------------------------------
            $Rows[] = SPrintF('Договор: %s',$Item['Customer']);
            #-------------------------------------------------------------------
            $ContractID = $Item['ContractID'];
          }
          #---------------------------------------------------------------------
          $Comp = Comp_Load('Formats/Order/Number',$Item['OrderID']);
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Input = Comp_Load(
            'Form/Input',
            Array(
              'name'    => 'RowsIDs[]',
              'type'    => 'checkbox',
              'checked' => 'true',
              'value'   => $Item['ID']
            )
          );
          if(Is_Error($Input))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Summ = Comp_Load('Formats/Currency',$Item['Summ']);
          if(Is_Error($Summ))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Code = $Item['ServiceCode'];
	  # added by lissyara 2011-10-10 in 15:49, for JBS-176
	  if($Code == "Domains"){$Code = "Domain";}
          #---------------------------------------------------------------------
          $Href = ($Code != 'Default'?SPrintF('/%sOrderPay?OrderID=%u',$Code,$Item['OrderID']):SPrintF('/ServiceOrderPay?OrderID=%s',$Item['OrderID']));
          #---------------------------------------------------------------------
          $Td = new Tag('TD',Array('class'=>'Standard'),new Tag('CNAME',SPrintF('%s %s',$Item['Amount'],$Service['Measure'])),new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('%s');",$Href)),'[изменить]'));
          #---------------------------------------------------------------------
          $Rows[] = Array(new Tag('TD',$Input),new Tag('TD',Array('class'=>'Comment'),$Service['Name']),$Item['Comment'],SPrintF('№%s',$Comp),$Td,$Summ);
          #---------------------------------------------------------------------
          $Next = ($i<Count($Basket)-1?$Basket[$i+1]:FALSE);
          #---------------------------------------------------------------------
          if(!$Next || $Item['ContractID'] != $Next['ContractID']){
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'onclick' => SPrintF("ShowWindow('/InvoiceMake',{ContractID:%u,StepID:1});",$Item['ContractID']),
                'type'    => 'button',
                'value'   => 'Выписать счет'
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Rows[] = $Comp;
          }
        break;
        default:
          return ERROR | @Trigger_Error(101);
      }
    }
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Tables/Extended',$Rows);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Form = new Tag('Form',Array('name'=>'BasketForm'));
    #---------------------------------------------------------------------------
    $Form->AddChild($Comp);
    #---------------------------------------------------------------------------
    $Table = Array();
    #---------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/Input',
      Array(
        'type'    => 'button',
        'onclick' => 'BasketDelete();',
        'value'   => 'Удалить'
      )
    );
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Выбранные заказы',$Comp);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Tables/Standard',$Table);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Form->AddChild($Comp);
    #-------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/Input',
      Array(
        'name'  => 'TableID',
        'type'  => 'hidden',
        'value' => 'Basket'
      )
    );
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #-------------------------------------------------------------------------
    $Form->AddChild($Comp);
    #---------------------------------------------------------------------------
    $DOM->AddChild('Into',$Form);
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Out = $DOM->Build();
#-------------------------------------------------------------------------------
if(Is_Error($Out))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------

?>
