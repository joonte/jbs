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
$InvoiceID = (integer) @$Args['InvoiceID'];
$IsRemote  = (boolean) @$Args['IsRemote'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Invoice = DB_Select('InvoicesOwners',Array('ID','UserID','PaymentSystemID','Document','IsPosted','StatusID','Summ'),Array('UNIQ','ID'=>$InvoiceID));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $PaymentSystemID = $Invoice['PaymentSystemID'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('InvoiceRead',(integer)$GLOBALS['__USER']['ID'],(integer)$Invoice['UserID']);
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
        $IsPayed = ($Invoice['IsPosted'] && $Invoice['StatusID'] != 'Conditionally');
	#-----------------------------------------------------------------------
	if($PaymentSystemID == 'QIWI' && strlen($GLOBALS['__USER']['Mobile']) < 10 && !$IsPayed){
	  DEBUG("[comp/www/InvoiceDocument]: Incorrect user phone");
	  $Comp3 = Comp_Load('Clauses/Load','/Help/Services/QIWIPhone',TRUE);
	  if(Is_Error($Comp3))
	  return ERROR | @Trigger_Error(500);
	  $DOM->AddText('Title',$Comp3['Title']);
	  $DOM->AddChild('Into',$Comp3['DOM']);
	}else{
          #-----------------------------------------------------------------------
          $Comp1 = Comp_Load('Buttons/Standard',Array('onclick'=>SPrintF("document.location = '/InvoiceDownload?InvoiceID=%u&IsStamp=yes';",$Invoice['ID'])),'Скачать счет в формате PDF','PDF.gif');
          if(Is_Error($Comp1))
            return ERROR | @Trigger_Error(500);
          #-----------------------------------------------------------------------
          $Comp2 = Comp_Load('Buttons/Standard',Array('onclick'=>SPrintF("document.location = '/InvoiceDownload?InvoiceID=%u&IsTIFF=yes&IsStamp=yes';",$Invoice['ID'])),'Скачать счет в формате TIFF','Image.gif');
          if(Is_Error($Comp2))
            return ERROR | @Trigger_Error(500);
          #-----------------------------------------------------------------------
          $Comp = Comp_Load('Buttons/Panel',Array('Comp'=>$Comp1,'Name'=>'Скачать счет в формате PDF'),Array('Comp'=>$Comp2,'Name'=>'Скачать счет в формате TIFF'));
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #-----------------------------------------------------------------------
          $DOM->AddChild('Into',$Comp);
          #-----------------------------------------------------------------------
          $Config = Config();
          #-----------------------------------------------------------------------
          $PaymentSystem = $Config['Invoices']['PaymentSystems'][$PaymentSystemID];
          #-----------------------------------------------------------------------
          if(!$IsPayed){
            #---------------------------------------------------------------------
            $A = new Tag('A',Array('style'=>'font-size:12px;','href'=>SPrintF("javascript:ShowWindow('/InvoiceEdit',{InvoiceID:%u});",$Invoice['ID'])),'[изменить]');
            #---------------------------------------------------------------------
            $DOM->AddChild('Into',new Tag('DIV',Array('class'=>'Title'),new Tag('CDATA',$PaymentSystem['Name']),$A));
          }
          #-----------------------------------------------------------------------
          $Document = new DOM($Invoice['Document']);
          #-----------------------------------------------------------------------
          $Document->Delete('Logo');
          #-----------------------------------------------------------------------
          $Document->Delete('Rubbish');
          #-----------------------------------------------------------------------
          $Document->DeleteIDs();
          #-----------------------------------------------------------------------
          $Div = new Tag('DIV',Array('class'=>'Standard','style'=>'max-width:700px;'),$Document->Object);
          #-----------------------------------------------------------------------
          if($IsPayed)
            $DOM->AddText('Title',' (Оплачен)');
          else{
            #---------------------------------------------------------------------
            if($PaymentSystem['IsContinuePaying']){
              #-------------------------------------------------------------------
              $Comp = Comp_Load(
                'Form/Input',
                Array(
                  'onclick' => "ShowProgress('Вход в платежную систему');form.submit();",
                  'type'    => 'button',
                  'style'   => 'font-size:25px;color:#F07D00;',
                  'value'   => 'Оплатить →'
                )
              );
              if(Is_Error($Comp))
                return ERROR | @Trigger_Error(500);
              #-------------------------------------------------------------------
              $Form = new Tag('FORM',Array('action'=>$PaymentSystem['Cpp'],'method'=>'POST'),new Tag('BR'),new Tag('DIV',$Comp));
              #-------------------------------------------------------------------
              $Send = Comp_Load(SPrintF('Invoices/PaymentSystems/%s',$PaymentSystem['Comp']),$PaymentSystemID,$Invoice['ID'],$Invoice['Summ']);
              if(Is_Error($Send))
                return ERROR | @Trigger_Error(500);
              #-------------------------------------------------------------------
              foreach(Array_Keys($Send) as $ParamID){
                #-----------------------------------------------------------------
                $Comp = Comp_Load('Form/Input',Array('name'=>$ParamID,'type'=>'hidden','value'=>$Send[$ParamID]));
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #-----------------------------------------------------------------
                $Form->AddChild($Comp);
              }
              #-------------------------------------------------------------------
              if($IsRemote){
                #-----------------------------------------------------------------
                $Out = $Document->Build();
                #-----------------------------------------------------------------
                if(Is_Error($Out))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------------
                return Array('Status'=>'Ok','Document'=>$Out,'Cpp'=>$PaymentSystem['Cpp'],'Send'=>$Send);
              }
              #-------------------------------------------------------------------
              $Div->AddChild($Form);
            }
          }
          #-----------------------------------------------------------------------
          $DOM->AddChild('Into',$Div);
          #-----------------------------------------------------------------------
          $Out = $DOM->Build(FALSE);
          #-----------------------------------------------------------------------
          if(Is_Error($Out))
            return ERROR | @Trigger_Error(500);
          #-----------------------------------------------------------------------
	}
        return Array('Status'=>'Ok','DOM'=>$DOM->Object);
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
