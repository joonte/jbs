<?php

#-------------------------------------------------------------------------------
/** @author Лапшин С.М. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('IsCreate','Folder','StartDate','FinishDate','Details');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Result = Array('Title'=>'Отчет по партнерам');
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$NoBody->AddChild(new Tag('P','Данная статистика содержит информацию о партнерах и привлеченных ими клиентов.'));
#-------------------------------------------------------------------------------
if(!$IsCreate)
  return $Result;
#-------------------------------------------------------------------------------
$Partners = DB_Select('`Users` as `First`',Array('ID','Email','Name'),Array('Where'=>"EXISTS(SELECT * FROM `Users` WHERE `Users`.`OwnerID` = `First`.`ID`) AND `ID` > 1"));
#-------------------------------------------------------------------------------
switch(ValueOf($Partners)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return $Result;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Partners as $Partner){
      #-------------------------------------------------------------------------
      $Table = new Tag('TABLE',Array('class'=>'Standard','cellspacing'=>5));
      #-------------------------------------------------------------------------
      $NoBody->AddChild(new Tag('H2',SPrintF('Партнер: %s (%s)',$Partner['Name'],$Partner['Email'])));
      #-------------------------------------------------------------------------
      $Users = DB_Select('Users',Array('ID','Email','Name'),Array('SortOn'=>'RegisterDate','Where'=>SPrintF("`OwnerID` = %u AND `ID` != %u",$Partner['ID'],$Partner['ID'])));
      #-------------------------------------------------------------------------
      switch(ValueOf($Users)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return ERROR | @Trigger_Error(400);
        case 'array':
          #---------------------------------------------------------------------
          foreach($Users as $User){
            #-------------------------------------------------------------------
            #$Table->AddChild(new Tag('TD',Array('class'=>'Separator','colspan'=>6),SPrintF('Клиент: %s (%s)',$User['Name'],$User['Email'])));
            #-------------------------------------------------------------------
            $Invoices = DB_Select('InvoicesOwners',Array('ID','CreateDate','((YEAR(FROM_UNIXTIME(`CreateDate`)) - 1970)*12 + MONTH(FROM_UNIXTIME(`CreateDate`))) as `Month`','(SELECT `Customer` FROM `Contracts` WHERE `Contracts`.`ID` = `ContractID`) as `Customer`','PaymentSystemID','Summ','StatusID','StatusDate'),Array('SortOn'=>'CreateDate','Where'=>Array(SPrintF("`UserID` = %u",$User['ID']),SPrintF("`CreateDate` >= %u AND `CreateDate` <= %u",$StartDate,$FinishDate),"`IsPosted` = 'yes'")));
            #-------------------------------------------------------------------
            switch(ValueOf($Invoices)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                #$Table->AddChild(new Tag('TR',new Tag('TD',Array('class'=>'Standard','colspan'=>6,'style'=>'background-color:#FCE5CC;'),'Счета не найдены')));
              break;
              case 'array':
	        $Table->AddChild(new Tag('TD',Array('class'=>'Separator','colspan'=>6),SPrintF('Клиент: %s (%s)',$User['Name'],$User['Email'])));
                #---------------------------------------------------------------
                $Total = 0.00;
                #---------------------------------------------------------------
                $Month = NULL;
                #---------------------------------------------------------------
                foreach($Invoices as $Invoice){
                  #-------------------------------------------------------------
                  if($Invoice['Month'] != $Month){
                    #-----------------------------------------------------------
                    if($Month){
                      #---------------------------------------------------------
                      $Comp = Comp_Load('Formats/Currency',$Total);
                      if(Is_Error($Comp))
                        return ERROR | @Trigger_Error(500);
                      #---------------------------------------------------------
                      $Table->AddChild(new Tag('TR',new Tag('TD',Array('class'=>'Standard','colspan'=>6,'align'=>'right','style'=>'background-color:#F1FCCE;'),$Comp)));
                      #---------------------------------------------------------
                      $Total = 0.00;
                    }
                    #-----------------------------------------------------------
                    $Comp = Comp_Load('Formats/Date/Month',$Invoice['Month']);
                    if(Is_Error($Comp))
                      return ERROR | @Trigger_Error(500);
                    #-----------------------------------------------------------
                    $Table->AddChild(new Tag('TR',new Tag('TD',Array('class'=>'Standard','colspan'=>6,'style'=>'background-color:#FCE5CC;'),$Comp)));
                    #-----------------------------------------------------------
                    $Month = $Invoice['Month'];
                  }
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('Invoices/Color',$Invoice['StatusID']);
                  if(Is_Error($Comp))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  $Tr = new Tag('TR',$Comp);
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('Formats/Invoice/Number',$Invoice['ID']);
                  if(Is_Error($Comp))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  $Tr->AddChild(new Tag('TD',Array('class'=>'Transparent'),$Comp));
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('Formats/String',$Invoice['Customer'],25);
                  if(Is_Error($Comp))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  $Tr->AddChild(new Tag('TD',Array('class'=>'Transparent'),$Comp));
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('Formats/Date/Standard',$Invoice['CreateDate']);
                  if(Is_Error($Comp))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  $Tr->AddChild(new Tag('TD',Array('class'=>'Transparent'),$Comp));
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('Formats/Invoice/Type',$Invoice['PaymentSystemID']);
                  if(Is_Error($Comp))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  $Tr->AddChild(new Tag('TD',Array('class'=>'Transparent'),$Comp));
                  #-------------------------------------------------------------
                  $Total += (double)$Invoice['Summ'];
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('Formats/Currency',$Invoice['Summ']);
                  if(Is_Error($Comp))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  $Tr->AddChild(new Tag('TD',Array('class'=>'Transparent','align'=>'right'),$Comp));
                  #-------------------------------------------------------------
                  $Comp = Comp_Load('Formats/Status/Name','Invoices',$Invoice['StatusID'],$Invoice['ID']);
                  if(Is_Error($Comp))
                    return ERROR | @Trigger_Error(500);
                  #-------------------------------------------------------------
                  $Tr->AddChild(new Tag('TD',Array('class'=>'Transparent'),$Comp));
                  #-------------------------------------------------------------
                  $Table->AddChild($Tr);
                }
                #---------------------------------------------------------------
                $Comp = Comp_Load('Formats/Currency',$Total);
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Table->AddChild(new Tag('TR',new Tag('TD',Array('class'=>'Standard','colspan'=>6,'align'=>'right','style'=>'background-color:#F1FCCE;'),$Comp)));
                #---------------------------------------------------------------
                $Total = 0.00;
                #---------------------------------------------------------------
              break;
              default:
                return ERROR | @Trigger_Error(101);
            }
          }
          #---------------------------------------------------------------------
        break;
        default:
          return ERROR | @Trigger_Error(101);
      }
      #-------------------------------------------------------------------------
      $NoBody->AddChild($Table);
    }
    #---------------------------------------------------------------------------
    $Result['DOM'] = $NoBody;
    #---------------------------------------------------------------------------
    return $Result;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
?>
