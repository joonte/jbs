<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$HostingServers = DB_Select('HostingServers',Array('ID','Address','SystemID','IP','IsOK','Notice','(SELECT `Name` FROM `HostingServersGroups` WHERE `HostingServersGroups`.`ID` = `HostingServers`.`ServersGroupID`) as `ServersGroupName`','(SELECT `Comment` FROM `HostingServersGroups` WHERE `HostingServersGroups`.`ID` = `HostingServers`.`ServersGroupID`) as `ServersGroupComment`','(SELECT (SUM(`UpTime`*`Count`)/SUM(`Count`)) as `UpTime` FROM `ServersUpTime` WHERE `ServersUpTime`.`ServerID` = `HostingServers`.`ID`) as `UpTime`'),Array('SortOn'=>'ServersGroupID'));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingServers)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return FALSE;
  case 'array':
    #---------------------------------------------------------------------------
    $Config = Config();
    #---------------------------------------------------------------------------
    $Systems = $Config['Hosting']['Systems'];
    #---------------------------------------------------------------------------
    $Rows = Array();
    #---------------------------------------------------------------------------
    $ServersGroupName = UniqID();
    #---------------------------------------------------------------------------
    foreach($HostingServers as $HostingServer){
      #-------------------------------------------------------------------------
      $Row = Array();
      #-------------------------------------------------------------------------
      if($HostingServer['ServersGroupName'] != $ServersGroupName){
         #----------------------------------------------------------------------
         $ServersGroupName = $HostingServer['ServersGroupName'];
         #----------------------------------------------------------------------
         $Comp = Comp_Load('Formats/String',$HostingServer['ServersGroupComment'],40);
         if(Is_Error($Comp))
           return ERROR | @Trigger_Error(500);
         #----------------------------------------------------------------------
         $Rows[] = new Tag('TR',new Tag('TD',Array('colspan'=>5,'class'=>'Separator'),new Tag('SPAN',Array('style'=>'font-size:16px;'),SPrintF('%s |',$ServersGroupName)),new Tag('SPAN',$Comp)));
      }
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Notice','HostingServers',$HostingServer['ID'],$HostingServer['Notice']);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Row[] = new Tag('TD',$Comp);
      $Row[] = new Tag('TD',Array('class'=>'Standard','onclick'=>SPrintF("HostingServerNoticeEdit(%u,'%s');",$HostingServer['ID'],$HostingServer['Notice'])),$HostingServer['Address']);
      $Row[] = new Tag('TD',Array('class'=>'Standard'),new Tag('SPAN',$HostingServer['IP']),new Tag('IMG',Array('alt'=>'+','class'=>'Button','onclick'=>SPrintF("window.open('http://www.reputationauthority.org/lookup.php?ip=%s&Submit.x=0&Submit.y=0&Submit=Search');",$HostingServer['IP']),'src'=>'SRC:{Images/Icons/Flag16.gif}')));
      $Row[] = new Tag('TD',Array('class'=>'Standard'),$Systems[$HostingServer['SystemID']]['Name']);
      $Row[] = new Tag('TD',Array('class'=>'Standard','align'=>'center'),SPrintF('%01.2f%%',$HostingServer['UpTime']));
      #-------------------------------------------------------------------------
      $Img = new Tag('IMG',Array('alt'=>'+','class'=>'Button','onclick'=>SPrintF("ShowWindow('/Administrator/ServerUpTimeInfo',{ServerID:%u});",$HostingServer['ID']),'width'=>16,'height'=>16,'src'=>SPrintF('SRC:{/Images/Icons/%s.gif}',$HostingServer['IsOK']?'Yes':'No')));
      #-------------------------------------------------------------------------
      $Row[] = new Tag('TD',Array('align'=>'center'),$Img);
      #-------------------------------------------------------------------------
      $Rows[] = $Row;
    }
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Tables/Extended',$Rows);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    return Array('Title'=>'Мониторинг серверов хостинга','DOM'=>$Comp);
  default:
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------

?>
