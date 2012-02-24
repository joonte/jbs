<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/Http.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Request = Array('date_req'=>SPrintF('%s/%s/%s',Date('d'),Date('m'),Date('Y')));
#-------------------------------------------------------------------------------
$Http = Array('Address'=>'www.cbr.ru','Host'=>'www.cbr.ru','Port'=>'80');
#-------------------------------------------------------------------------------
$Response = Http_Send('/scripts/XML_daily.asp',$Http,$Request);
if(Is_Error($Response))
  return 3600;
#-------------------------------------------------------------------------------
if(!Preg_Match('/HTTP\/1\.1\s200/',$Response['Heads']))
  return 3600;
#-------------------------------------------------------------------------------
$XML = String_XML_Parse($Response['Body']);
if(Is_Exception($XML))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$XML = $XML->ToArray('Valute');
#-------------------------------------------------------------------------------
$Courses = (array)$XML['ValCurs'];
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$cPaymentSystems = $Config['Invoices']['PaymentSystems'];
#-------------------------------------------------------------------------------
$HostsIDs = $GLOBALS['HOST_CONF']['HostsIDs'];
#-------------------------------------------------------------------------------
$Config = System_XML('config/Config.xml',Current($HostsIDs));
if(Is_Error($Config))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!IsSet($Config['Invoices']))
  $Config['Invoices'] = Array();
#-------------------------------------------------------------------------------
$Invoices = &$Config['Invoices'];
#-------------------------------------------------------------------------------
if(!IsSet($Invoices['PaymentSystems']))
  $Invoices['PaymentSystems'] = Array();
#-------------------------------------------------------------------------------
$PaymentSystems = &$Invoices['PaymentSystems'];
#-------------------------------------------------------------------------------
foreach($cPaymentSystems as $PaymentSystemID=>$PaymentSystem){
  #-----------------------------------------------------------------------------
  foreach($Courses as $Course){
    #---------------------------------------------------------------------------
    if(IsSet($PaymentSystems[$PaymentSystemID]) && @$Course['CharCode'] == $PaymentSystem['Valute'] && $PaymentSystem['IsCourseUpdate']){
      #-------------------------------------------------------------------------
      $Current = (string)SPrintF('%01.2f',FloatVal(Str_Replace(',','.',@$Course['Value']))/@$Course['Nominal']);
      #-------------------------------------------------------------------------
      $PaymentSystems[$PaymentSystemID]['Course'] = $Current;
    }
  }
}
#-------------------------------------------------------------------------------
$Path = System_Element('config/Config.xml');
if(Is_Error($Path))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsWrite = IO_Write($Path,To_XML_String($Config),TRUE);
if(Is_Error($IsWrite))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsFlush = MemoryCache_Flush();
if(Is_Error($IsFlush))
  @Trigger_Error(500);
#-------------------------------------------------------------------------------
return MkTime(2,0,0,Date('n'),Date('j')+1,Date('Y'));
#-------------------------------------------------------------------------------

?>
