<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('StatusID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
switch($StatusID){
  case 'Waiting':
    $Color = 'FFE322';
  break;
  case 'Sended':
    $Color = 'D5F66C';
  break;
  case 'Received':
    $Color = '7799E6';
  break;
  default:
    $Color = 'EAEAEA';
}
#-------------------------------------------------------------------------------
return Array('bgcolor'=>SPrintF('#%s',$Color));
#-------------------------------------------------------------------------------

?>
