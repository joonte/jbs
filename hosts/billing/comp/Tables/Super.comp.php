<?php

/**
 * Creates tables.
 *
 * @author vvelikodny
 */
$__args_list = Array('TemplateID','Addition');

Eval(COMP_INIT);

$Chain = Array();

$PatternID = $TemplateID;

do {
    $Pattern = System_XML(SPrintF('tables/%s.xml',$PatternID));

    if(Is_Error($Pattern)) {
        return ERROR | @Trigger_Error(500);
    }

    Array_UnShift($Chain,$Pattern);

    $PatternID = (IsSet($Pattern['RootID'])?$Pattern['RootID']:FALSE);
} while ($PatternID);

if(!Is_Null($Addition)) {
    $Chain[] = $Addition;
}

$Template = System_XML('tables/Common.xml');

if(Is_Error($Template)) {
    return ERROR | @Trigger_Error(500);
}

foreach ($Chain as $Pattern) {
    if(IsSet($Pattern['Cut'])) {
        Array_Cut($Template, $Pattern['Cut']);
    }

    Array_Union($Template, $Pattern);
}

$Columns = &$Template['Columns'];

foreach (Array_Keys($Columns) as $ColumnID) {
    $Column = $Template['Column'];

    $Add = $Column['Add'];

    if (IsSet($Column['Cut'])) {
        Array_Cut($Add, $Column['Cut']);
    }

    Array_Union($Add, $Columns[$ColumnID]);

    $Columns[$ColumnID] = $Add;
}

$Query = &$Template['Query'];

$Args = &Args();

$IsFlush = (IsSet($Args['IsFlush']) && (integer)$Args['IsFlush']);

if($IsFlush) {
    $Args = Array();
}

$Session = new Session(IsSet($_COOKIE['SessionID'])?(string)$_COOKIE['SessionID']:UniqID());

$IsSession = $Session->Load();

if (!Is_Error($IsSession) && $Template['IsSession']) {
    $KeyID = Md5($TemplateID);

    if (IsSet($Session->Data[$KeyID]) && !$IsFlush) {
        $Data = $Session->Data[$KeyID];

        if (IsSet($Data['Index'])) {
            $Query['Index'] = $Data['Index'];
        }

        if (IsSet($Data['IsDesc'])) {
            $Query['IsDesc'] = $Data['IsDesc'];
        }

        if (IsSet($Data['SortOn'])) {
            $Query['SortOn'] = $Data['SortOn'];
        }

        if (IsSet($Data['GroupBy'])) {
            $Query['GroupBy'] = $Data['GroupBy'];
        }

        if (IsSet($Data['InPage'])) {
            $Query['InPage'] = $Data['InPage'];
        }
    }
    else {
        $Session->Data[$KeyID] = Array();
    }

    $Template['Session'] = &$Session->Data[$KeyID];
}
else {
    $Template['Session'] = Array();
}

if (IsSet($Args['Index'])) {
    $Query['Index'] = $Args['Index'];
}

if (IsSet($Args['IsDesc'])) {
    $Query['IsDesc'] = $Args['IsDesc'];
}

if (IsSet($Args['SortOn'])) {
    $Query['SortOn'] = $Args['SortOn'];
}

if (IsSet($Args['GroupBy'])) {
    $Query['GroupBy'] = $Args['GroupBy'];
}

if (IsSet($Args['InPage'])) {
    $Query['InPage'] = $Args['InPage'];
}

$Source = &$Template['Source'];

//$Form = new Tag('FORM',Array('id'=>'TableSuperForm','name'=>'TableSuperForm','method'=>'POST','onsubmit'=>'return false;'),new Tag('INPUT',Array('type'=>'submit','style'=>'display:none;')));
$Form = new Tag('FORM',Array('id'=>'TableSuperForm','name'=>'TableSuperForm','method'=>'POST','onsubmit'=>'return false;'),new Tag('DIV',Array('style'=>'display:none;')));

$Links = Links();

$DOM = &$Links['DOM'];

if (!Comp_IsLoaded('Tables/Super')) {
    $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/TableSuper.js}')));
}

$Appearance = $Template['Appearance'];

$Comp = Comp_Load('Css',$Appearance['Css']);

if (Is_Error($Comp)) {
    return ERROR | @Trigger_Error(500);
}

foreach ($Comp as $Css) {
    $DOM->AddChild('Head', $Css);
}
#-------------------------------------------------------------------------------
$Before = $Template['Comps']['Before'];
#-------------------------------------------------------------------------------
$LinkID = UniqID('Super');
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links[$LinkID] = &$Template;
#-------------------------------------------------------------------------------
if(Count($Before)){
  #-----------------------------------------------------------------------------
  $Table = new Tag('TABLE',Array('cellspacing'=>0,'cellpadding'=>0,'width'=>'100%','role'=>'table'));
  #-----------------------------------------------------------------------------
  $Tr = new Tag('TR',Array('role'=>'row'));
  #-----------------------------------------------------------------------------
  foreach($Before as $Before){
    #---------------------------------------------------------------------------
    $Params = Array($Before['Comp'],$LinkID);
    #---------------------------------------------------------------------------
    if(Count($Before['Args']))
      $Params = Array_Merge($Params,$Before['Args']);
    #---------------------------------------------------------------------------
    $Comp = Call_User_Func_Array('Comp_Load',Array_Values($Params));
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if(Is_Object($Comp)){
      #-------------------------------------------------------------------------
      if(IsSet($Before['NoBlock'])){
        #-----------------------------------------------------------------------
        if(Count($Tr->Childs))
          $Table->AddChild(new Tag('TR',Array('role'=>'row'),new Tag('TD',new Tag('TABLE',Array('cellspacing'=>0,'role'=>'table'),$Tr))));
        #-----------------------------------------------------------------------
        $Table->AddChild(new Tag('TR',Array('role'=>'row'),new Tag('TD',$Comp)));
        #-----------------------------------------------------------------------
        $Tr = new Tag('TR');
      }else
        $Tr->AddChild(new Tag('TD',$Comp));
    }
  }
  #-----------------------------------------------------------------------------
  if(Count($Tr->Childs))
    $Table->AddChild(new Tag('TR',new Tag('TD',new Tag('TABLE',Array('cellspacing'=>0),$Tr))));
  #-----------------------------------------------------------------------------
  if(Count($Table->Childs))
    $Form->AddChild($Table);
}
#-------------------------------------------------------------------------------
/** Get data. */
$Comp = Comp_Load($Template['Provider'],$LinkID);

if(Is_Error($Comp)) {
  return ERROR | @Trigger_Error(500);
}

$After = $Template['Comps']['After'];
#-------------------------------------------------------------------------------
if(Count($After)){
  #-----------------------------------------------------------------------------
  $Table = new Tag('TABLE',Array('cellspacing'=>0,'cellpadding'=>0,'width'=>'100%'));
  #-----------------------------------------------------------------------------
  $Tr = new Tag('TR');
  #-----------------------------------------------------------------------------
  foreach($After as $After){
    #---------------------------------------------------------------------------
    $Params = Array($After['Comp'],$LinkID);
    #---------------------------------------------------------------------------
    if(Count($After['Args']))
      $Params = Array_Merge($Params,$After['Args']);
    #---------------------------------------------------------------------------
    $Comp = Call_User_Func_Array('Comp_Load',Array_Values($Params));
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if(Is_Object($Comp)){
      #-------------------------------------------------------------------------
      if(IsSet($After['NoBlock'])){
        #-----------------------------------------------------------------------
        if(Count($Tr->Childs))
          $Table->AddChild(new Tag('TR',new Tag('TD',new Tag('TABLE',Array('cellspacing'=>0),$Tr))));
        #-----------------------------------------------------------------------
        $Table->AddChild(new Tag('TR',new Tag('TD',$Comp)));
        #-----------------------------------------------------------------------
        $Tr = new Tag('TR');
      }else
        $Tr->AddChild(new Tag('TD',$Comp));
    }
  }
  #-----------------------------------------------------------------------------
  if(Count($Tr->Childs))
    $Table->AddChild(new Tag('TR',new Tag('TD',new Tag('TABLE',Array('cellspacing'=>0),$Tr))));
  #-----------------------------------------------------------------------------
  if(Count($Table->Childs))
    $Form->AddChild($Table);
}
#-------------------------------------------------------------------------------
if(!Is_Error($IsSession) && $Template['IsSession']){
  #-----------------------------------------------------------------------------
  $Data = &$Template['Session'];
  #-----------------------------------------------------------------------------
  $Data['Index']  = $Query['Index'];
  $Data['IsDesc'] = $Query['IsDesc'];
  $Data['SortOn'] = $Query['SortOn'];
  $Data['InPage'] = $Query['InPage'];

    if (isSet($Query['GroupBy'])) {
        $Data['GroupBy'] = $Query['GroupBy'];
    }

  #-----------------------------------------------------------------------------
  $Session->Data[Md5($TemplateID)] = $Data;
  #-----------------------------------------------------------------------------
  if(Is_Error($Session->Save()))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
UnSet($Links[$LinkID]);
#-------------------------------------------------------------------------------
if(!Count($Form->Childs))
  return new Tag('NOBODY','No output');
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'  => 'Index',
    'type'  => 'hidden',
    'value' => $Query['Index']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'  => 'IsDesc',
    'type'  => 'hidden',
    'value' => $Query['IsDesc']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'  => 'SortOn',
    'type'  => 'hidden',
    'value' => $Query['SortOn']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'  => 'InPage',
    'type'  => 'hidden',
    'value' => $Query['InPage']
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'  => 'IsFlush',
    'type'  => 'hidden',
    'value' => '0'
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
$Form->AddAttribs(Array('count'=>$Template['Source']['Count']));
#-------------------------------------------------------------------------------
return $Form;
#-------------------------------------------------------------------------------

?>
