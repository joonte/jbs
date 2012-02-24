<?php


#-------------------------------------------------------------------------------
/** @author Лапшин С.М. (Joonte Ltd)*/
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$HostingServers = DB_Select('HostingServers',Array('ID','Address','Port','Services'));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingServers)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more..
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($HostingServers as $HostingServer){
      #-------------------------------------------------------------------------
      $IsOK = TRUE;
      #-------------------------------------------------------------------------
      $Services = Preg_Split('/\n+/',$HostingServer['Services']);
      #-------------------------------------------------------------------------
      foreach($Services as $Service){
        #-----------------------------------------------------------------------
        $Service = Explode('=',$Service);
        #-----------------------------------------------------------------------
        $ServiceName = Current($Service);
        #-----------------------------------------------------------------------
        $IsConnected = Is_Resource(@FsockOpen($HostingServer['Address'],IntVal(Next($Service)),$nError,$sError,0x5));
        #-----------------------------------------------------------------------
        if(!$IsConnected)
          $IsOK = FALSE;
        #----------------------------------------------------------------------- 
        $IPage = Array(
          #---------------------------------------------------------------------
          'TestDate' => Time(),
          'ServerID' => $HostingServer['ID'],
          'Service'  => Trim($ServiceName),
          'UpTime'   => ($IsConnected?100:0),
          'Day'      => Date('d'),
          'Month'    => Date('m'),
          'Year'     => Date('Y')
        );
        #-----------------------------------------------------------------------
        $IsInsert = DB_Insert('ServersUpTime',$IPage);
        if(Is_Error($IsInsert))
          return ERROR | @Trigger_Error(500);
      }
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('HostingServers',Array('TestDate'=>Time(),'IsOK'=>$IsOK),Array('ID'=>$HostingServer['ID']));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
    }
  break;
  default:
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
return 600;
#-------------------------------------------------------------------------------

?>
