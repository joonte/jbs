<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Domain','Zone','Length','DomainZone');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/

#-------------------------------------------------------------------------------
if(Mb_StrLen($Domain) > $Length)
  $Domain = SPrintF('%s...',Mb_SubStr($Domain,0,$Length));
#-------------------------------------------------------------------------------
$A = new Tag('A',Array('target'=>'blank','href'=>SPrintF('http://%s.%s/',$Domain,$DomainZone),'title'=>'Перейти на сайт'),$Domain);
#-------------------------------------------------------------------------------
return $A;
#-------------------------------------------------------------------------------

?>
