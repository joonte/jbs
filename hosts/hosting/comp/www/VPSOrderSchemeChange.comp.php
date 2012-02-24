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
$VPSOrderID = (integer) @$Args['VPSOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class','libs/Tree.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','SchemeID','(SELECT `ServersGroupID` FROM `VPSServers` WHERE `VPSServers`.`ID` = `VPSOrdersOwners`.`ServerID`) as `ServersGroupID`','StatusID');
#-------------------------------------------------------------------------------
$VPSOrder = DB_Select('VPSOrdersOwners',$Columns,Array('UNIQ','ID'=>$VPSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('VPSOrdersRead',(integer)$__USER['ID'],(integer)$VPSOrder['UserID']);
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
        if($VPSOrder['StatusID'] != 'Active')
          return new gException('ORDER_NOT_ACTIVE','Заказ виртуального сервера не активен');
        #-----------------------------------------------------------------------
        $OldScheme = DB_Select('VPSSchemes',Array('IsSchemeChange','IsReselling'),Array('UNIQ','ID'=>$VPSOrder['SchemeID']));
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
            $UniqID = UniqID('VPSSchemes');
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Services/Schemes','VPSSchemes',$VPSOrder['UserID'],Array('Name','ServersGroupID'),$UniqID);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Where = SPrintF("`ServersGroupID` = %u AND `ID` != %u AND `IsActive` = 'yes' AND `IsSchemeChangeable` = 'yes' AND `IsReselling` = '%s'",$VPSOrder['ServersGroupID'],$VPSOrder['SchemeID'],$OldScheme['IsReselling']?'yes':'no');
            #-------------------------------------------------------------------
            $VPSSchemes = DB_Select($UniqID,Array('ID','Name'),Array('SortOn'=>'SortID','Where'=>$Where));
            #-------------------------------------------------------------------
            switch(ValueOf($VPSSchemes)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return new gException('VPS_SCHEMES_NOT_FOUND','Не тарифов для смены');
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
                $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/VPSOrderSchemeChange.js}')));
                #---------------------------------------------------------------
                $Table = $Options = Array();
                #---------------------------------------------------------------
                foreach($VPSSchemes as $VPSScheme)
                  $Options[$VPSScheme['ID']] = $VPSScheme['Name'];
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
                    'onclick' => 'VPSOrderSchemeChange();',
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
                $Form = new Tag('FORM',Array('name'=>'VPSOrderSchemeChangeForm','onsubmit'=>'return false;'),$Comp);
                #---------------------------------------------------------------
                $Comp = Comp_Load(
                  'Form/Input',
                  Array(
                    'name'  => 'VPSOrderID',
                    'type'  => 'hidden',
                    'value' => $VPSOrder['ID']
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
