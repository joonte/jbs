<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('StatusID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
#Debug(SPrintF('[comp/DomainsOrders/Color]: %s',$StatusID));
switch($StatusID){
  case 'Waiting':
    $Color = 'F9E47D';
  break;
  case 'ClaimForRegister':
    $Color = 'BCF0FF';
  break;
  case 'ForContractRegister':
    $Color = 'ADC1F0';
  break;
  case 'OnContractRegister':
    $Color = 'ADC1F0';
  break;
  case 'ForRegister':
    $Color = 'ADC1F0';
  break;
  case 'OnRegister':
    $Color = 'BCF0FF';
  break;
  case 'OnDelegating':
    $Color = 'F1FCCE';
  break;
  case 'Active':
    $Color = 'D5F66C';
  break;
  case 'ForProlong':
    $Color = 'ADC1F0';
  break;
  case 'OnProlong':
    $Color = 'ADC1F0';
  break;
  case 'ForNsChange':
    $Color = 'ADC1F0';
  break;
  case 'OnNsChange':
    $Color = 'ADC1F0';
  break;
  case 'Suspended':
    $Color = 'FF6666';
  break;
  case 'Deleted':
    $Color = 'DCDCDC';
  break;
  case 'ForTransfer':
    $Color = 'ADD8E6';
  break;
  case 'OnTransfer':
    $Color = 'E8FAFF';
  break;
  default:
    $Color = '999999';
}
#-------------------------------------------------------------------------------
return Array('bgcolor'=>SPrintF('#%s',$Color));
#-------------------------------------------------------------------------------

?>
