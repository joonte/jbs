
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Rss = new Tag('rss',Array('version'=>'2.0'));
#-------------------------------------------------------------------------------
$Channel = new Tag('channel');
$Channel->AddChild(new Tag('title','Версии и изменения системы  JBs'));
$Channel->AddChild(new Tag('link','http://www.joonte.com'));
$Channel->AddChild(new Tag('description','Последние изменения в системе JBs'));
$Channel->AddChild(new Tag('copyright','www.joonte.com'));
$Channel->AddChild(new Tag('language','ru'));
$Channel->AddChild(new Tag('ttl',5));
$Channel->AddChild(new Tag('managingEditor','office@joonte.com'));
#-------------------------------------------------------------------------------
$Image = new Tag('image');
$Image->AddChild(new Tag('url','http://www.joonte.com/styles/joonte/Images/RssLogo.png'));
$Image->AddChild(new Tag('title','www.joonte.com'));
$Image->AddChild(new Tag('link','http://www.joonte.com'));
$Channel->AddChild($Image);
#-------------------------------------------------------------------------------
$Features = DB_Select(Array('Features','Versions'),Array('`Features`.`ID` as `ID`','Title','`Features`.`Comment` as `Comment`','CreateDate'),Array('SortOn'=>'CreateDate','Where'=>"`Versions`.`ID` = `Features`.`VersionID` AND `StatusID` = 'Released'",'IsDesc'=>TRUE,'Limits'=>Array(0,20)));
#-------------------------------------------------------------------------------
switch(ValueOf($Features)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return FALSE;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Features as $Feature){
       #------------------------------------------------------------------------
       $Link = SPrintF('http://%s/Rss/Feature?FeatureID=%s',HOST_ID,$Feature['ID']);
       #------------------------------------------------------------------------
       $Item = new Tag('item');
       $Item->AddChild(new Tag('guid',$Link));
       $Item->AddChild(new Tag('title',$Feature['Title']));
       $Item->AddChild(new Tag('description',$Feature['Comment']));
       $Item->AddChild(new Tag('author','office@joonte.com'));
       $Item->AddChild(new Tag('pubDate',Date('r',$Feature['CreateDate'])));
       $Item->AddChild(new Tag('link',$Link));
       #------------------------------------------------------------------------
       $Channel->AddChild($Item);
    }
    #---------------------------------------------------------------------------
    $Rss->AddChild($Channel);
    #---------------------------------------------------------------------------
    Header('Content-Type: application/rss+xml');
    #---------------------------------------------------------------------------
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"\n";
    #---------------------------------------------------------------------------
    return $Rss->ToXMLString();
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
