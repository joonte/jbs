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
$HostingSchemeID = (string) @$Args['HostingSchemeID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$HostingScheme = DB_Select('HostingSchemes','*',Array('UNIQ','ID'=>$HostingSchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingScheme)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $DOM = new DOM();
    #---------------------------------------------------------------------------
    $Links = &Links();
    # Коллекция ссылок
    $Links['DOM'] = &$DOM;
    #---------------------------------------------------------------------------
    if(Is_Error($DOM->Load('Window')))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $DOM->AddText('Title','Тариф хостинга');
    #---------------------------------------------------------------------------
    $Table = Array('Общая информация');
    #---------------------------------------------------------------------------
    $Table[] = Array('Название тарифа',$HostingScheme['Name']);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Formats/Currency',$HostingScheme['CostDay']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Цена 1 дн.',$Comp);
    #---------------------------------------------------------------------------
    $ServersGroup = DB_Select('HostingServersGroups','*',Array('UNIQ','ID'=>$HostingScheme['ServersGroupID']));
    if(!Is_Array($ServersGroup))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Группа серверов',$ServersGroup['Name']);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Formats/Logic',$HostingScheme['IsReselling']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Права реселлера',$Comp);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Formats/Logic',$HostingScheme['IsActive']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Тариф активен',$Comp);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Formats/Logic',$HostingScheme['IsProlong']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Возможность продления',$Comp);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Formats/Logic',$HostingScheme['IsSchemeChange']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Возможность смены тарифа',$Comp);
    #---------------------------------------------------------------------------
    $Table[] = 'Общие ограничения';
    #---------------------------------------------------------------------------
    $Table[] = Array('Дисковое пространство',SPrintF("%u Мб.",$HostingScheme['QuotaDisk']));
    #---------------------------------------------------------------------------
    $Table[] = Array('Почтовые ящики',$HostingScheme['QuotaEmail']);
    #---------------------------------------------------------------------------
    $Table[] = Array('Кол-во доменов',$HostingScheme['QuotaDomains']);
    #---------------------------------------------------------------------------
    $Table[] = Array('FTP пользователи',$HostingScheme['QuotaFTP']);
    #---------------------------------------------------------------------------
    $Table[] = Array('Кол-во псевдонимов домена',$HostingScheme['QuotaParkDomains']);
    #---------------------------------------------------------------------------
    $Table[] = Array('Кол-во поддоменов',$HostingScheme['QuotaSubDomains']);
    #---------------------------------------------------------------------------
    $Table[] = Array('Кол-во баз данных',$HostingScheme['QuotaDBs']);
    #---------------------------------------------------------------------------
    $Table[] = Array('Месячный трафик',SPrintF('%u Мб.',$HostingScheme['QuotaTraffic']));
    #---------------------------------------------------------------------------
    $Table[] = Array('Кол-во почтовых автоответчиков',$HostingScheme['QuotaEmailAutoResp']);
    #---------------------------------------------------------------------------
    $Table[] = Array('Кол-во списков рассылки',$HostingScheme['QuotaEmailLists']);
    #---------------------------------------------------------------------------
    $Table[] = Array('Кол-во пересылок почты',$HostingScheme['QuotaEmailForwards']);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Formats/Logic',$HostingScheme['IsShellAccess']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Secure Shell (SSH)',$Comp);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Formats/Logic',$HostingScheme['IsSSLAccess']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Secure Sockets Layer (SSL)',$Comp);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Formats/Logic',$HostingScheme['IsCGIAccess']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Common Gateway Interface (CGI)',$Comp);
    #---------------------------------------------------------------------------
    $SystemsIDs = Array();
    #---------------------------------------------------------------------------
    $HostingServers = DB_Select('HostingServers','SystemID',Array('Where'=>SPrintF('`ServersGroupID` = %u',$HostingScheme['ServersGroupID']),'GroupBy'=>'SystemID'));
    #---------------------------------------------------------------------------
    switch(ValueOf($HostingServers)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        # No more...
      break;
      case 'array':
        #-----------------------------------------------------------------------
        foreach($HostingServers as $HostingServer)
          $SystemsIDs[] = $HostingServer['SystemID'];
      break;
      default:
        return ERROR | @Trigger_Error(101);
    }
    #---------------------------------------------------------------------------
    if(In_Array('IspManager',$SystemsIDs)){
      #-------------------------------------------------------------------------
      $Table[] = 'Ограничения для ISPmanager';
      #-------------------------------------------------------------------------
      $Table[] = Array('Кол-во WWW доменов',$HostingScheme['QuotaWWWDomains']);
      #-------------------------------------------------------------------------
      $Table[] = Array('Кол-во почтовых доменов',$HostingScheme['QuotaEmailDomains']);
      #-------------------------------------------------------------------------
      $Table[] = Array('Кол-во пользователей баз данных',$HostingScheme['QuotaUsersDBs']);
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Formats/Logic',$HostingScheme['IsSSIAccess']);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Table[] = Array('Server Side Includes (SSI)',$Comp);
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Formats/Logic',$HostingScheme['IsPHPModAccess']);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Table[] = Array('PHP как модуль',$Comp);
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Formats/Logic',$HostingScheme['IsPHPCGIAccess']);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Table[] = Array('PHP как CGI',$Comp);
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Formats/Logic',$HostingScheme['IsPHPFastCGIAccess']);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Table[] = Array('PHP как FastCGI',$Comp);
    }
    #---------------------------------------------------------------------------
    if(In_Array('Cpanel',$SystemsIDs)){
      #-------------------------------------------------------------------------
      $Table[] = 'Ограничения для cPanel';
      #-------------------------------------------------------------------------
      $Table[] = Array('Кол-во дополнительных доменов',$HostingScheme['QuotaAddonDomains']);
    }
    #---------------------------------------------------------------------------
    if(In_Array('Plesk',$SystemsIDs)){
      #-------------------------------------------------------------------------
      $Table[] = 'Ограничения для Plesk';
      #-------------------------------------------------------------------------
      $Table[] = Array('Кол-во веб-пользователей',$HostingScheme['QuotaWebUsers']);
      #-------------------------------------------------------------------------
      $Table[] = Array('Ограничение на объем почтового ящика',$HostingScheme['QuotaEmailBox']);
      #-------------------------------------------------------------------------
      $Table[] = Array('Кол-во почтовых групп',$HostingScheme['QuotaEmailGroups']);
      #-------------------------------------------------------------------------
      $Table[] = Array('Кол-во веб-приложений',$HostingScheme['QuotaWebApp']);
    }
    #---------------------------------------------------------------------------
    if(In_Array('DirectAdmin',$SystemsIDs)){
      #-------------------------------------------------------------------------
      $Table[] = 'Ограничения для DirectAdmin';
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Formats/Logic',$HostingScheme['IsAnonimousFTP']);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Table[] = Array('Анонимные FTP',$Comp);
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Formats/Logic',$HostingScheme['IsPHPAccess']);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Table[] = Array('PHP интерфейс',$Comp);
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Formats/Logic',$HostingScheme['IsSpamAssasing']);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Table[] = Array('Почтовый антиспам',$Comp);
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Formats/Logic',$HostingScheme['IsCatchAll']);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Table[] = Array('Функция [catch all]',$Comp);
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Formats/Logic',$HostingScheme['IsSystemInfo']);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Table[] = Array('Доступ к системной информации',$Comp);
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Formats/Logic',$HostingScheme['IsDnsControll']);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Table[] = Array('Возможность DNS управления',$Comp);
    }
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Tables/Standard',$Table);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $DOM->AddChild('Into',$Comp);
    #---------------------------------------------------------------------------
    if(Is_Error($DOM->Build(FALSE)))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    return Array('Status'=>'Ok','DOM'=>$DOM->Object);
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
