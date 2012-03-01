<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$ISPswOrderID = (integer) @$Args['ISPswOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/Tree.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','SchemeID','(SELECT `SoftWareGroup` FROM `ISPswSchemes` WHERE `ISPswSchemes`.`ID` = `SchemeID`) AS SoftWareGroup','StatusID');
#-------------------------------------------------------------------------------
$ISPswOrder = DB_Select('ISPswOrdersOwners',$Columns,Array('UNIQ','ID'=>$ISPswOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('ISPswOrdersRead',(integer)$__USER['ID'],(integer)$ISPswOrder['UserID']);
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
#        if($ISPswOrder['StatusID'] != 'Active')
#          return new gException('ORDER_NOT_ACTIVE','Заказ программного обеспечения не активен');
        #-----------------------------------------------------------------------
        $OldScheme = DB_Select('ISPswSchemes',Array('IsSchemeChange'),Array('UNIQ','ID'=>$ISPswOrder['SchemeID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($OldScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            if(!$OldScheme['IsSchemeChange'])
              return new gException('SCHEME_NOT_ALLOW_SCHEME_CHANGE','Тарифный план заказа программного обеспечения не позволяет смену тарифа');
            #-------------------------------------------------------------------
            $__USER = $GLOBALS['__USER'];
            #-------------------------------------------------------------------
            $UniqID = UniqID('ISPswSchemes');
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Services/Schemes','ISPswSchemes',$ISPswOrder['UserID'],Array('Name','SoftWareGroup'),$UniqID);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Where = SPrintF("`SoftWareGroup` = %u AND `ID` != %u AND `IsActive` = 'yes' AND `IsSchemeChangeable` = 'yes'",$ISPswOrder['SoftWareGroup'],$ISPswOrder['SchemeID']);
            #-------------------------------------------------------------------
            $ISPswSchemes = DB_Select($UniqID,Array('ID','Name'),Array('SortOn'=>'SortID','Where'=>$Where));
            #-------------------------------------------------------------------
            switch(ValueOf($ISPswSchemes)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return new gException('ISPsw_SCHEMES_NOT_FOUND','Нет тарифов для смены');
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
                $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/ISPswOrderSchemeChange.js}')));
                #---------------------------------------------------------------
                $Table = $Options = Array();
                #---------------------------------------------------------------
                foreach($ISPswSchemes as $ISPswScheme)
                  $Options[$ISPswScheme['ID']] = $ISPswScheme['Name'];
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
                    'onclick' => 'ISPswOrderSchemeChange();',
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
                $Form = new Tag('FORM',Array('name'=>'ISPswOrderSchemeChangeForm','onsubmit'=>'return false;'),$Comp);
                #---------------------------------------------------------------
                $Comp = Comp_Load(
                  'Form/Input',
                  Array(
                    'name'  => 'ISPswOrderID',
                    'type'  => 'hidden',
                    'value' => $ISPswOrder['ID']
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
