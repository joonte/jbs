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
$ServiceOrderID = (integer) @$Args['ServiceOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','OrderDate','ContractID','ExpirationDate','StatusID','StatusDate','(SELECT `Name` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) as `ServiceName`','IsAutoProlong');
#-------------------------------------------------------------------------------
$ServiceOrder = DB_Select('OrdersOwners',$Columns,Array('UNIQ','ID'=>$ServiceOrderID));
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
    $IsPermission = Permission_Check('ServiceOrderRead',(integer)$__USER['ID'],(integer)$ServiceOrder['UserID']);
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
        $DOM->AddText('Title','Информация о заказе на услугу');
        #-----------------------------------------------------------------------
        $Table = Array('Общая информация');
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Date/Extended',$ServiceOrder['OrderDate']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Дата заказа',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Contract/Number',$ServiceOrder['ContractID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Договор №',$Comp);
        #-----------------------------------------------------------------------
        $Table[] = Array('Название услуги',$ServiceOrder['ServiceName']);
        #-----------------------------------------------------------------------
        $ExpirationDate = $ServiceOrder['ExpirationDate'];
        #-----------------------------------------------------------------------
        if($ExpirationDate){
          #---------------------------------------------------------------------
          $Comp = Comp_Load('Formats/Date/Standard',$ExpirationDate);
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Table[] = Array('Дата окончания',$Comp);
        }
        #-----------------------------------------------------------------------
        $ServiceOrderFields = DB_Select('OrdersFields',Array('ID','ServiceFieldID','Value','FileName'),Array('Where'=>SPrintF('`OrderID` = %u',$ServiceOrderID)));
        #-----------------------------------------------------------------------
        switch(ValueOf($ServiceOrderFields)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            # No more...
          break;
          case 'array':
            #-------------------------------------------------------------------
            $Fields = Array();
            #-------------------------------------------------------------------
            foreach($ServiceOrderFields as $ServiceOrderField){
              #-----------------------------------------------------------------
              $Value = $ServiceOrderField['Value'];
              #-----------------------------------------------------------------
              $ServiceField = DB_Select('ServicesFields',Array('Name','TypeID','Options'),Array('UNIQ','ID'=>$ServiceOrderField['ServiceFieldID']));
              #-----------------------------------------------------------------
              switch(ValueOf($ServiceField)){
                case 'error':
                  return ERROR | @Trigger_Error(500);
                case 'exception':
                  return ERROR | @Trigger_Error(400);
                case 'array':
                  #-------------------------------------------------------------
                  switch($ServiceField['TypeID']){
                    case 'Select':
                      #---------------------------------------------------------
                      $Options = Explode("\n",$ServiceField['Options']);
                      #---------------------------------------------------------
                      if(Count($Options)){
                        #-------------------------------------------------------
                        $Alternatives = Array();
                        #-------------------------------------------------------
                        foreach($Options as $Option){
                          #-----------------------------------------------------
                          $Option = Explode("=",$Option);
                          #-----------------------------------------------------
                          $Alternatives[Current($Option)] = Next($Option);
                        }
                        #-------------------------------------------------------
                        if(IsSet($Alternatives[$Value]))
                          $Value = $Alternatives[$Value];   
                      }else
                        $Value = 'Список выбора поля';
                    break;
                    case 'File':
                      #---------------------------------------------------------
                      $FileName = $ServiceOrderField['FileName'];
                      #---------------------------------------------------------
                      if(Mb_StrLen($FileName) > 15)
                        $FileName = SPrintF('%s...',Mb_SubStr($FileName,0,15));
                      #---------------------------------------------------------
                      $Value = new Tag('TD',Array('class'=>'Standard'),new Tag('A',Array('href'=>SPrintF('/OrderFileDownload?OrderFieldID=%u',$ServiceOrderField['ID'])),SPrintF('%s (%01.2f Кб.)',$FileName,StrLen(Base64_Decode($Value))/1024)));
                    break;
                    case 'Hidden':
                      continue 2;
                    default:
                      # No more...
                  }
                  #-------------------------------------------------------------
                  $Fields[] = Array($ServiceField['Name'],$Value);
                break;
                default:
                  return ERROR | @Trigger_Error(101);
              }
            }
            #-------------------------------------------------------------------
            if(Count($Fields)){
              #-----------------------------------------------------------------
              $Table[] = 'Параметры заказа';
              #-----------------------------------------------------------------
              $Table = Array_Merge($Table,$Fields);
            }
          break;
          default:
            return ERROR | @Trigger_Error(101);
        }
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	$Table[] = 'Прочее';
	#-----------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Logic',$ServiceOrder['IsAutoProlong']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-----------------------------------------------------------------------
	$Table[] = Array('Автопродление',$Comp);
	#-----------------------------------------------------------------------
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Statuses/State','Orders',$ServiceOrder);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table = Array_Merge($Table,$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Tables/Standard',$Table);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
	$Form = new Tag('FORM',Array('method'=>'POST'),$Comp);
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	$Comp = Comp_Load(
			'Form/Input',
			Array(
				'type'  => 'hidden',
				'name'  => 'OrderID',
				'value' => $ServiceOrder['ID']
				)
			);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-----------------------------------------------------------------------
        $Form->AddChild($Comp);
        $DOM->AddChild('Into',$Form);
        #-----------------------------------------------------------------------
        if(Is_Error($DOM->Build(FALSE)))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        return Array('Status'=>'Ok','DOM'=>$DOM->Object);
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
