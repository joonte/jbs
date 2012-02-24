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
$ContractID      = (integer) @$Args['ContractID'];
$PaymentSystemID =  (string) @$Args['PaymentSystemID'];
$Summ            =  (double) @$Args['Summ'];
$StepID          = (integer) @$Args['StepID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Window')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Новый счет');
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'InvoiceMakeForm','onsubmit'=>'return false;'));
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
switch($StepID){
  case 0:
    #---------------------------------------------------------------------------
    $Contracts = DB_Select('Contracts',Array('ID','TypeID','Customer'),Array('Where'=>SPrintF('`UserID` = %u',$GLOBALS['__USER']['ID'])));
    #---------------------------------------------------------------------------
    switch(ValueOf($Contracts)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        #-----------------------------------------------------------------------
        $PaymentSystems = $Config['Invoices']['PaymentSystems'];
        #-----------------------------------------------------------------------
        $Options = Array();
        #-----------------------------------------------------------------------
        foreach(Array_Keys($PaymentSystems) as $PaymentSystemID){
          #---------------------------------------------------------------------
          $PaymentSystem = $PaymentSystems[$PaymentSystemID];
          #---------------------------------------------------------------------
          if(!$PaymentSystem['IsActive'])
            continue;
          #---------------------------------------------------------------------
          $Options[$PaymentSystemID] = $PaymentSystem['Name'];
        }
        #-----------------------------------------------------------------------
        if(!Count($Options))
          return new gException('PAYMENT_SYSTEMS_NOT_DEFINED','Платежные системы не определены');
        #-----------------------------------------------------------------
        if(SizeOf($Options) > 5){
          $WindowHeight = SizeOf($Options);
        }else{
          $WindowHeight = 5;
        }
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Form/Select',Array('name'=>'PaymentSystemID','size'=>$WindowHeight),$Options);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Платежная система',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'name'  => 'StepID',
            'type'  => 'hidden',
            'value' => 2
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form->AddChild($Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'onclick' => "ShowWindow('/InvoiceMake',FormGet(form));",
            'type'    => 'button',
            'value'   => 'Продолжить'
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = $Comp;
      break 2;
      case 'array':
        #-----------------------------------------------------------------------
        $Options = Array();
        #-----------------------------------------------------------------------
        foreach($Contracts as $Contract){
          #---------------------------------------------------------------------
          $Customer = $Contract['Customer'];
          #---------------------------------------------------------------------
          if(Mb_StrLen($Customer) > 20)
            $Customer = SPrintF('%s...',Mb_SubStr($Customer,0,20));
          #---------------------------------------------------------------------
          $Options[$Contract['ID']] = $Customer;
        }
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Form/Select',Array('name'=>'ContractID'),$Options,$ContractID);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $NoBody = new Tag('NOBODY',$Comp);
        #-----------------------------------------------------------------------
        $Window = JSON_Encode(Array('Url'=>'/InvoiceMake','Args'=>Array('StepID'=>1)));
        #-----------------------------------------------------------------------
        $A = new Tag('A',Array('href'=>SPrintF("javascript:ShowWindow('/ContractMake',{Window:'%s'});",Base64_Encode($Window))),'[новый]');
        #-----------------------------------------------------------------------
        $NoBody->AddChild($A);
        #-----------------------------------------------------------------------
        $Table[] = Array('Базовый договор',$NoBody);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'name'  => 'ContractID',
            'value' => $Contract['ID'],
            'type'  => 'hidden',
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form->AddChild($Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'name'  => 'StepID',
            'type'  => 'hidden',
            'value' => 1
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form->AddChild($Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'onclick' => "ShowWindow('/InvoiceMake',FormGet(form));",
            'type'    => 'button',
            'value'   => 'Продолжить'
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = $Comp;
      break 2;
      default:
        return ERROR | @Trigger_Error(101);
    }
  case 1:
    #---------------------------------------------------------------------------
    $Contract = DB_Select('Contracts',Array('ID','UserID','TypeID','Customer','Balance'),Array('UNIQ','ID'=>$ContractID));
    #---------------------------------------------------------------------------
    switch(ValueOf($Contract)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return new gException('CONTRACT_NOT_FOUND','Договор не найден');
      case 'array':
        #-----------------------------------------------------------------------
        $__USER = $GLOBALS['__USER'];
        #-----------------------------------------------------------------------
        $IsPermission = Permission_Check('ContractRead',(integer)$__USER['ID'],(integer)$Contract['UserID']);
        #-----------------------------------------------------------------------
        switch(ValueOf($IsPermission)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'false':
            return ERROR | @Trigger_Error(700);
          case 'true':
	    #-------------------------------------------------------------------
	    #-------------------------------------------------------------------
	    if($Contract['TypeID'] == "NaturalPartner"){
	    	return new gException('CANNOT_PAY_FOR_NaturalPartner','Данный тип договора нельзя пополнить напрямую');
	    }
	    #-------------------------------------------------------------------
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'name'  => 'ContractID',
                'type'  => 'hidden',
                'value' => $Contract['ID']
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Form->AddChild($Comp);
            #-------------------------------------------------------------------
            $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/InvoiceMake.js}')));
            #-------------------------------------------------------------------
            if($Contract['TypeID'] == 'Default'){
              #-----------------------------------------------------------------
              $PaymentSystems = $Config['Invoices']['PaymentSystems'];
	      #-----------------------------------------------------------------
	      #-----------------------------------------------------------------
	      $Script = "var PayDesc = {}; ";
	      #-----------------------------------------------------------------
              #-----------------------------------------------------------------
              $Options = Array();
              #-----------------------------------------------------------------
              foreach(Array_Keys($PaymentSystems) as $PaymentSystemID){
                #---------------------------------------------------------------
                $PaymentSystem = $PaymentSystems[$PaymentSystemID];
                #---------------------------------------------------------------
                if(!$PaymentSystem['IsActive'])
                  continue;
                #---------------------------------------------------------------
                $Options[$PaymentSystemID] = $PaymentSystem['Name'];
		#---------------------------------------------------------------
		$Script = $Script . "PayDesc['" . $PaymentSystemID . "'] = '" . $PaymentSystem['SystemDescription'] . "'; ";
              }
              #-----------------------------------------------------------------
              if(!Count($Options))
                return new gException('PAYMENT_SYSTEMS_NOT_DEFINED','Платежные системы не определены');
              #-----------------------------------------------------------------
	      if(SizeOf($Options) > 5){
	        $WindowHeight = SizeOf($Options);
	      }else{
	        $WindowHeight = 5;
	      }
	      #-----------------------------------------------------------------
	      #-----------------------------------------------------------------
	      $Script = $Script . ' form.PaymentsDescription.value = PayDesc[value]; ';
	      #-----------------------------------------------------------------
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Form/Select',Array('name'=>'PaymentSystemID','onchange'=>$Script,'prompt'=>'Список доступных платёжных систем','size'=>$WindowHeight),$Options);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Table[] = Array('Платежная система',$Comp);
	      #-----------------------------------------------------------------
	      #-----------------------------------------------------------------
	      $Comp = Comp_Load(
				'Form/TextArea',
				Array(
					'name'		=> 'PaymentsDescription',
					'style'		=> 'width:100%;',
					'class'		=> 'Duty',
					'prompt'	=> 'Описание выбранной платёжной системы',
					'readonly'	=> 'readonly',
					'rows'  	=> 4
				)
			);
	      if(Is_Error($Comp))
	        return ERROR | @Trigger_Error(500);
	      #---------------------------------------------------------------------------
	      $Table[] = $Comp;
	      #-----------------------------------------------------------------
              #-----------------------------------------------------------------
              $Comp = Comp_Load(
                'Form/Input',
                Array(
                  'onclick' => "ShowWindow('/InvoiceMake',FormGet(form));",
                  'type'    => 'button',
                  'value'   => 'Продолжить'
                )
              );
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Table[] = $Comp;
              #-----------------------------------------------------------------
              $Comp = Comp_Load(
                'Form/Input',
                Array(
                  'name'  => 'StepID',
                  'type'  => 'hidden',
                  'value' => 2
                )
              );
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Form->AddChild($Comp);
            }else{
              #-----------------------------------------------------------------
              $Table = Array();
              #-----------------------------------------------------------------
              $Customer = $Contract['Customer'];
              #-----------------------------------------------------------------
              if(Mb_StrLen($Customer) > 20)
                $Customer = SPrintF('%s...',Mb_SubStr($Customer,0,20));
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Formats/Currency',$Contract['Balance']);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Table[] = Array('Договор',SPrintF('%s - %s',$Customer,$Comp));
	      #-----------------------------------------------------------------
	      #-----------------------------------------------------------------
	      $Script = "var PayDesc = {}; ";
	      #-----------------------------------------------------------------
              #-----------------------------------------------------------------
              $PaymentSystems = $Config['Invoices']['PaymentSystems'];
              #-----------------------------------------------------------------
              $Options = Array();
              #-----------------------------------------------------------------
              foreach(Array_Keys($PaymentSystems) as $PaymentSystemID){
                #---------------------------------------------------------------
                $PaymentSystem = $PaymentSystems[$PaymentSystemID];
                #---------------------------------------------------------------
                if(!$PaymentSystem['IsActive'] || !$PaymentSystem['ContractsTypes'][$Contract['TypeID']])
                  continue;
                #---------------------------------------------------------------
                $Options[$PaymentSystemID] = $PaymentSystem['Name'];
		#---------------------------------------------------------------
		$Script = $Script . "PayDesc['" . $PaymentSystemID . "'] = '" . $PaymentSystem['SystemDescription'] . "'; ";
              }
              #-----------------------------------------------------------------
              if(!Count($Options))
                return new gException('PAYMENT_SYSTEMS_NOT_DEFINED','Платежные системы не определены');
	      #-----------------------------------------------------------------
	      if(SizeOf($Options) > 5){
		$WindowHeight = SizeOf($Options);
	      }else{
	        $WindowHeight = 5;
	      }
	      #-----------------------------------------------------------------
	      #-----------------------------------------------------------------
	      $Script = $Script . ' form.PaymentsDescription.value = PayDesc[value]; ';
	      #-----------------------------------------------------------------
              #-----------------------------------------------------------------
              $Comp = Comp_Load('Form/Select',Array('name'=>'PaymentSystemID','onchange'=>$Script,'prompt'=>'Список доступных платёжных систем','size'=>$WindowHeight),$Options);
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Table[] = Array('Платежная система',$Comp);
	      #-----------------------------------------------------------------
	      #-----------------------------------------------------------------
	      $Comp = Comp_Load(
				'Form/TextArea',
				Array(
					'name'		=> 'PaymentsDescription',
					'style'		=> 'width:100%;',
					'class'		=> 'Duty',
					'prompt'	=> 'Описание выбранной платёжной системы',
					'readonly'	=> 'readonly',
					'rows'		=> 4
				)
			);
	      if(Is_Error($Comp))
	        return ERROR | @Trigger_Error(500);
	      #---------------------------------------------------------------------------
	      $Table[] = $Comp;
	      #-----------------------------------------------------------------
              #-----------------------------------------------------------------
              $Count = DB_Count('BasketOwners',Array('Where'=>SPrintF('`ContractID` = %u',$Contract['ID'])));
              if(Is_Error($Count))
                  return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              if(!$Count){
                #---------------------------------------------------------------------
                $Table[] = new Tag('TD',Array('width'=>350,'colspan'=>2,'class'=>'Standard','style'=>'background-color:#FDF6D3;'),'Ваша корзина заказов пуста, однако, Вы можете пополнить баланс Вашего договора, чтобы в дальнейшем использовать эти денежные средства для оплаты услуг.');
                #---------------------------------------------------------------
                $Comp = Comp_Load('Form/Summ');
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Table[] = Array('Сумма для зачисления',$Comp);
              }
              #-----------------------------------------------------------------
              $Comp = Comp_Load(
                'Form/Input',
                Array(
                  'onclick' => 'InvoiceMake();',
                  'type'    => 'button',
                  'value'   => 'Продолжить'
                )
              );
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Table[] = $Comp;
            }
          break 3;
          default:
            return ERROR | @Trigger_Error(101);
        }
      default:
        return ERROR | @Trigger_Error(101);
    }
  case 2:
    #---------------------------------------------------------------------------
    if(!$PaymentSystemID)
      return new gException('PAYMENT_SYSTEM_NOT_SELECTED','Платежная система не указана');
    #---------------------------------------------------------------------------
    $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/InvoiceMake.js}')));
    #---------------------------------------------------------------------------
    $PaymentSystems = $Config['Invoices']['PaymentSystems'];
    #---------------------------------------------------------------------------
    if(!IsSet($PaymentSystems[$PaymentSystemID]))
      return new gException('PAYMENT_SYSTEM_NOT_FOUND','Платежная система не найдена');
    #---------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/Input',
      Array(
        'name'  => 'PaymentSystemID',
        'type'  => 'hidden',
        'value' => $PaymentSystemID
      )
    );
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Form->AddChild($Comp);
    #---------------------------------------------------------------------------
    $PaymentSystem = $PaymentSystems[$PaymentSystemID];
    #---------------------------------------------------------------------------
    $ContractsTypesIDs = Array();
    #---------------------------------------------------------------------------
    $ContractsTypes = $PaymentSystem['ContractsTypes'];
    #---------------------------------------------------------------------------
    foreach($ContractsTypes as $ContractTypeID=>$IsActive){
      #-------------------------------------------------------------------------
      if($IsActive)
        $ContractsTypesIDs[] = SPrintF("'%s'",$ContractTypeID);
    }
    #---------------------------------------------------------------------------
    $Contracts = DB_Select('Contracts',Array('ID','Customer'),Array('Where'=>SPrintF('`UserID` = %u AND `TypeID` IN (%s)',$GLOBALS['__USER']['ID'],Implode(',',$ContractsTypesIDs))));
    #---------------------------------------------------------------------------
    switch(ValueOf($Contracts)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        #-----------------------------------------------------------------------
        $Window = JSON_Encode(Array('Url'=>'/InvoiceMake','Args'=>Array('PaymentSystemID'=>$PaymentSystemID,'StepID'=>2)));
        #-----------------------------------------------------------------------
        $ContractsTypesIDs = Array();
        #-----------------------------------------------------------------------
        foreach($ContractsTypes as $ContractTypeID=>$IsActive){
          #---------------------------------------------------------------------
          if($IsActive)
            $ContractsTypesIDs[] = $ContractTypeID;
        }
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('www/ContractMake',Array('TypesIDs'=>Implode(',',$ContractsTypesIDs),'IsSimple'=>TRUE,'Window'=>Base64_Encode($Window)));
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        return $Comp;
      case 'array':
        #-----------------------------------------------------------------------
        $Table[] = Array('Платежная система',$PaymentSystem['Name']);
        #-----------------------------------------------------------------------
        $Options = Array();
        #-----------------------------------------------------------------------
        foreach($Contracts as $Contract){
          #---------------------------------------------------------------------
          $Customer = $Contract['Customer'];
          #---------------------------------------------------------------------
          if(Mb_StrLen($Customer) > 20)
            $Customer = SPrintF('%s...',Mb_SubStr($Customer,0,20));
          #---------------------------------------------------------------------
          $Options[$Contract['ID']] = $Customer;
        }
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Form/Select',Array('name'=>'ContractID'),$Options,$ContractID);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Базовый договор',$Comp);
        #-----------------------------------------------------------------------
        $Count = DB_Count('BasketOwners',Array('Where'=>SPrintF('`ContractID` = %u',$Contract['ID'])));
        if(Is_Error($Count))
            return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        if(!$Count){
          #---------------------------------------------------------------------
          $Table[] = new Tag('TD',Array('width'=>350,'colspan'=>2,'class'=>'Standard','style'=>'background-color:#FDF6D3;'),'Ваша корзина заказов пуста, однако, Вы можете пополнить баланс Вашего договора, чтобы в дальнейшем использовать эти денежные средства для оплаты услуг.');
          #---------------------------------------------------------------------
          $Comp = Comp_Load('Form/Summ');
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Table[] = Array('Сумма для зачисления',$Comp);
        }else{
          #---------------------------------------------------------------------
          if($ContractID)
            $DOM->AddAttribs('Body',Array('onload'=>'InvoiceMake();'));
        }
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'onclick' => 'InvoiceMake();',
            'type'    => 'button',
            'value'   => 'Продолжить'
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = $Comp;
      break 2;
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Form);
#-------------------------------------------------------------------------------
$Out = $DOM->Build(FALSE);
#-------------------------------------------------------------------------------
if(Is_Error($Out))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------

?>
