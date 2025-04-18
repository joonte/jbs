<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('LinkID');
/******************************************************************************/
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Links = &Links();
# Коллекция ссылок
$Template = &$Links[$LinkID];
/******************************************************************************/
/******************************************************************************/
$CacheID = 'TableSuper[Indexes]';
#-------------------------------------------------------------------------------
if(!$NoBody = Cache_Get($CacheID)){
	#-------------------------------------------------------------------------------
	$Count = $Template['Source']['Count'];
//	Debug(SPrintF('[comp/Tables/Forms/Indexes]: $Count = %s',print_r($Count,true)));
//	Debug(SPrintF('[comp/Tables/Forms/Indexes]: $Template = %s',print_r($Template,true)));
	#-------------------------------------------------------------------------------
	if($Count < 1)
		return FALSE;
	#-------------------------------------------------------------------------------
	$Div = new Tag('DIV',Array('class'=>'TableSuperNavigation'));
	#-------------------------------------------------------------------------------
	$Options = Array();
	#-------------------------------------------------------------------------------
	$Max = ($GLOBALS['__USER']['IsAdmin'])?700:100;
	for($i=5;$i<$Max;$i*=2)
		$Options[$i] = $i;
	#-------------------------------------------------------------------------------
	$InPage = $Template['Query']['InPage'];
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Select',Array('onchange'=>'form.InPage.value=value;TableSuperSetIndex();'),$Options,$InPage);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Div->AddChild(new Tag('NOBODY',new Tag('SPAN','Записей на странице:'),$Comp,new Tag('SPAN',SPrintF('из %u',$Count))));
	#-------------------------------------------------------------------------------
	$Options = Array('Прямой','Обратный');
	#-------------------------------------------------------------------------------
	$IsDesc = $Template['Query']['IsDesc'];
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Form/Select',Array('onchange'=>'form.IsDesc.value=value;TableSuperSetIndex();'),$Options,$IsDesc);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Div->AddChild(new Tag('NOBODY',new Tag('SPAN','| Порядок:'),$Comp));
	#-------------------------------------------------------------------------------
	if($Count <= $InPage)
		return $Div;
	#-------------------------------------------------------------------------------
	$Div->AddChild(new Tag('SPAN',' | '));
	#-------------------------------------------------------------------------------
	$Index = $Template['Query']['Index'];
	#-------------------------------------------------------------------------------
	$Left  = $Index - 3;
	$Right = $Index + 3;
	#-------------------------------------------------------------------------------
	if($Left < 1)
		$Right -= $Left;
	#-------------------------------------------------------------------------------
	if($Right > ($Pages = Ceil($Count/$InPage)))
		$Left -= $Right - $Pages;
	#-------------------------------------------------------------------------------
	// стрелка в лево, костыль на первую страницу
	if($Left > 0)
		$Div->AddChild(new Tag('IMG',Array('class'=>'Button','alt'=>'Прокрутить назад','width'=>12,'height'=>10,'onclick'=>SPrintF('TableSuperSetIndex(%s);',0/*$Index-6*/),'src'=>'SRC:{Images/ArrowLeft.gif}')));
	#-------------------------------------------------------------------------------
	$Left  = Max(0,$Left);
	$Right = Min($Pages,$Right);
	#-------------------------------------------------------------------------------
	// кнопки с номерами страниц
	for($i=$Left;$i<$Right;$i++){
		#-------------------------------------------------------------------------------
		$Button = new Tag('BUTTON',Array('class'=>'TableSuperIndexes','onclick'=>SPrintF('TableSuperSetIndex(%s);',$i)),$i+1);
		#-------------------------------------------------------------------------------
		if($i == $Index)
			$Button->AddAttribs(Array('disabled'=>'true'));
		#-------------------------------------------------------------------------------
		$Div->AddChild($Button);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	// стрелка вправо, костыль на последнюю страницу
	if($Right < $Pages)
		$Div->AddChild(new Tag('IMG',Array('class'=>'Button','alt'=>'Прокрутить вперед','width'=>12,'height'=>10,'onclick'=>SPrintF('TableSuperSetIndex(%s);',$Pages-1/*$Index+6*/),'src'=>'SRC:{Images/ArrowRight.gif}')));
	#-------------------------------------------------------------------------------
	$NoBody = new Tag('NOBODY',$Div);
	#-------------------------------------------------------------------------------
	Cache_Add($CacheID,$NoBody);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $NoBody;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
