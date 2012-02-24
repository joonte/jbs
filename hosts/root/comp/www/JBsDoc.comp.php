<?php


#-------------------------------------------------------------------------------
Header('Content-type: text/plain; charset=utf-8');
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/DOM.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Files = IO_Files(SPrintF('%s/hosts',SYSTEM_PATH));
if(Is_Error($Files))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$JBsDocPath = SPrintF('%s/jbsdoc',SYSTEM_PATH);
#-------------------------------------------------------------------------------
$IsWrite = IO_Write(SPrintF('%s/Dictionary.js',$JBsDocPath),"\$Dictionary = [];\n",TRUE);
if(Is_Error($IsWrite))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$FilesList = Array();
#-------------------------------------------------------------------------------
foreach($Files as $File){
  #-----------------------------------------------------------------------------
  if(!Preg_Match('/(.class|.php)$/',$File))
    continue;
  #-----------------------------------------------------------------------------
  $Source = IO_Read($File);
  if(Is_Error($Source))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  if(Preg_Match_All('/\/\*\*\n.+\*\//sU',$Source,$Matches)){
    #---------------------------------------------------------------------------
    $FilesList[] = $File;
    #---------------------------------------------------------------------------
    echo SPrintF("Читаем файл %s\n",$File);
    #---------------------------------------------------------------------------
    $Docs = Current($Matches);
    #---------------------------------------------------------------------------
    if(Preg_Match_All('/(public|private|)+\sfunction\s([a-zA-Z\_]+)\(([\$a-zA-Z0-9\_\=\s\,\&\(\)\']*)\){.*/siU',$Source,$Matches)){
      #-------------------------------------------------------------------------
      $Polimoths = Next($Matches);
      $Functions = Next($Matches);
      $Arguments = Next($Matches);
      #-------------------------------------------------------------------------
      echo SPrintF("Кол-во описаний %u\nКол-во функций %u\n",Count($Functions),Count($Docs));
      #-------------------------------------------------------------------------
      $DOM = new DOM();
      #-------------------------------------------------------------------------
      if(Is_Error($DOM->Load('JBsDoc')))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      if(Preg_Match('/(.*)(.class|.php)$/',BaseName($File),$String))
        $DOM->AddText('Title',Next($String));
      #-------------------------------------------------------------------------
      $Indexes = new Tag('OL',Array('class'=>'Standard'));
      #-------------------------------------------------------------------------
      for($i=0;$i<Count($Docs);$i++){
        #-----------------------------------------------------------------------
        if(!IsSet($Docs[$i]))
          continue;
        #-----------------------------------------------------------------------
        $Doc = &$Docs[$i];
        #-----------------------------------------------------------------------
        $Content = Explode("\n*\n",$Doc);
        #-----------------------------------------------------------------------
        $Doc = Array(Current($Content),Next($Content),Next($Content));
        #-----------------------------------------------------------------------
        #for($j=0;$j<Count($Doc);$j++)
        #  $Doc[$j] = Preg_Replace("/[\*\n]+/",$Doc[$j],'');
        #-----------------------------------------------------------------------
        $IsWrite = IO_Write(SPrintF('%s/Dictionary.js',$JBsDocPath),SPrintF("\$Dictionary.push({FunctionID:'%s',File:'%s'});\n",$Functions[$i],basename($File)),FALSE);
        if(Is_Error($IsWrite))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $UniqID = UniqID('ID');
        #-----------------------------------------------------------------------
        $Indexes->AddChild(new Tag('LI',new Tag('A',Array('href'=>SPrintF('#%s',$UniqID)),$Functions[$i]),new Tag('SPAN',Current($Doc))));
        #-----------------------------------------------------------------------
        $H1 = new Tag('H1',Array('id'=>$UniqID));
        #-----------------------------------------------------------------------
        if($String = $Polimoths[$i])
          $H1->AddChild(new Tag('SPAN',Array('style'=>'font-size:12px;'),$String));
        #-----------------------------------------------------------------------
        $H1->AddChild(new Tag('SPAN',$Functions[$i]));
        #-----------------------------------------------------------------------
        $Params = @$Doc[2];
        #-----------------------------------------------------------------------
        $Params = Explode("\n",$Params);
        #-----------------------------------------------------------------------
        $Ul = new Tag('UL',Array('class'=>'Standard'));
        #-----------------------------------------------------------------------
        $Parameters = Explode(',',$Arguments[$i]);
        #-----------------------------------------------------------------------
        if(Count($Parameters)){
          #---------------------------------------------------------------------
          $Array = Array();
          #---------------------------------------------------------------------
          for($j=0;$j<Count($Parameters);$j++){
            #-------------------------------------------------------------------
            if(!IsSet($Params[$j]))
              continue;
            #-------------------------------------------------------------------
            $Parameter = Explode('=',$Parameters[$j]);
            #-------------------------------------------------------------------
            if(Preg_Match('/@param\s([a-z]+)\s<(.+)>/',$Params[$j],$Index)){
              #-----------------------------------------------------------------
              $Li = new Tag('LI');
              #-----------------------------------------------------------------
              $Text = Trim(Current($Parameter));
              #-----------------------------------------------------------------
              $Array[] = (Count($Parameter) > 1?SPrintF('[%s]',$Text):$Text);
              #-----------------------------------------------------------------
              $Li->AddChild(new Tag('SPAN',$Text));
              $Li->AddChild(new Tag('SPAN',Array('style'=>'color:green;font-weight:bold;'),Next($Index)));
              $Li->AddChild(new Tag('SPAN',Next($Index)));
              #-----------------------------------------------------------------
              if(Count($Parameter) > 1)
                $Li->AddChild(new Tag('SPAN',Array('style'=>'color:blue;'),SPrintF(' default = %s',Trim(Next($Parameter)))));
              #-----------------------------------------------------------------
              $Ul->AddChild($Li);
            }
          }
          #---------------------------------------------------------------------
          if(Count($Array))
            $H1->AddChild(new Tag('SPAN',Array('style'=>'font-size:14px;'),SPrintF('(%s)',Implode(', ',$Array))));
        }
        #-----------------------------------------------------------------------
        $DOM->AddChild('Into',$H1);
        #-----------------------------------------------------------------------
        $DOM->AddChild('Into',new Tag('H2',$Doc[0]));
        #-----------------------------------------------------------------------
        if(Count($Ul->Childs))
          $DOM->AddChild('Into',$Ul);
        #-----------------------------------------------------------------------
        $Pre = new Tag('PRE',Array('class'=>'Standard'),$Doc[1]);
        #-----------------------------------------------------------------------
        $DOM->AddChild('Into',$Pre);
        #-----------------------------------------------------------------------
        $DOM->AddChild('Into',new Tag('HR',Array('size'=>'2')));
      }
      #-------------------------------------------------------------------------
      $DOM->AddChild('Into',$Indexes,TRUE);
      #-------------------------------------------------------------------------
      $Out = $DOM->Build();
      if(Is_Error($Out))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Files[] = BaseName($File);
      #-------------------------------------------------------------------------
      $IsWrite = IO_Write(SPrintF('%s/%s.html',$JBsDocPath,BaseName($File)),$Out,TRUE);
    }
    #---------------------------------------------------------------------------
    echo "OK\n";
  }
}
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('JBsDoc')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Список файлов');
#-------------------------------------------------------------------------------
$Title = new Tag('UL',Array('class'=>'Standard'));
#-------------------------------------------------------------------------------
foreach($Files as $File)
  $Title->AddChild(new Tag('LI',new Tag('A',Array('href'=>SPrintF('%s.html',BaseName($File))),BaseName($File))));
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Title);
#-------------------------------------------------------------------------------
$Out = $DOM->Build();
#-------------------------------------------------------------------------------
$IsWrite = IO_Write(SPrintF('%s/FileList.html',$JBsDocPath),$Out,TRUE);
if(Is_Error($IsWrite))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('JBsDoc')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',new Tag('SCRIPT',Array('src'=>'Dictionary.js')));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',new Tag('SCRIPT',Array('src'=>'Search.js')));
#-------------------------------------------------------------------------------
$DOM->AddAttribs('Body',Array('onLoad'=>'Refresh();'));
#-------------------------------------------------------------------------------
$Parse = <<<EOD
<TABLE class="Standard" cellspacing="5" cellpadding="5" width="100%" height="100%">
  <TR>
   <TD colspan="2">
    <IMG src="http://www.joonte.com/styles/joonte/Images/TopLogo.png" />
   </TD>
  </TR>
  <TR>
   <TD width="200">
    <FORM name="SearchForm">
     <TABLE cellspacing="0" cellpadding="0">
      <TR>
       <TD>
        <INPUT name="FunctionID" type="text" onkeyup="Refresh();" style="width:200px;" />
       </TD>
      </TR>
      <TR height="10">
       <TD />
      </TR>
      <TR>
       <TD>
        <SELECT name="Indexes" size="20" onchange="document.frames.Main.location = value+'.html';" style="width:200px;" />
       </TD>
      </TR>
     </TABLE>
    </FORM>
   </TD>
   <TD>
    <IFRAME name="Main" src="FileList.html" style="width:100%;height:100%;s" />
   </TD>
  </TR>
</TABLE>
EOD;
#-------------------------------------------------------------------------------
$DOM->AddHTML('Into',$Parse);
#-------------------------------------------------------------------------------
$Out = $DOM->Build();
#-------------------------------------------------------------------------------
$IsWrite = IO_Write(SPrintF('%s/Index.html',$JBsDocPath),$Out,TRUE);
if(Is_Error($IsWrite))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$File = IO_Read(SPrintF('%s/styles/root/Css/Standard.css',SYSTEM_PATH));
if(Is_Error($File))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsWrite = IO_Write(SPrintF('%s/Standard.css',$JBsDocPath),$File,TRUE);
if(Is_Error($IsWrite))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------

?>
