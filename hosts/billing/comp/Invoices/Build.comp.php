<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('InvoiceID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/DOM.class','libs/Wizard.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Invoice = DB_Select('Invoices',Array('ID','CreateDate','ContractID','PaymentSystemID','Summ'),Array('UNIQ','ID'=>$InvoiceID));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $InvoiceID = $Invoice['ID'];
    #---------------------------------------------------------------------------
    $Contract = DB_Select('Contracts',Array('ID','CreateDate','ProfileID'),Array('UNIQ','ID'=>$Invoice['ContractID']));
    #---------------------------------------------------------------------------
    switch(ValueOf($Contract)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        #-----------------------------------------------------------------------
        $ContractID = (integer)$Contract['ID'];
        #-----------------------------------------------------------------------
        $IsQuery = DB_Query(SPrintF('UPDATE `Invoices` SET `CreateDate` = IF(`CreateDate` < %u,%u,`CreateDate`) WHERE `ContractID` = %u',$Contract['CreateDate'],$Contract['CreateDate'],$ContractID));
        if(Is_Error($IsQuery))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Contract/Number',$ContractID);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Contract['Number'] = $Comp;
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Date/Standard',$Contract['CreateDate']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Contract['CreateDate'] = $Comp;
        #-----------------------------------------------------------------------
        $Config = Config();
        #-----------------------------------------------------------------------
        $PaymentSystem = $Config['Invoices']['PaymentSystems'][$Invoice['PaymentSystemID']];
        #-----------------------------------------------------------------------
        $Replace = Array('Contract'=>$Contract,'PaymentSystem'=>$PaymentSystem);
        #-----------------------------------------------------------------------
        $ProfileID = (integer)$Contract['ProfileID'];
        #-----------------------------------------------------------------------
        if($ProfileID){
          #---------------------------------------------------------------------
          $Profile = Comp_Load('www/Administrator/API/ProfileCompile',Array('ProfileID'=>$ProfileID));
          #---------------------------------------------------------------------
          switch(ValueOf($Profile)){
            case 'error':
              return ERROR | @Trigger_Error(500);
            case 'exception':
              return ERROR | @Trigger_Error(400);
            case 'array':
              $Replace['Customer'] = $Profile['Attribs'];
            break;
            default:
              return ERROR | @Trigger_Error(101);
          }
        }
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Invoice/Number',$InvoiceID);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Invoice['Number'] = $Comp;
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Date/Standard',$Invoice['CreateDate']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Invoice['CreateDate'] = $Comp;
        #-----------------------------------------------------------------------
        $Summ = $Invoice['Summ'];
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Currency',$Summ);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Invoice['Summ'] = $Comp;
        #-----------------------------------------------------------------------
        $Invoice['Foreign'] = SPrintF('%01.2f',$Summ/$PaymentSystem['Course']);
        #-----------------------------------------------------------------------
        $Wizard = Wizard_ToString((double)$Invoice['Summ']);
        if(Is_Error($Wizard))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Nds = Comp_Load('Formats/Currency',($Summ*18)/118);
        if(Is_Error($Nds))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Wizard = SPrintF('%s. %s',$Wizard,$Config['Executor']['IsNds']?SPrintF('(в том числе НДС %s)',$Nds):'(НДС не облагается)');
        #-----------------------------------------------------------------------
        $Invoice['Wizard'] = $Wizard;
        #-----------------------------------------------------------------------
        $Executor = Comp_Load('www/Administrator/API/ProfileCompile',Array('ProfileID'=>100));
        #-----------------------------------------------------------------------
        switch(ValueOf($Executor)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'array':
            #-------------------------------------------------------------------
            $Replace['Executor'] = $Executor['Attribs'];
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Clauses/Load',SPrintF('Invoices/PaymentSystems/%s/%s',$Invoice['PaymentSystemID'],$Executor['TemplateID']));
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            if($Comp['IsExists']){
              #-----------------------------------------------------------------
              $DOM = new DOM($Comp['DOM']);
              #-----------------------------------------------------------------
              break;
            }
          case 'exception':
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Clauses/Load',SPrintF('Invoices/PaymentSystems/%s',$Invoice['PaymentSystemID']));
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $DOM = new DOM($Comp['DOM']);
          break;
          default:
            return ERROR | @Trigger_Error(101);
        }
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Clauses/Load','Invoices/Services');
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $DOM->AddChild('Services',$Comp['DOM']);
        #-----------------------------------------------------------------------
        $Replace['Invoice'] = $Invoice;
        #-----------------------------------------------------------------------
        $InvoiceItems = DB_Select('InvoicesItems','*',Array('Where'=>SPrintF('`InvoiceID` = %u',$InvoiceID)));
        #-----------------------------------------------------------------------
        switch(ValueOf($InvoiceItems)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            if(IsSet($DOM->Links['Item'])){
              #-----------------------------------------------------------------
              $Childs = $DOM->Links['Item']->Childs;
              #-----------------------------------------------------------------
              foreach($InvoiceItems as $Item){
                #---------------------------------------------------------------
                $Comp = Comp_Load('Formats/Currency',$Item['Summ']);
                if(Is_Error($Summ))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Item['Summ'] = $Comp;
                #---------------------------------------------------------------
                $Service = DB_Select('Services','*',Array('UNIQ','ID'=>$Item['ServiceID']));
                #---------------------------------------------------------------
                switch(ValueOf($Service)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return ERROR | @Trigger_Error(400);
                  case 'array':
                    #-----------------------------------------------------------
                    $Item['Service'] = $Service;
                    #-----------------------------------------------------------
                    $OrderID = (integer)$Item['OrderID'];
                    #-----------------------------------------------------------
                    if($OrderID){
                      #---------------------------------------------------------
                      $OrderID = Comp_Load('Formats/Order/Number',$Item['OrderID']);
                      if(Is_Error($OrderID))
                        return ERROR | @Trigger_Error(500);
                    }
                    #-----------------------------------------------------------
                    $Item['Order'] = Array('Number'=>$OrderID);
                    #-----------------------------------------------------------
                    $Replacing = Array_ToLine($Item,'%');
                    #-----------------------------------------------------------
                    $Tr = new Tag('TR');
                    #-----------------------------------------------------------
                    foreach($Childs as $Child){
                      #---------------------------------------------------------
                      $Td = Clone($Child);
                      #---------------------------------------------------------
                      foreach(Array_Keys($Replacing) as $Pattern){
                        #-------------------------------------------------------
                        $String = ($Replacing[$Pattern]?$Replacing[$Pattern]:'-');
                        #-------------------------------------------------------
                        $Td->Text = Str_Replace($Pattern,$String,$Td->Text);
                      }
                      #---------------------------------------------------------
                      $Tr->AddChild($Td);
                    }
                    #-----------------------------------------------------------
                    $DOM->AddChild('Items',$Tr);
                  break;
                  default:
                    return ERROR | @Trigger_Error(101);
                }
              }
              #-----------------------------------------------------------------
              $DOM->Delete('Item');
            }
            #-------------------------------------------------------------------
            $Document = $DOM->Build();
            if(Is_Error($Document))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Replace = Array_ToLine($Replace);
            #-------------------------------------------------------------------
            foreach(Array_Keys($Replace) as $LinkID){
              #-----------------------------------------------------------------
              $Text = (string)$Replace[$LinkID];
              #-----------------------------------------------------------------
              $Document = Str_Replace(SPrintF('%%%s%%',$LinkID),$Text?$Text:'-',$Document);
            }
            #-------------------------------------------------------------------
            $IsUpdate = DB_Update('Invoices',Array('Document'=>$Document),Array('ID'=>$InvoiceID));
            if(Is_Error($IsUpdate))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
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
#-------------------------------------------------------------------------------

?>
