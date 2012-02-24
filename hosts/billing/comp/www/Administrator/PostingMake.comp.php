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
$ContractID = (integer) @$Args['ContractID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Contract = DB_Select('Contracts',Array('ID','UserID','Balance'),Array('UNIQ','ID'=>$ContractID));
#-------------------------------------------------------------------------------
switch(ValueOf($Contract)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $DOM = new DOM();
    #---------------------------------------------------------------------------
    $Links = &Links();
    # Коллекция ссылок
    $Links['DOM'] = &$DOM;
    #---------------------------------------------------------------------------
    if(Is_Error($DOM->Load('Window')))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $DOM->AddText('Title','Осуществить финансовую проводку');
    #---------------------------------------------------------------------------
    $Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Administrator/PostingMake.js}'));
    #---------------------------------------------------------------------------
    $DOM->AddChild('Head',$Script);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/Input',
      Array(
        'name'  => 'ContractID',
        'type'  => 'hidden',
        'value' => $Contract['ID']
      )
    );
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Form = new Tag('FORM',Array('name'=>'PostingMakeForm','onsubmit'=>'return false;'),$Comp);
    #---------------------------------------------------------------------------
    $Table = Array();
    #---------------------------------------------------------------------------
    $Services = DB_Select('Services',Array('ID','Name','OperationSign'));
    #---------------------------------------------------------------------------
    switch(ValueOf($Services)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        #-----------------------------------------------------------------------
        $Options = Array();
	#-----------------------------------------------------------------------
	$Script = "var PaySumm = {}; ";
        #-----------------------------------------------------------------------
        foreach($Services as $Service){
          $Options[$Service['ID']] = "[" . $Service['OperationSign'] . "] " . $Service['Name'];
	  if($Service['ID'] == 2000 || $Service['ID'] == 4000 ){
	    $Script = $Script . "PaySumm['" . $Service['ID'] . "'] = '" . $Contract['Balance'] . "'; ";
	  }else{
	    $Script = $Script . "PaySumm['" . $Service['ID'] . "'] = '0.00'; ";
	  }
	}
	#-----------------------------------------------------------------------
	$Script = $Script . ' form.Summ.value = PaySumm[value]; ';
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Form/Select',Array('name'=>'ServiceID','onchange'=>$Script),$Options);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Тип',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/TextArea',
          Array(
            'name'  => 'Comment',
            'style' => 'width:100%;',
            'rows'  => 3
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Комментарий',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Form/Summ');
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Сумма',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'type'    => 'button',
            'onclick' => 'PostingMake();',
            'value'   => 'Осуществить'
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = $Comp;
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Tables/Standard',$Table);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form->AddChild($Comp);
        #-----------------------------------------------------------------------
        $DOM->AddChild('Into',$Form);
        #-----------------------------------------------------------------------
        if(Is_Error($DOM->Build(FALSE)))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        return Array('Status'=>'Ok','DOM'=>$DOM->Object);
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
