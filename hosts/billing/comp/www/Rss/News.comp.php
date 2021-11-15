<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
Header('Content-Type: application/rss+xml');
#-------------------------------------------------------------------------------
echo "<" . "?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Profile = DB_Select('Profiles',Array('*'),Array('UNIQ','ID'=>100));
#-------------------------------------------------------------------------------
switch(ValueOf($Profile)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('PROFILE_NOT_FOUND','Профиль не найден');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Rss = new Tag('rss',Array('version'=>'2.0'));
#-------------------------------------------------------------------------------
$Channel = new Tag('channel');
$Channel->AddChild(new Tag('title',SPrintF('Новости компании %s',$Profile['Name'])));
$Channel->AddChild(new Tag('link',SPrintF('%s://%s',URL_SCHEME,HOST_ID)));
$Channel->AddChild(new Tag('description',SPrintF('Новости компании %s',$Profile['Name'])));
$Channel->AddChild(new Tag('copyright',HOST_ID));
$Channel->AddChild(new Tag('language','ru'));
$Channel->AddChild(new Tag('ttl',5));
$Channel->AddChild(new Tag('managingEditor',$Email = SPrintF('rss@%s',HOST_ID)));
#-------------------------------------------------------------------------------
$Versions = DB_Select('Clauses',Array('ID','PublicDate','Title','Text'),Array('Limits'=>Array(0,20),'Where'=>Array('`GroupID` = 2','`IsPublish` = "yes"'),'SortOn'=>'PublicDate','IsDesc'=>TRUE));
#-------------------------------------------------------------------------------
switch(ValueOf($Versions)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $Rss->ToXMLString();
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
foreach($Versions as $Version){
	#-------------------------------------------------------------------------------
	$Link = SPrintF('%s://%s/Rss/New?NewID=%u',URL_SCHEME,HOST_ID,$Version['ID']);
	#-------------------------------------------------------------------------------
	$Text = Strip_Tags($Version['Text']);
	#-------------------------------------------------------------------------------
	if(Mb_StrLen($Text) > 120)
		$Text = SPrintF('%s...',Mb_SubStr($Text,0,120));
	#-------------------------------------------------------------------------------
	$Item = new Tag('item');
	$Item->AddChild(new Tag('guid',$Link));
	$Item->AddChild(new Tag('title',$Version['Title']));
	$Item->AddChild(new Tag('description',$Text));
	$Item->AddChild(new Tag('author',$Email));
	$Item->AddChild(new Tag('pubDate',Date('r',$Version['PublicDate'])));
	$Item->AddChild(new Tag('link',$Link));
	#-------------------------------------------------------------------------------
	$Channel->AddChild($Item);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Rss->AddChild($Channel);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Rss->ToXMLString();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
