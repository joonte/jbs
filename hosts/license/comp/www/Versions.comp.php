<?
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#$smarty=$GLOBALS['smarty'];

#$versions_json = file_get_contents("http://jira.joonte.com/rest/api/2.0.alpha1/project/JBS/versions");

#$versions = array_reverse(Json_Decode($versions_json));
#$smarty->assign('versions', $versions);

#return $smarty->display('versions.tpl');
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$VersionID = (integer) @$Args['VersionID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/DOM.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM = new DOM();
#        die("ss");
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Main')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddText('Title','История версий');
#-------------------------------------------------------------------------------
$Img = new Tag('IMG',Array('border'=>0,'height'=>32,'width'=>32,'src'=>'SRC:{Images/Icons/Rss.gif}'));
#-------------------------------------------------------------------------------
$A = new Tag('A',Array('class'=>'Image','href'=>SPrintF('http://%s/Rss/Features',HOST_ID)),$Img);
#-------------------------------------------------------------------------------
$Into = new Tag('NOBODY',new Tag('TABLE',new Tag('TR',new Tag('TD',$A),new Tag('TD','RSS 2.0'))));
#-------------------------------------------------------------------------------
$Log = Array();
#-------------------------------------------------------------------------------
Exec('svn info svn://joonte.com/jbs',$Log);
#-------------------------------------------------------------------------------
$Log = Implode("\n",$Log);
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
if(Preg_Match('/Last\sChanged\sRev:\s([0-9]+)/',$Log,$Matches)){
  #-----------------------------------------------------------------------------
  $Matches = Next($Matches);
  #-----------------------------------------------------------------------------
  $Table[] = Array('Последняя ревизия',$Matches);
}
#-------------------------------------------------------------------------------
if(Preg_Match('/Last\sChanged\sAuthor:\s([a-zA-Z\-]+)/',$Log,$Matches)){
  #-----------------------------------------------------------------------------
  $Matches = Next($Matches);
  #-----------------------------------------------------------------------------
  $Table[] = Array('Последний автор',$Matches);
}
#-------------------------------------------------------------------------------
if(Preg_Match('/Last\sChanged\sDate:\s([0-9]{4})\-([0-9]{2})\-([0-9]{2})\s([0-9]{2}):([0-9]{2}):([0-9]{2})/',$Log,$Matches)){
  #-----------------------------------------------------------------------------
  Array_Shift($Matches);
  #-----------------------------------------------------------------------------
  $Date = Array_Combine(Array('Year','Month','Day','Hour','Minute','Second'),$Matches);
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Formats/Date/Extended',MkTime($Date['Hour'],$Date['Minute'],$Date['Second'],$Date['Month'],$Date['Day'],$Date['Year']));
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Table[] = Array('Дата обновления',$Comp);
}
#-------------------------------------------------------------------------------
if(Count($Table)){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Tables/Standard',$Table,'Мы работаем');
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Into->AddChild($Comp);
}
#-------------------------------------------------------------------------------
$Permission = FALSE;
#-------------------------------------------------------------------------------
if(IsSet($GLOBALS['__USER'])){
  #-----------------------------------------------------------------------------
  $Permission = Permission_Check('/Administrator/',(integer)$GLOBALS['__USER']['ID']);
  #-----------------------------------------------------------------------------
  switch(ValueOf($Permission)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return ERROR | @Trigger_Error(400);
    case 'true':
      # No more...
    case 'false':
      # No more...
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
if($Permission){
  #-----------------------------------------------------------------------------
  $Comp1 = Comp_Load('Buttons/Standard',Array('onclick'=>"ShowWindow('/Administrator/VersionEdit');"),'Новая версия','Add.gif');
  if(Is_Error($Comp1))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Comp2 = Comp_Load('Buttons/Standard',Array('onclick'=>"ShowWindow('/Administrator/FeatureEdit');"),'Новое изменение','Add.gif');
  if(Is_Error($Comp2))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Buttons/Panel',Array('Comp'=>$Comp1,'Name'=>'Новая версия'),Array('Comp'=>$Comp2,'Name'=>'Новое изменение'));
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Into->AddChild($Comp);
}
#-------------------------------------------------------------------------------
$Versions = DB_Select('Versions',Array('ID','CreateDate','Name','StatusID'),Array('SortOn'=>'CreateDate','IsDesc'=>TRUE));
#-------------------------------------------------------------------------------
switch(ValueOf($Versions)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $Table = new Tag('TABLE',Array('class'=>'Standard','cellspacing'=>5),new Tag('CAPTION','Версии биллинговой системы JBs'));
    #---------------------------------------------------------------------------
    $Tr = new Tag('TR');
    #---------------------------------------------------------------------------
    foreach($Versions as $Version){
      #-------------------------------------------------------------------------
      if(!$VersionID)
        $VersionID = $Version['ID'];
      #-------------------------------------------------------------------------
      $Comp = Comp_Load('Formats/Date/Standard',$Version['CreateDate']);
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $IsSelected = ($Version['ID'] == $VersionID);
      #-------------------------------------------------------------------------
      $Td = new Tag('TD',Array('class'=>'Standard','style'=>'font-size:16px;'),($IsSelected?new Tag('U',$Version['Name']):new Tag('A',Array('href'=>SPrintF('/Versions?VersionID=%u',$Version['ID'])),$Version['Name'])),new Tag('DIV',Array('style'=>'font-size:11px;color:848484;'),new Tag('CNAME',$Comp),new Tag('BR'),new Tag('B',$Version['StatusID'])));
      #-------------------------------------------------------------------------
      if($IsSelected)
        $Td->AddAttribs(Array('style'=>SPrintF('background-color:#F8F8F8;')));
      #-------------------------------------------------------------------------
      if($Permission)
        $Td->AddChild(new Tag('A',Array('style'=>'font-size:11px;','href'=>SPrintF("javascript:ShowWindow('/Administrator/VersionEdit',{VersionID:%u});",$Version['ID'])),new Tag('SPAN','[редактировать]')));
      #-------------------------------------------------------------------------
      $Tr->AddChild($Td);
      #-------------------------------------------------------------------------
      if(Count($Tr->Childs)%5 == 0){
        #-----------------------------------------------------------------------
        $Table->AddChild($Tr);
        #-----------------------------------------------------------------------
        $Tr = new Tag('TR');
      }
    }
    #---------------------------------------------------------------------------
    if(Count($Tr->Childs))
      $Table->AddChild($Tr);
    #---------------------------------------------------------------------------
    $Into->AddChild($Table);
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Version = DB_Select('Versions',Array('ID','CreateDate','Name','Comment'),Array('UNIQ','ID'=>$VersionID));
#-------------------------------------------------------------------------------
switch(ValueOf($Version)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Information','Версия не найдена.','Notice');
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Into->AddChild($Comp);
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $Table = new Tag('TABLE',Array('class'=>'Standard','cellspacing'=>5));
    #---------------------------------------------------------------------------
    $Types = Array('new'=>'Новая возможность','bug'=>'Исправление ошибки','update'=>'Улучшение');
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Formats/Date/Standard',$Version['CreateDate']);
    if(Is_Error($Comp))
       return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Td = new Tag('TD',Array('class'=>'Separator','colspan'=>2),new Tag('SPAN',SPrintF('%s от %s',$Version['Name'],$Comp)));
    #---------------------------------------------------------------------------
    $Table->AddChild(new Tag('TR',$Td));
    #---------------------------------------------------------------------------
    if($Comment = $Version['Comment']){
      #-------------------------------------------------------------------------
      $Comment = Comp_Load('Edesks/Text',$Comment);
      if(Is_Error($Comment))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $NoBody = new Tag('NOBODY',new Tag('B',Array('style'=>'color:#969696;'),'Комментарии:'),new Tag('BR'));
      $NoBody->AddHTML(SPrintF('<NOBODY>%s</NOBODY>',$Comment));
      #-------------------------------------------------------------------------
      $Table->AddChild(new Tag('TR',new Tag('TD',Array('class'=>'Standard','colspan'=>2),$NoBody)));
    }
    #---------------------------------------------------------------------------
    $Features = DB_Select('Features',Array('ID','TypeID','Title','Comment','(SELECT AVG(`Rating`) as `Rating` FROM `FeaturesRating` WHERE `FeatureID` = `Features`.`ID` GROUP BY `FeatureID`) as `Rating`'),Array('Where'=>SPrintF('`VersionID` = %u',$Version['ID'])));
    #---------------------------------------------------------------------------
    switch(ValueOf($Features)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        $Table->AddChild(new Tag('TR',new Tag('TD',Array('colspan'=>2,'class'=>'Standard'),'Изменения в версии не найдены.')));
      break;
      case 'array':
        #-----------------------------------------------------------------------
        foreach($Features as $Feature){
          #---------------------------------------------------------------------
          $Tr = new Tag('TR');
          #---------------------------------------------------------------------
          $TypeID = $Feature['TypeID'];
          #---------------------------------------------------------------------
          $Img = new Tag('IMG',Array('height'=>24,'width'=>24,'src'=>SPrintF('SRC:{Images/Icons/Version/%s.gif}',$TypeID)));
          #---------------------------------------------------------------------
          $Tr->AddChild(new Tag('TD',Array('align'=>'center'),$Img));
          #---------------------------------------------------------------------
          $NoBody = new Tag('NOBODY',new Tag('SPAN',Array('style'=>'color:#848484;font-size:11px;'),$Types[$TypeID]),new Tag('BR'),new Tag('SPAN',Array('style'=>'color:#5B8B15;font-size:14px;'),$Feature['Title']),new Tag('BR'));
          #---------------------------------------------------------------------
          $Comment = Comp_Load('Edesks/Text',$Feature['Comment']);
          if(Is_Error($Comment))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Span = new Tag('SPAN');
          $Span->AddHTML(SPrintF('<NOBODY>%s</NOBODY>',$Comment));
          #---------------------------------------------------------------------
          $NoBody->AddChild($Span);
          #---------------------------------------------------------------------
          if($Permission)
            $NoBody->AddChild(new Tag('DIV',Array('align'=>'right'),new Tag('HR'),new Tag('A',Array('style'=>'font-size:11px;','href'=>SPrintF("javascript:ShowWindow('/Administrator/FeatureEdit',{FeatureID:%u});",$Feature['ID'])),'[редактировать]')));
          #---------------------------------------------------------------------
          $Tr->AddChild(new Tag('TD',Array('class'=>'Standard'),$NoBody));
          #---------------------------------------------------------------------
          $Table->AddChild($Tr);
        }
      break;
      default:
        return ERROR | @Trigger_Error(101);
    }
    #---------------------------------------------------------------------------
    $Into->AddChild($Table);
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tab','Versions',$Into);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Comp);
#-------------------------------------------------------------------------------
$Out = $DOM->Build();
#-------------------------------------------------------------------------------
if(Is_Error($Out))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
?>