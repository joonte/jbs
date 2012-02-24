<?php


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
$Channel->AddChild(new Tag('title','Новости компании'));
$Channel->AddChild(new Tag('link',SPrintF('http://%s',HOST_ID)));
$Channel->AddChild(new Tag('description','Новости компании'));
$Channel->AddChild(new Tag('copyright',HOST_ID));
$Channel->AddChild(new Tag('language','ru'));
$Channel->AddChild(new Tag('ttl',5));
$Channel->AddChild(new Tag('managingEditor',$Email = SPrintF('rss@%s',HOST_ID)));
#-------------------------------------------------------------------------------
$Versions = DB_Select('Clauses',Array('ID','PublicDate','Title','Text'),Array('Limits'=>Array(0,20),'Where'=>"`Partition` = 'News'",'SortOn'=>'PublicDate','IsDesc'=>TRUE));
#-------------------------------------------------------------------------------
switch(ValueOf($Versions)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    #---------------------------------------------------------------------------
    # no more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Versions as $Version){
       #------------------------------------------------------------------------
       $Link = SPrintF('http://%s/Rss/New?NewID=%u',HOST_ID,$Version['ID']);
       #------------------------------------------------------------------------
       $Text = Strip_Tags($Version['Text']);
       #------------------------------------------------------------------------
       if(Mb_StrLen($Text) > 120)
         $Text = SPrintF('%s...',Mb_SubStr($Text,0,120));
       #------------------------------------------------------------------------
       $Item = new Tag('item');
       $Item->AddChild(new Tag('guid',$Link));
       $Item->AddChild(new Tag('title',$Version['Title']));
       $Item->AddChild(new Tag('description',$Text));
       $Item->AddChild(new Tag('author',$Email));
       $Item->AddChild(new Tag('pubDate',Date('r',$Version['PublicDate'])));
       $Item->AddChild(new Tag('link',$Link));
       #------------------------------------------------------------------------
       $Channel->AddChild($Item);
    }
    #---------------------------------------------------------------------------
    $Rss->AddChild($Channel);
    #---------------------------------------------------------------------------
    Header('Content-Type: application/rss+xml');
    #---------------------------------------------------------------------------
    echo "<" . "?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
    #---------------------------------------------------------------------------
    return $Rss->ToXMLString();
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
