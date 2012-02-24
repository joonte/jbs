<?php
#-------------------------------------------------------------------------------
/*<JBsDOC>
 <Target>file</Target>
 <Org>Joonte Ltd.</Org>
 <Author>Бреславский А.В.</Author>
</JBsDOC>*/
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$UpTimes = DB_Select('ServersUpTime',Array('ServerID','Service','TestDate','Day','Month','Year','AVG(`UpTime`) as `UpTime`','Count(`UpTime`) as `Count`'),Array('GroupBy'=>Array('ServerID','Service','Day','Month','Year')));
#-------------------------------------------------------------------------------
switch(ValueOf($UpTimes)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more..
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($UpTimes as $UpTime){
      #-------------------------------------------------------------------------
      $IsDelete = DB_Delete('ServersUpTime',Array('Where'=>SPrintF("`Day` = %u AND `Month` = %u AND `Year` = %u AND `ServerID` = %u AND `Service` = '%s'" ,$UpTime['Day'],$UpTime['Month'],$UpTime['Year'],$UpTime['ServerID'],$UpTime['Service'])));
      if(Is_Error($IsDelete))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $IPage = Array(
          #---------------------------------------------------------------------
          'ServerID' => $UpTime['ServerID'],
          'Service'  => $UpTime['Service'],
          'TestDate' => $UpTime['TestDate'],
          'Day'      => $UpTime['Day'],
          'Month'    => $UpTime['Month'],
          'Year'     => $UpTime['Year'],
          'UpTime'   => $UpTime['UpTime'],
          'Count'    => $UpTime['Count']
        );
        #-----------------------------------------------------------------------
        $IsInsert = DB_Insert('ServersUpTime',$IPage);
        if(Is_Error($IsInsert))
          return ERROR | @Trigger_Error(500);
    }
  break;
  default:
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>