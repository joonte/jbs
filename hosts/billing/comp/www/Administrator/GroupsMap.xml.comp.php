<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
function CreateMap($ID,&$ParentNode){
  #-----------------------------------------------------------------------------
  $Group = DB_Select('Groups','*',Array('UNIQ','ID'=>$ID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Group)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return ERROR | @Trigger_Error(400);
    case 'array':
      #-------------------------------------------------------------------------
      $Node = new Tag('node',Array('TEXT'=>$Group['Name'],'STYLE'=>'bubble','BACKGROUND_COLOR'=>$Group['IsDepartment']?'#F07D00':'#F9E47D'),new Tag('edge',Array('STYLE'=>'sharp_bezier','COLOR'=>'#D5F66C','WIDTH'=>5)),new Tag('font',Array('BOLD'=>'true','NAME'=>'SansSerif','SIZE'=>14)),new Tag('icon',Array('BUILTIN'=>'none')));
      #-------------------------------------------------------------------------
      $Childs = DB_Select('Groups','*',Array('Where'=>SPrintF('`ParentID` = %u AND `ParentID` != `ID`',$Group['ID'])));
      #-------------------------------------------------------------------------
      switch(ValueOf($Childs)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          # No more...
        break;
        case 'array':
          #---------------------------------------------------------------------
          foreach($Childs as $Child)
            CreateMap($Child['ID'],$Node);
        break;
        default:
          return ERROR | @Trigger_Error(101);
      }
      #-------------------------------------------------------------------------
      $ParentNode->AddChild($Node);
      #-------------------------------------------------------------------------
      return TRUE;
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
$Map = new Tag('map',Array('version'=>'0.8.0'));
#-------------------------------------------------------------------------------
$Map->AddChild(new Tag('hook',Array('NAME'=>'accessories/plugins/AutomaticLayout.properties')));
#-------------------------------------------------------------------------------
CreateMap(1,$Map);
#-------------------------------------------------------------------------------
return $Map->ToXMLString();
#-------------------------------------------------------------------------------

?>
