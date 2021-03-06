<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Table = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = Array('Текущее время',Date('Y-m-d H:i:s',Time()));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Tmp = System_Element('tmp');
if(Is_Error($Tmp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Marker = SPrintF('%s/TaskLastExecute.txt',$Tmp);
#-------------------------------------------------------------------------------
if(!File_Exists($Marker)){
	#-------------------------------------------------------------------------------
	$Table[] = Array('Маркерный файл','отсутствует');
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Widgets/Administrator/KillCronDaemon]: маркерный файл отсутствует: %s',$Marker));
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Data = IO_Read($Marker);
	if(Is_Error($Data))
		return ERROR | @Trigger_Error('[MARKER_READ_ERROR]: не удалось прочитать файл');
	#-------------------------------------------------------------------------------
        Debug(SPrintF('[comp/Widgets/Administrator/KillCronDaemon]: LastExecuted = %s',Date('Y-m-d H:i:s',StrToTime($Data))));
	#-------------------------------------------------------------------------------
	$Table[] = Array('Последний запуск',Date('Y-m-d H:i:s',StrToTime($Data)));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$BinaryName = SPrintF('%s/CronBinaryName.txt',$Tmp);
#-------------------------------------------------------------------------------
if(!File_Exists($BinaryName)){
	#-------------------------------------------------------------------------------
	$Table[] = Array('Имя бинаника','отсутствует');
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Widgets/Administrator/KillCronDaemon]: не найден файл с указанием имени бинарника: %s',$BinaryName));
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Data = IO_Read($BinaryName);
	if(Is_Error($Data))
		return ERROR | @Trigger_Error('[BinaryName_READ_ERROR]: не удалось прочитать файл');
	#-------------------------------------------------------------------------------
	$Table[] = Array('Команда',$Data);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$TaskNowRunning = SPrintF('%s/TaskNowRunning.txt',$Tmp);
#-------------------------------------------------------------------------------
if(!File_Exists($TaskNowRunning)){
	#-------------------------------------------------------------------------------
	$Table[] = Array('Сейчас выполняется','-');
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Widgets/Administrator/KillCronDaemon]: не найден файл с указанием выполняемого задания: %s',$TaskNowRunning));
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Data = IO_Read($TaskNowRunning);
	if(Is_Error($Data))
		return ERROR | @Trigger_Error('[TaskNowRunning_READ_ERROR]: не удалось прочитать файл');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Task = DB_Select('Tasks',Array('ID','TypeID'),Array('UNIQ','ID'=>IntVal($Data)));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Task)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Task/Type',$Task['TypeID'],200);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Number = Comp_Load('Formats/Task/Number',$Task['ID']);
	if(Is_Error($Number))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Table[] = new Tag('TR',new Tag('TD',Array('class'=>'Comment','valign'=>'bottom'),'Задание'),new Tag('TD',Array('class'=>'Standard'),New Tag('SPAN',Array('onmouseover'=>SPrintF("PromptShow(event,'%s',this);",$Comp)),$Number)));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'type'		=> 'button',
			'onclick'	=> "AjaxCall('/Administrator/API/KillCronDaemon',{IsKill:9},'Убиение планировщика','ShowTick(\"Планировщик убит\");');",
			'value'		=> 'Убить планировщик задач',
			'prompt'	=> 'Жёсткий перезапуск, возможен только если планировщик "завис" как минимум на 10 минут'
			)
		);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
		'Form/Input',
		Array(
			'type'		=> 'button',
			'onclick'	=> "AjaxCall('/Administrator/API/KillCronDaemon',{IsKill:1},'Убиение планировщика','ShowTick(\"Создана задача перезапуска планировщика\");');",
			'value'		=> 'Перезапустить планировщик задач',
			'prompt'	=> 'Мягкий перезапуск, сработает только если не завис',
			)
		);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Title'=>'Состояние планировщика','DOM'=>$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
