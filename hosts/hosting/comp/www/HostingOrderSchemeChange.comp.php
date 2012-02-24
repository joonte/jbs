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
$HostingOrderID = (integer) @$Args['HostingOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class','libs/Tree.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','SchemeID','(SELECT `ServersGroupID` FROM `HostingServers` WHERE `HostingServers`.`ID` = `HostingOrdersOwners`.`ServerID`) as `ServersGroupID`','StatusID');
#-------------------------------------------------------------------------------
$HostingOrder = DB_Select('HostingOrdersOwners',$Columns,Array('UNIQ','ID'=>$HostingOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('HostingOrdersRead',(integer)$__USER['ID'],(integer)$HostingOrder['UserID']);
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
        if($HostingOrder['StatusID'] != 'Active')
          return new gException('ORDER_NOT_ACTIVE','Заказ хостинга не активен');
        #-----------------------------------------------------------------------
        $OldScheme = DB_Select('HostingSchemes',Array('IsSchemeChange','IsReselling'),Array('UNIQ','ID'=>$HostingOrder['SchemeID']));
        #-----------------------------------------------------------------------
        switch(ValueOf($OldScheme)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            if(!$OldScheme['IsSchemeChange'])
              return new gException('SCHEME_NOT_ALLOW_SCHEME_CHANGE','Тарифный план заказа хостинга не позволяет смену тарифа');
            #-------------------------------------------------------------------
            $__USER = $GLOBALS['__USER'];
            #-------------------------------------------------------------------
            $UniqID = UniqID('HostingSchemes');
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Services/Schemes','HostingSchemes',$HostingOrder['UserID'],Array('Name','ServersGroupID'),$UniqID);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Where = SPrintF("`ServersGroupID` = %u AND `ID` != %u AND `IsActive` = 'yes' AND `IsSchemeChangeable` = 'yes' AND `IsReselling` = '%s'",$HostingOrder['ServersGroupID'],$HostingOrder['SchemeID'],$OldScheme['IsReselling']?'yes':'no');
            #-------------------------------------------------------------------
            $HostingSchemes = DB_Select($UniqID,Array('ID','Name'),Array('SortOn'=>'SortID','Where'=>$Where));
            #-------------------------------------------------------------------
            switch(ValueOf($HostingSchemes)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return new gException('HOSTING_SCHEMES_NOT_FOUND','Не тарифов для смены');
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
                $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/HostingOrderSchemeChange.js}')));
                #---------------------------------------------------------------
                $Table = $Options = Array();
                #---------------------------------------------------------------
                foreach($HostingSchemes as $HostingScheme)
                  $Options[$HostingScheme['ID']] = $HostingScheme['Name'];
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
                    'onclick' => 'HostingOrderSchemeChange();',
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
                $Form = new Tag('FORM',Array('name'=>'HostingOrderSchemeChangeForm','onsubmit'=>'return false;'),$Comp);
                #---------------------------------------------------------------
                $Comp = Comp_Load(
                  'Form/Input',
                  Array(
                    'name'  => 'HostingOrderID',
                    'type'  => 'hidden',
                    'value' => $HostingOrder['ID']
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
