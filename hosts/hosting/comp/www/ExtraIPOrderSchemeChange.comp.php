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
$ExtraIPOrderID = (integer) @$Args['ExtraIPOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/Tree.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','SchemeID','(SELECT `sGroupID` FROM `ExtraIPs` WHERE `ExtraIPs`.`ID` = `ExtraIPOrdersOwners`.`ServerID`) as `sGroupID`','StatusID');
#-------------------------------------------------------------------------------
$ExtraIPOrder = DB_Select('ExtraIPOrdersOwners',$Columns,Array('UNIQ','ID'=>$ExtraIPOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('ExtraIPOrdersRead',(integer)$__USER['ID'],(integer)$ExtraIPOrder['UserID']);
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
        if($ExtraIPOrder['StatusID'] != 'Active')
          return new gException('ORDER_NOT_ACTIVE','Заказ виртуального сервера не активен');
        #-----------------------------------------------------------------------
        $OldScheme = DB_Select('ExtraIPSchemes',Array('IsSchemeChange','IsReselling'),Array('UNIQ','ID'=>$ExtraIPOrder['SchemeID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($OldScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            if(!$OldScheme['IsSchemeChange'])
              return new gException('SCHEME_NOT_ALLOW_SCHEME_CHANGE','Тарифный план заказа виртуального сервера не позволяет смену тарифа');
            #-------------------------------------------------------------------
            $__USER = $GLOBALS['__USER'];
            #-------------------------------------------------------------------
            $UniqID = UniqID('ExtraIPSchemes');
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Services/Schemes','ExtraIPSchemes',$ExtraIPOrder['UserID'],Array('Name','sGroupID'),$UniqID);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Where = SPrintF("`sGroupID` = %u AND `ID` != %u AND `IsActive` = 'yes' AND `IsSchemeChangeable` = 'yes' AND `IsReselling` = '%s'",$ExtraIPOrder['sGroupID'],$ExtraIPOrder['SchemeID'],$OldScheme['IsReselling']?'yes':'no');
            #-------------------------------------------------------------------
            $ExtraIPSchemes = DB_Select($UniqID,Array('ID','Name'),Array('SortOn'=>'SortID','Where'=>$Where));
            #-------------------------------------------------------------------
            switch(ValueOf($ExtraIPSchemes)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return new gException('ExtraIP_SCHEMES_NOT_FOUND','Не тарифов для смены');
              case 'array':
                #---------------------------------------------------------------
                $DOM = new DOM();
                #---------------------------------------------------------------
                $Links = &Links();
                # Коллекция ссылок
                $Links['DOM'] = &$DOM;
                #---------------------------------------------------------------
                if(Is_Error($DOM->Load('Window')))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $DOM->AddText('Title','Смена тарифного плана');
                #---------------------------------------------------------------
                $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/ExtraIPOrderSchemeChange.js}')));
                #---------------------------------------------------------------
                $Table = $Options = Array();
                #---------------------------------------------------------------
                foreach($ExtraIPSchemes as $ExtraIPScheme)
                  $Options[$ExtraIPScheme['ID']] = $ExtraIPScheme['Name'];
                #---------------------------------------------------------------
                $Comp = Comp_Load('Form/Select',Array('name'=>'NewSchemeID'),$Options);
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Table[] = Array('Новый тарифный план',$Comp);
                #---------------------------------------------------------------
                $Comp = Comp_Load(
                  'Form/Input',
                  Array(
                    'type'    => 'button',
                    'onclick' => 'ExtraIPOrderSchemeChange();',
                    'value'   => 'Сменить'
                  )
                );
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Table[] = $Comp;
                #---------------------------------------------------------------
                $Comp = Comp_Load('Tables/Standard',$Table);
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Form = new Tag('FORM',Array('name'=>'ExtraIPOrderSchemeChangeForm','onsubmit'=>'return false;'),$Comp);
                #---------------------------------------------------------------
                $Comp = Comp_Load(
                  'Form/Input',
                  Array(
                    'name'  => 'ExtraIPOrderID',
                    'type'  => 'hidden',
                    'value' => $ExtraIPOrder['ID']
                  )
                );
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Form->AddChild($Comp);
                #---------------------------------------------------------------
                $DOM->AddChild('Into',$Form);
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
