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
$ClauseID = (integer) @$Args['ClauseID'];
$Rating   = (integer) @$Args['Rating'];
#-------------------------------------------------------------------------------
$Count = DB_Count('Clauses',Array('ID'=>$ClauseID));
if(Is_Error($Count))
  return ERROR | Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
  return new gException('CLAUSE_NOT_FOUND','Указанная статья не найдена');
#-------------------------------------------------------------------------------
if($Rating < 1 || $Rating > 5)
  return new gException('WRONG_RATTING','Неверный рейтинг');
#-------------------------------------------------------------------------------
$IsInsert = DB_Insert('ClausesRating',Array('ClauseID'=>$ClauseID,'IP'=>$_SERVER['REMOTE_ADDR'],'Rating'=>$Rating));
if(Is_Error($IsInsert))
  return ERROR | Trigger_Error(500);
#-------------------------------------------------------------------------------
$ClauseRating = DB_Select('ClausesRating',Array('AVG(`Rating`) as `Rating`'),Array('UNIQ','Where'=>SPrintF('`ClauseID` = %u',$ClauseID),'GroupBy'=>'ClauseID'));
#-------------------------------------------------------------------------------
switch(ValueOf($ClauseRating)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    return Array('Status'=>'Ok','Rating'=>SPrintF('%01.2f',$ClauseRating['Rating']));
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
