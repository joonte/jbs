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
$DSSchemeID = (string) @$Args['DSSchemeID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DSScheme = DB_Select('DSSchemesOwners','*',Array('UNIQ','ID'=>$DSSchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($DSScheme)){
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
    $DOM->AddText('Title','Тариф выделенного сервера сервера');
    #---------------------------------------------------------------------------
    $Table = Array('Общая информация');
    #---------------------------------------------------------------------------
    $Table[] = Array('Название тарифа',$DSScheme['Name']);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Formats/Currency',$DSScheme['CostDay']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Цена 1 дн.',$Comp);
    #---------------------------------------------------------------------------

    $Comp = Comp_Load('Formats/Currency',$DSScheme['CostInstall']);
    if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Стоимость установки/подключения',$Comp);
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    $ServersGroup = DB_Select('DSServersGroups','*',Array('UNIQ','ID'=>$DSScheme['ServersGroupID']));
    if(!Is_Array($ServersGroup))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Группа серверов',$ServersGroup['Name']);
    #---------------------------------------------------------------------------

    # check permission for additional info
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('DSAdditionalInfo',(integer)$__USER['ID'],(integer)$DSScheme['UserID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsPermission)){
    case 'true':
	$Table[] = Array('Всего серверов',$DSScheme['NumServers']);
	#---------------------------------------------------------------------------
	$Table[] = Array('Осталось серверов',$DSScheme['RemainServers']);
	#---------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Logic',$DSScheme['IsCalculateNumServers']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	$Table[] = Array('Автоподсчёт серверов',$Comp);
    }



    $Comp = Comp_Load('Formats/Logic',$DSScheme['IsActive']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Тариф активен',$Comp);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Formats/Logic',$DSScheme['IsProlong']);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Возможность продления',$Comp);
    #---------------------------------------------------------------------------

    $Table[] = Array('Минимальное кол-во дней оплаты',$DSScheme['MinDaysPay']);
    $Table[] = Array('Максимальное кол-во дней оплаты',$DSScheme['MaxDaysPay']);

    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------

    $Table[] = 'Технические характеристики сервера';

    # Load CPUType => CPUName Array
    $CpuArray = Comp_Load('Formats/DSOrder/CPUTypesList');
    if(Is_Error($CpuArray))
	return ERROR | @Trigger_Error(500);
    $Table[] = Array('Тип процессора',$CpuArray[$DSScheme['cputype']]);

    $Table[] = Array('Архитектура процессора',$DSScheme['cpuarch']);

    $Table[] = Array('Число физических процессоров',$DSScheme['numcpu']);

    $Table[] = Array('Число ядер в процессоре',$DSScheme['numcores']);

    $Table[] = Array('Частота процессора, MHz',$DSScheme['cpufreq']);

    $Table[] = Array('Объём оперативной памяти, Mb',$DSScheme['ram']);

    $Table[] = Array('Тип RAID контроллера',$DSScheme['raid']);

    $Table[] = Array('Характеристики 1 жёсткого диска',$DSScheme['disk1']);

    $Table[] = Array('Характеристики 2 жёсткого диска',$DSScheme['disk2']);

    $Table[] = Array('Характеристики 3 жёсткого диска',$DSScheme['disk3']);

    $Table[] = Array('Характеристики 4 жёсткого диска',$DSScheme['disk4']);

    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    $Table[] = 'Прочая информация';
    $Table[] = Array('Скорость канала, мегабит',$DSScheme['chrate']);
    $Table[] = Array('Месячный трафик, Gb',$DSScheme['trafflimit']);
    $Table[] = Array('Соотношения трафика, входящий/исходящий',$DSScheme['traffcorrelation']);
    $Table[] = Array('Предустановленная ОС',$DSScheme['OS']);
    $Table[] = 'Дополнительная информация';
    $Table[] = new Tag('TD',Array('class'=>'Standard','colspan'=>2),$DSScheme['UserComment']);
    # check permission for additional info
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('DSAdditionalInfo',(integer)$__USER['ID'],(integer)$DSScheme['UserID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsPermission)){
    case 'true':
	$Table[] = 'Административный комментарий';
	$Table[] = new Tag('TD',Array('class'=>'Standard','colspan'=>2),$DSScheme['AdminComment']);
    }
    #---------------------------------------------------------------------------
    $SystemsIDs = Array();
    #---------------------------------------------------------------------------
    $DSServers = DB_Select('DSServers','SystemID',Array('Where'=>SPrintF('`ServersGroupID` = %u',$DSScheme['ServersGroupID']),'GroupBy'=>'SystemID'));
    #---------------------------------------------------------------------------
    switch(ValueOf($DSServers)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        # No more...
      break;
      case 'array':
        #-----------------------------------------------------------------------
        foreach($DSServers as $DSServer)
          $SystemsIDs[] = $DSServer['SystemID'];
      break;
      default:
        return ERROR | @Trigger_Error(101);
    }
    #---------------------------------------------------------------------------
    if(In_Array('VdsManager',$SystemsIDs)){
      #-------------------------------------------------------------------------
      $Table[] = 'Ограничения для VDSmanager';
      #-------------------------------------------------------------------------
      $Table[] = Array('Шаблон диска',$DSScheme['disktempl']);
      #-------------------------------------------------------------------------
    }
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Tables/Standard',$Table);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $DOM->AddChild('Into',$Comp);
    #---------------------------------------------------------------------------
    if(Is_Error($DOM->Build(FALSE)))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    return Array('Status'=>'Ok','DOM'=>$DOM->Object);
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
