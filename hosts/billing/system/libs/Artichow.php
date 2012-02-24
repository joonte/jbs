<?php
#-------------------------------------------------------------------------------
/** @author Лапшин С.М. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/Color.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
foreach(Array('BarPlot','LinePlot','Pie') as $Class){
 #-----------------------------------------------------------------------------
  $IsLoad = Load(SPrintF('%s/others/billing/artichow/%s.class.php',SYSTEM_PATH,$Class));
  if(Is_Error($IsLoad))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
/**
* Формирование гистограммы.
*
* Функция формирует, а затем записывает в файл ($File) изображение гистограммы по
* переданным пользователем данным($Lines). Первым элементом массива $Lines подпись к
* легенде для данного столбца гистограммы с ключем 'Name'.
*
* @param string <заголовок гистограммы>
* @param string <полный путь с именем файла-результата>
* @param array  <исходные данные>
* @param array  <подписи к оси Ox>
* @param array  <цвета столбцов>
*/
#-------------------------------------------------------------------------------
function Artichow_Histogram($Name,$File,$Lines,$Labels,$Colors){
  #-----------------------------------------------------------------------------
  $Graph = new Graph(800,500);
  $Graph->setDriver('gd');
  $Graph->setAntiAliasing(TRUE);
  $Graph->title->set($Name);
  $Graph->title->move(0,-5);
  $Graph->border->hide();
  $Graph->setBackgroundGradient(new LinearGradient(new Color(240,240,240,0),new White,0));
  #-----------------------------------------------------------------------------
  $Group = new PlotGroup;
  $Group->grid->hide(FALSE);
  $Group->setBackgroundColor(new Color(240,240,240));
  $Group->setSpace(2,2,20,0);
  $Group->setPadding(60,10,NULL,NULL);
  #-----------------------------------------------------------------------------
  foreach($Lines as $LineID=>$Value){
    #---------------------------------------------------------------------------
    $Legend = $Value['Name'];
    UnSet($Value['Name']);
    #---------------------------------------------------------------------------
    $Plot = new BarPlot($Value,1,1,0);
    $Plot->barShadow->setPosition(Shadow::RIGHT_TOP);
    $Plot->barShadow->setColor(new Color(160, 160, 160, 10));
    $Color = Color_RGB_Explode($Colors[$LineID]);
    $Plot->setBarColor(new Color($Color['R'],$Color['G'],$Color['B'],56));
    #---------------------------------------------------------------------------
    $Group->add($Plot);
    $Group->legend->add($Plot,$Legend,Legend::BACKGROUND);
  }
  #-----------------------------------------------------------------------------
  $Group->axis->bottom->setLabelText($Labels);
  $Group->axis->bottom->hideTicks(TRUE);
  #-----------------------------------------------------------------------------
  $Group->legend->shadow->setSize(4);
  $Group->legend->setAlign(Legend::BOTTOM);
  $Group->legend->setSpace(5);
  $Group->legend->setTextFont(new Tuffy(8));
  $Group->legend->setPosition(0.30,0.20);
  $Group->setPadding(50,10,50,30);
  $Group->legend->setBackgroundColor(new Color(255,255,255,25));
  #-----------------------------------------------------------------------------
  $Graph->add($Group);
  #-----------------------------------------------------------------------------
  $Graph->draw($File);
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
/**
* Формирование диаграммы.
*
* Функция формирует, а затем записывает в файл ($File) изображение диаграммы по
* переданным пользователем данным($Lines).
*
* @param string <заголовок диаграммы>
* @param string <полный путь с именем файла-результата>
* @param array  <исходные данные>
* @param array  <легенда диаграммы>
*/
function Artichow_Pie($Name,$File,$Lines,$Labels){
  #-----------------------------------------------------------------------------
  $Graph = new Graph(500,400);
  $Graph->setDriver('gd');
  $Graph->setAntiAliasing(TRUE);
  $Graph->setBackgroundGradient(new LinearGradient(new Color(240,240,240,0),new White,0));
  #-----------------------------------------------------------------------------
  $Graph->title->set($Name);
  $Graph->title->setFont(new Tuffy(15));
  #-----------------------------------------------------------------------------
  $Pie = new Pie($Lines,Array(new LightOrange,new LightPurple,new LightBlue,new LightRed,new LightPink,new VeryDarkGreen,new MidBlue,new VeryDarkCyan,new Cyan,new DarkOrange,new VeryLightOrange));
  $Pie->setCenter(0.3,0.5);
  $Pie->setAbsSize(300,300);
  $Pie->setPadding(40,40,40,40);
  $Pie->setLegend($Labels);
  #-----------------------------------------------------------------------------
  $Pie->legend->setTextFont(new Tuffy(7));
  $Pie->legend->setPosition(1.6,0.5);
  #-----------------------------------------------------------------------------
  $Pie->title->move(0,-40);
  $Pie->title->setFont(new Tuffy(14));
  $Pie->title->setBackgroundColor(new White(50));
  $Pie->title->setPadding(5,5,2,2);
  $Pie->title->border->setColor(new Black());
  #-----------------------------------------------------------------------------
  $Graph->add($Pie);
  #-----------------------------------------------------------------------------
  $Graph->draw($File);
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
/**
* Формирование графика.
*
* Функция формирует, а затем записывает в файл ($File) изображение графика по
* переданным пользователем данным($Lines). Данная функция может рисовать как одиночные,
* так и многолинейные графики.Исходными данными является массив, элементы которого также
* является массивами исходных данных для соответствующих графиков (данная структура
* сохраняется и для одиночных графиков!). Цвет каждой линии передается в массиве
* $Colors, ключи которого совпадают с ключами массива исходных данных. Цвет задается
* шестнадцатиричным кодом цвета (например, 0x000000 для черного цвета).
*
* @param string  <заголовок диаграммы>
* @param string  <полный путь с именем файла-результата>
* @param array   <исходные данные>
* @param array   <подписи к оси Ox>
* @param array   <цвета линий>
*/
function Artichow_Line($Name,$File,$Lines,$Labels,$Colors){
  #-----------------------------------------------------------------------------
  $Graph = new Graph(1000,300);
  $Graph->setDriver('gd');
  $Graph->setAntiAliasing(TRUE);
  #-----------------------------------------------------------------------------
  $Graph->title->set($Name);
  $Graph->title->setFont(new Tuffy(15));
  $Graph->title->move(0,-5);
  #-----------------------------------------------------------------------------
  if(Count($Lines) > 1){
    #---------------------------------------------------------------------------
    $Group = new PlotGroup;
    $Group->setPadding(40,40);
    $Group->setBackgroundColor(new Color(240,240,240));
  }
  #-----------------------------------------------------------------------------
  $IsSetLabel = FALSE;
  #-----------------------------------------------------------------------------
  foreach($Lines as $LineID=>$Line){
    #---------------------------------------------------------------------------
    $Plot = new LinePlot($Line);
    $Color = Color_RGB_Explode($Colors[$LineID]);
    $Plot->setColor(new Color($Color['R'],$Color['G'],$Color['B']));
    $Plot->setThickness(1);
    $Plot->setBackgroundGradient(new LinearGradient(new Color(240,240,240),new Color(255,255,255),0));
    $Plot->setFillGradient(new LinearGradient(new LightOrange(10),new VeryLightOrange(90),90));
    $Plot->setPadding(50,50,50,50);
    #---------------------------------------------------------------------------
    $Plot->mark->setType(Mark::CIRCLE);
    $Plot->mark->setSize(5);
    #---------------------------------------------------------------------------
    $Plot->label->set($Line);
    $Plot->label->move(0,-15);
    $Plot->label->setBackgroundGradient(new LinearGradient(new Color(250,250,250,10),new Color(255,200,200,30),0));
    $Plot->label->border->setColor(new Color(20, 20, 20, 20));
    $Plot->label->setPadding(2,2,2,2);
    #---------------------------------------------------------------------------
    if(Count($Lines) < 2){
      #-------------------------------------------------------------------------
      $Plot->xAxis->setLabelText($Labels);
      $Plot->yAxis->setLabelPrecision(1);
    }else{
      #-------------------------------------------------------------------------
      if(!$IsSetLabel){
        #-----------------------------------------------------------------------
        $Plot->setXAxis(Plot::BOTTOM);
        $IsSetLabel = TRUE;
      }
    }
    #---------------------------------------------------------------------------
    if(Count($Lines) > 1)
      $Group->add($Plot);
    #---------------------------------------------------------------------------
    if(Count($Lines) > 1){
      #-------------------------------------------------------------------------
      $Graph->add($Group);
      #-------------------------------------------------------------------------
      $Group->axis->bottom->setTickStyle(0);
      $Group->axis->bottom->setLabelText($Labels);
    }else
      $Graph->add($Plot);
  }
  #-----------------------------------------------------------------------------
  $Graph->draw($File);
  #-----------------------------------------------------------------------------
  return TRUE;
}
#-------------------------------------------------------------------------------
?>
