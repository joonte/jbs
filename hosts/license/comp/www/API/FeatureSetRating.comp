
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$FeatureID = (integer) @$Args['FeatureID'];
$Rating    = (integer) @$Args['Rating'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Count = DB_Count('Features',Array('ID'=>$FeatureID));
if(Is_Error($Count))
  return ERROR | Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
  return new gException('FEATURE_NOT_FOUND','Указанное требование не найдено');
#-------------------------------------------------------------------------------
if($Rating < 1 || $Rating > 5)
  return new gException('WRONG_RATTING','Неверный рейтинг');
#-------------------------------------------------------------------------------
$IsInsert = DB_Insert('FeaturesRating',Array('FeatureID'=>$FeatureID,'UserID'=>$GLOBALS['__USER']['ID'],'Rating'=>$Rating));
if(Is_Error($IsInsert))
  return ERROR | Trigger_Error(500);
#-------------------------------------------------------------------------------
$FeatureRating = DB_Select('FeaturesRating',Array('AVG(`Rating`) as `Rating`'),Array('UNIQ','Where'=>SPrintF('`FeatureID` = %u',$FeatureID),'GroupBy'=>'FeatureID'));
#-------------------------------------------------------------------------------
switch(ValueOf($FeatureRating)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    return Array('Status'=>'Ok','Rating'=>SPrintF('%01.2f',$FeatureRating['Rating']));
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
