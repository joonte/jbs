<?php
/**
 * GUI script for install Joonte Billing.
 *
 * @author vvelikodny
 */

/** Enable error reporting. */
Error_Reporting(E_ALL);

$__MESSAGES = Array();

function Message($Message) {
    global $__MESSAGES;

    $__MESSAGES[]= $Message;
}

$__ERRORS = Array();

function Error($Error) {
    global $__ERRORS;

    $__ERRORS[]= $Error;
}

/** Handle error messages. */
function __Error_Handler__($Number, $Error, $File, $Line) {
    Error(SPrintF('%s в линии %u', $Error, $Line));
}

/* added by lissyara 2012-01-28 in 12:43 MSK, for JBS-303 */
if(In_Array('exec',Explode(',',StrToLower(Ini_Get("disable_functions"))))){
	echo "'exec' function is disabled, cannot continue installtion";
	exit;
}

/* added by lissyara, 2012-01-01 in 19:20 MSK, for JBS-241 */
$Result = Exec('whereis -b mysql');
$Result = Explode(" ",$Result);
if($Result[1]){
	$MySQLbin = $Result[1];
	#echo $MySQLbin;
}else{
	if(File_Exists('/usr/local/bin/mysql')){
		$MySQLbin = '/usr/local/bin/mysql';
	}elseif(File_Exists('/usr/bin/mysql')){
		$MySQLbin = '/usr/bin/mysql';
	}else{
		echo "mysql not found using PATH, or /usr/local/bin/mysql, or /usr/bin/mysql";
		exit;
	}
}

Set_Error_Handler('__Error_Handler__');

Define('PHP_INI_PATH','php.ini');

$HostID = StrToLower(@$_SERVER['HTTP_HOST']);

if (Preg_Match('/^www\.(.+)$/', $HostID, $Mathces)) {
    $HostID = Next($Mathces);
}

if (Preg_Match('/^(.+)\:[0-9]+$/', $HostID, $Mathces)) {
    $HostID = Next($Mathces);
}

Define('HOST_ID', $HostID);
Define('SYSTEM_PATH', DirName(DirName(__FILE__)));
Define('SETTINGS_FILE',SPrintF('%s/install.settings',SYSTEM_PATH));

$__SETTINGS = Array(
  'db-server'   => 'localhost',
  'db-port'     => '3306',
  'db-type'     => 'exists',
  'db-root'     => '',
  'db-user'     => 'jbs',
  'db-password' => 'password',
  'db-name'     => 'jbs'
);

if (!IsSet($_GET['flush']) && File_Exists(SETTINGS_FILE)) {
    $__SETTINGS = @File_Get_Contents(SETTINGS_FILE);
    
    if (!$__SETTINGS) {
        Error(SPrintF('Не удалось прочитать файл конфигурации (%s)', SETTINGS_FILE));
    }

    $__SETTINGS = JSON_Decode($__SETTINGS, TRUE);
}

$__STEP_ID = IsSet($_POST['step-id']) ? Max(0, $_POST['step-id']) : 0;

echo <<<EOD
<HTML>
 <HEAD>
  <TITLE>Установка биллинговой системы</TITLE>
  <META http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <LINK href="/styles/root/Css/Standard.css" rel="stylesheet" type="text/css" />
  <STYLE>body {margin:10px;}</STYLE>
 </HEAD>
 <BODY>
  <TABLE class="Standard" cellspacing="5" cellpadding="0" style="max-width:600px;">
   <CAPTION>Установка биллинговой системы Joonte Billing System 2007-##CURR_YEAR##</CAPTION>
EOD;

function __ShutDown_Function__() {
    global $__SETTINGS, $__MESSAGES, $__ERRORS;

    echo '</TABLE>';

    if (Count($__MESSAGES)) {
        echo '<H2>Сообщения:</H2><UL class="Standard">';

        foreach($__MESSAGES as $__MESSAGE) {
            echo SPrintF('<LI>%s</LI>',$__MESSAGE);
        }

        echo '</UL>';
    }

    if (Count($__ERRORS)) {
        echo '<H2>Ошибки выполнения:</H2><UL class="Standard">';

        foreach($__ERRORS as $__ERROR) {
            echo SPrintF('<LI><PRE>%s</PRE></LI>',$__ERROR);
        }
        echo '</UL>';
    }

    if (!@File_Put_Contents(SETTINGS_FILE, JSON_Encode($__SETTINGS))) {
        echo SPrintF('<P>Не удалось сохранить текущие настройки установки в файле (%s)</P>', SETTINGS_FILE);
    }

echo <<<EOD
 </BODY>
</HTML>
EOD;
}

Register_ShutDown_Function('__ShutDown_Function__');

foreach (Array('db-server', 'db-port', 'db-type', 'db-root', 'db-user', 'db-password', 'db-name') as $ArgID) {
    if(IsSet($_POST[$ArgID]) && $_POST[$ArgID]) {
        $__SETTINGS[$ArgID] = $_POST[$ArgID];
    }
}

if ($__STEP_ID == 4) {
    switch ($__SETTINGS['db-type']) {
        case 'exists': {
            $MySQL = @MySQL_Connect(SPrintF('%s:%u', $__SETTINGS['db-server'], $__SETTINGS['db-port']),
                $__SETTINGS['db-user'], $__SETTINGS['db-password']);

            if ($MySQL) {
                $Result = @MySQL_Query($Query = SPrintF('use `%s`', $__SETTINGS['db-name']), $MySQL);

                if ($Result) {
                    Message('Настройки соединения успешно проверены.');

                    /* added by lissyara for JBS-230 */
                    $Result = @MySQL_Query('SHOW ENGINES');
                    while ($Engine = MySQL_Fetch_Assoc($Result)){
                      #-----------------------------------------------------------------------------
                      if($Engine['Engine'] == 'InnoDB'){
                        #---------------------------------------------------------------------------
                        if($Engine['Support'] != 'YES' && $Engine['Support'] != 'DEFAULT'){
                          #------------------------------------------------------------------------
                          Error('MySQL собран без поддержки InnoDB, или возможность использования InnoDB в MySQL отключена.');
			  Error('Пожалуйста, исправьте возникшую проблему, т.к. биллинговая система не может использовать транзации и поддержку ссылочной целостности, что может привести к потерям данных.');
                        }
                      }
                    }

                }else{
		  Error('Не удалось выбрать базу данных.');
                }

            }else {
                Error(SPrintF('Не удалось соединиться с сервером баз данных (%s)',MySQL_Error()));
            }

            break;
        }
        case 'create': {
            $MySQL = @MySQL_Connect(SPrintF('%s:%u',$__SETTINGS['db-server'],$__SETTINGS['db-port']),'root',$__SETTINGS['db-root']);

            if ($MySQL) {
                $Query = SPrintF("CREATE USER '%s' IDENTIFIED BY '%s';",$__SETTINGS['db-user'],$__SETTINGS['db-password']);

                $Result = @MySQL_Query($Query,$MySQL);
                if ($Result) {
                    $Query = SPrintF("CREATE DATABASE `%s`;",$__SETTINGS['db-name']);

                    $Result = @MySQL_Query($Query,$MySQL);
                    if ($Result) {
                        $Query = SPrintF("GRANT ALL ON `%s`.* TO '%s'@'%%';",$__SETTINGS['db-name'],$__SETTINGS['db-user']);

                        $Result = @MySQL_Query($Query,$MySQL);
                        if ($Result) {
                            Message('Пользователь и база данных успешно созданы');
                        }
                        else {
                            Error(SPrintF('Не удалось выполнить запрос (%s) (%s)',$Query,MySQL_Error($MySQL)));
                        }
                    }
                    else {
                        Error(SPrintF('Не удалось выполнить запрос (%s) (%s)',$Query,MySQL_Error($MySQL)));
                    }
                }
                else {
                    Error(SPrintF('Не удалось выполнить запрос (%s) (%s)',$Query,MySQL_Error($MySQL)));
                }
            }
            else {
                Error(SPrintF('Не удалось соединиться с сервером баз данных от имени пользователя root (%s)',MySQL_Error()));
            }

            break;
        }
        default: {
            Error('Не удалось определить тип установки базы данных');
        }
    }

    if(!Count($__ERRORS)) {
        $Folder = SPrintF('%s/hosts/%s/config',SYSTEM_PATH,HOST_ID);

        if (!File_Exists($Folder)) {
            if (!@MkDir($Folder,0755,TRUE)) {
                Error(SPrintF('Не возможно создать директорию (%s)',$Folder));
            }
        }

        $File = SPrintF('%s/Config.xml',$Folder);

$Data = <<<EOD
<XML>
 <DBConnection>
  <User>%s</User>
  <Password>%s</Password>
  <DbName>%s</DbName>
  <Server>localhost</Server>
  <Port>%s</Port>
 </DBConnection>
</XML>
EOD;
        if (File_Put_Contents($File,SPrintF($Data, $__SETTINGS['db-user'], $__SETTINGS['db-password'],
                $__SETTINGS['db-name'],$__SETTINGS['db-port']))) {
          Message('Настройки конфигурации успешно сохранены');

          $__STEP_ID = 5;
        }
        else {
            Error(SPrintF('Не возможно создать файл конфигурации (%s)', $File));
        }
    }
}
#-------------------------------------------------------------------------------
if($__STEP_ID == 6){
  #-----------------------------------------------------------------------------
  if($__SETTINGS['db-root']){
    #---------------------------------------------------------------------------
    $MySQL = @MySQL_Connect(SPrintF('%s:%u',$__SETTINGS['db-server'],$__SETTINGS['db-port']),'root',$__SETTINGS['db-root']);
    if($MySQL){
      #-------------------------------------------------------------------------
      $Query = SPrintF("UPDATE `mysql`.`user` SET `Super_priv` = 'Y' WHERE `user` = '%s';",$__SETTINGS['db-user']);
      #-------------------------------------------------------------------------
      $Result = @MySQL_Query($Query,$MySQL);
      if($Result){
        #-----------------------------------------------------------------------
        $Query = SPrintF("GRANT ALL ON `%s`.* TO '%s'@'%%';",$__SETTINGS['db-user'],$__SETTINGS['db-name']);
        #-----------------------------------------------------------------------
        $Result = @MySQL_Query($Query,$MySQL);
        if($Result){
          #---------------------------------------------------------------------
          $Query = 'flush privileges;';
          #---------------------------------------------------------------------
          $Result = @MySQL_Query($Query,$MySQL);
          if($Result){
            #-------------------------------------------------------------------
            Message('Права для пользователя успешно установлены');
            #-------------------------------------------------------------------
            $__STEP_ID = 8;
          }
          else
            Error(SPrintF('Не удалось выполнить запрос (%s) (%s)',$Query,MySQL_Error($MySQL)));
        }else
          Error(SPrintF('Не удалось выполнить запрос (%s) (%s)',$Query,MySQL_Error($MySQL)));
      }else
        Error(SPrintF('Не удалось выполнить запрос (%s) (%s)',$Query,MySQL_Error($MySQL)));
    }else
      Error(SPrintF('Не удалось соединиться с сервером баз данных от имени пользователя root (%s)',MySQL_Error()));
  }else
    $__STEP_ID = 7;
}
#-------------------------------------------------------------------------------
if($__STEP_ID == 8){
  #-----------------------------------------------------------------------------
  $File = SPrintF('%s/HostsIDs.txt',SYSTEM_PATH);
  #-----------------------------------------------------------------------------
  $HostsIDs = @File_Get_Contents($File);
  if($HostsIDs){
    #---------------------------------------------------------------------------
    $HostsIDs = Explode(',',$HostsIDs);
    #---------------------------------------------------------------------------
    foreach(Array_Reverse($HostsIDs) as $HostID){
      #-------------------------------------------------------------------------
      $HostID = Trim($HostID);
      #-------------------------------------------------------------------------
      foreach(Array('structure','views','permissions','triggers','functions','db') as $File){
        #-----------------------------------------------------------------------
        $Path = SPrintF('%s/db/%s/%s.sql',SYSTEM_PATH,$HostID,$File);
        #-----------------------------------------------------------------------
        if(File_Exists($Path)){
          #---------------------------------------------------------------------
	  Message("Импортируется: " . $Path);
          $MySQL = SPrintF('%s -u %s --password=%s --host=%s --port=%u %s < %s 2>&1',$MySQLbin,$__SETTINGS['db-user'],$__SETTINGS['db-password'],$__SETTINGS['db-server'],$__SETTINGS['db-port'],$__SETTINGS['db-name'],$Path);
          #---------------------------------------------------------------------
          $Result = Exec($MySQL,$Log);
          if($Result)
            #Error(SPrintF("Ошибка установки базы данных:\n%s",Implode("\n",$Log)));
	    Error(SPrintF("Ошибка установки базы данных, файл: %s\n Сообщение:\n%s",$Path,Implode("\n",$Log)));
        }
      }
    }
    #---------------------------------------------------------------------------
    if(!Count($__ERRORS)){
      #-------------------------------------------------------------------------
      Message('База данных успешно установлена');
      #-------------------------------------------------------------------------
      $__STEP_ID = 10;
    }
  }else
    Error(SPrintF('Ошибка загрузки файла (%s)',$File));
}
#-------------------------------------------------------------------------------
if($__STEP_ID == 10){
  #-----------------------------------------------------------------------------
  $Folder = SPrintF('%s/hosts/%s/tmp',SYSTEM_PATH,HOST_ID);
  #-----------------------------------------------------------------------------
  if(!File_Exists($Folder)){
    #---------------------------------------------------------------------------
    if(!@MkDir($Folder,0755,TRUE))
      Error(SPrintF('Не возможно создать директорию (%s)',$Folder));
  }
  #-----------------------------------------------------------------------------
  if(!Count($__ERRORS)){
    #---------------------------------------------------------------------------
    $File = SPrintF('%s/hosts/%s/host.ini',SYSTEM_PATH,HOST_ID);
    #---------------------------------------------------------------------------
    if(!File_Exists($File)){
      #-------------------------------------------------------------------------
      Array_UnShift($HostsIDs,HOST_ID);
      #-------------------------------------------------------------------------
      if(!File_Put_Contents($File,SPrintF("HostsIDs=%s\nmemcached.port=11211",Implode(',',$HostsIDs))))
        Error(SPrintF('Ошибка записи файла (%s)',$File));
    }
    #---------------------------------------------------------------------------
    $Link = SPrintF('%s/hosts/%s/tmp/public',SYSTEM_PATH,HOST_ID);
    #---------------------------------------------------------------------------
    if(!File_Exists($Link)){
      #-------------------------------------------------------------------------
      if(!@MkDir($Link,0755,TRUE)) {
        Error(SPrintF('Не возможно создать директорию (%s)',$Link));
      }
      #-------------------------------------------------------------------------
      if(!@SymLink(SPrintF('./hosts/%s/tmp/public',HOST_ID),'./public'))
        Error(SPrintF('Не возможно создать символическую ссылку (%s)',$Link));
    }
    #---------------------------------------------------------------------------
    if(File_Exists(SETTINGS_FILE)){
      #-------------------------------------------------------------------------
      if(!@UnLink(SETTINGS_FILE))
        Error(SPrintF('Не возможно удалить файл (%s)',SETTINGS_FILE));
    }
    #---------------------------------------------------------------------------
    if(!Count($__ERRORS)){
      #-------------------------------------------------------------------------
      $File = SPrintF('%s/INSTALL',SYSTEM_PATH);
      #-------------------------------------------------------------------------
      if(File_Exists($File)){
        #-----------------------------------------------------------------------
        if(!@UnLink($File))
          Error(SPrintF('Не возможно удалить файл (%s)',$File));
      }
    }
    #---------------------------------------------------------------------------
    if(!Count($__ERRORS)){
      #-------------------------------------------------------------------------
      //SetCookie('Email','admin@company.com',Time() + 31536000,'/');
      #-------------------------------------------------------------------------
      Message('Завершение по установке выполнено');
      #-------------------------------------------------------------------------
      $__STEP_ID = 11;
    }
  }
}

if (Count($__ERRORS)) {
    $__STEP_ID--;
}
/**
 * Step 0
 */
if($__STEP_ID == 0){
#-------------------------------------------------------------------------------
echo <<<EOD
<FORM method="POST">
 <TR>
  <TD class="Separator" colspan="2">Регламент на использование программного продукта</TD>
 </TR>
 <TR>
  <TD>
   <IFRAME width="700" src="http://joonte.com/JBsRules?TemplateID=Standard" height="300">Ваш броузер не поддерживает iframe</IFRAME>
  </TD>
 </TR>
 <TR>
  <TD align="right">
   <INPUT type="checkbox" onclick="document.getElementById('Continue').disabled=!this.checked;" />Я согласен
   <INPUT id="Continue" type="submit" value="Продолжить" disabled="true" />
  </TD>
 </TR>
 <INPUT type="hidden" name="step-id" value="1" />
</FORM>
EOD;
#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
if($__STEP_ID == 1){
  #-----------------------------------------------------------------------------
  $Tests = Array('Проверка окружения');
  #-----------------------------------------------------------------------------
  $PhpVersion = PhpVersion();
  #-----------------------------------------------------------------------------
  $Tests[] = Array('Name'=>'Версия PHP интерпретатора (phpversion)','Status'=>$PhpVersion,'IsOk'=>($PhpVersion >= 5),'Comment'=>'Ваша версия PHP не совместима с биллинговой системой (требуется PHP >= 5), пожалуйста, установите нужную версию PHP.');
  #-----------------------------------------------------------------------------
  $safe_mode = (boolean)Ini_Get('safe_mode');
  #-----------------------------------------------------------------------------
  $Tests[] = Array('Name'=>'Безопасный режим PHP (safe_mode)','Status'=>($safe_mode?'Включен':'Выключен'),'IsOk'=>!$safe_mode,'Comment'=>SPrintF('Необходимо выключить безопасный режим в PHP, т.к. это существенно ограничивает возможности PHP интерпритатора. Найдите в файле %s опцию <U>safe_mode</U> и установите ее значение в 0.',PHP_INI_PATH));
  #-----------------------------------------------------------------------------
  $disable_functions = Ini_Get('disable_functions');
  #-----------------------------------------------------------------------------
  $Tests[] = Array('Name'=>'Запрещенные функции (disable_functions)','Status'=>($disable_functions?'Включены':'Выключены'),'IsOk'=>!$disable_functions,'Comment'=>SPrintF('Внимание! В PHP выключены следюущие функции: <U>%s</U>. Возможно данные функции потребуются для работы системы. Найдите в файле %s опцию <U>disable_functions</U> и установите для нее пустое значение.',$disable_functions,PHP_INI_PATH));
  #-----------------------------------------------------------------------------
  $Tests[] = 'Поиск приложений';
  #-----------------------------------------------------------------------------
  #-----------------------------------------------------------------------------
  if(IsSet($MySQLbin)){
    #---------------------------------------------------------------------------
    $Result = Exec($MySQLbin . ' --version 2>&1');
    #---------------------------------------------------------------------------
    if(Preg_Match('/[0-9]+\.[0-9]+\.[0-9]/',$Result,$MySQL)){
      #-------------------------------------------------------------------------
      $MySQL = Current($MySQL);
      #-------------------------------------------------------------------------
      if(IntVal($MySQL) >= 5)
        $Test = Array('Status'=>SPrintF('%s (совместимо)',$MySQL),'IsOk'=>TRUE);
      else
        $Test = Array('Status'=>SPrintF('%s (несовместимо)',$MySQL),'IsOk'=>FALSE,'Comment'=>'Несовместимая версия mysql. Требуется версия mysql 5+.');
    }else
      $Test = Array('Status'=>'Версия не определена','IsOk'=>FALSE,'Comment'=>'Не удалось определить версию mysql. Попробуйте, выполнить следующу команду mysql --version.');
  }else
    $Test = Array('Status'=>'Не найдено','IsOk'=>FALSE,'Comment'=>'Приложение mysql не найдено. Пожалуйста, воспользуйтесь менеджером пакетов для установки данной программы <A target="blank" href="http://wiki.joonte.com/?title=Документация:Подготовка_к_установке">[подробнее...]</A>');
  #-----------------------------------------------------------------------------
  $Test['Name'] = 'Клиент баз данных mysql';
  #-----------------------------------------------------------------------------
  $Tests[] = $Test;
  #-----------------------------------------------------------------------------
  $Result = Exec('htmldoc --version 2>&1');
  #-----------------------------------------------------------------------------
  if(!Preg_Match('/not\sfound/',$Result)){
    #---------------------------------------------------------------------------
    if(Preg_Match('/[0-9]+\.[0-9]+\.[0-9]/',$Result,$HtmlDoc)){
      #-------------------------------------------------------------------------
      $HtmlDoc = Current($HtmlDoc);
      #-------------------------------------------------------------------------
      if(FloatVal($HtmlDoc) >= 1.8)
        $Test = Array('Status'=>SPrintF('%s (совместимо)',$HtmlDoc),'IsOk'=>TRUE);
      else
        $Test = Array('Status'=>SPrintF('%s (несовместимо)',$HtmlDoc),'IsOk'=>FALSE,'Comment'=>'Несовместимая версия htmldoc. Требуется версия htmldoc 1.8+. htmldoc - приложение позволяющее биллинговой системе формировать документы в формате PDF.');
    }else
      $Test = Array('Status'=>'Версия не определена','IsOk'=>FALSE,'Comment'=>'Не удалось определить версию htmldoc. Попробуйте, выполнить следующу команду <U>htmldoc --version</U>. htmldoc - приложение позволяющее биллинговой системе формировать документы в формате PDF.');
  }else
    $Test = Array('Status'=>'Не найдено','IsOk'=>FALSE,'Comment'=>'Приложение htmldoc не найдено. Пожалуйста, воспользуйтесь менеджером пакетов для установки данной программы <A target="blank" href="http://wiki.joonte.com/?title=Документация:Подготовка_к_установке">[подробнее...]</A> htmldoc - приложение позволяющее биллинговой системе формировать документы в формате PDF.');
  #-----------------------------------------------------------------------------
  $Test['Name'] = 'Формирование PDF (htmldoc)';
  #-----------------------------------------------------------------------------
  $Tests[] = $Test;
  #-----------------------------------------------------------------------------
  $Tests[] = 'Проверка модулей PHP';
  #-----------------------------------------------------------------------------
  $Extensions = Array('gd','json','libxml','mbstring','mysql','openssl','xml','zlib');
  #-----------------------------------------------------------------------------
  foreach($Extensions as $Extension){
    #---------------------------------------------------------------------------
    $IsLoaded = Extension_Loaded($Extension);
    #---------------------------------------------------------------------------
    $Tests[] = Array('Name'=>SPrintF('Модуль %s',$Extension),'Status'=>($IsLoaded?'Установлен':'Не найден'),'IsOk'=>$IsLoaded,'Comment'=>SPrintF('Модуль <U>%s</U> не установлен в системе. Для его установки воспользуйтесь возможностями менеджера пакетов операционной системы или утилиты phpize, <A target="blank" href="http://wiki.joonte.com/?title=Документация:Подготовка_к_установке">[подробнее...]</A>',$Extension));
  }
  #-----------------------------------------------------------------------------
  echo '<FORM method="POST">';
  #-----------------------------------------------------------------------------
  $IsError = FALSE;
  #-----------------------------------------------------------------------------
  foreach($Tests as $Test){
    #---------------------------------------------------------------------------
    if(Is_Scalar($Test)){
#-------------------------------------------------------------------------------
$Echo =  <<<EOD
<TR>
 <TD colspan="2" class="Separator">%s</TD>
</TR>
EOD;
       #------------------------------------------------------------------------
       echo SPrintF($Echo,$Test);
       #------------------------------------------------------------------------
       continue;
     }
#-------------------------------------------------------------------------------
$Echo =  <<<EOD
<TR>
 <TD class="Comment">%s</TD>
 <TD width="110" class="Standard" style="background-color:%s;">%s</TD>
</TR>
EOD;
#-------------------------------------------------------------------------------
  $IsOk = $Test['IsOk'];
  #-----------------------------------------------------------------------------
    echo SPrintF($Echo,$Test['Name'],$IsOk?'#C1F17B':'#FF6666',$Test['Status']);
    #---------------------------------------------------------------------------
    if(!$IsOk){
      #-------------------------------------------------------------------------
      $IsError = TRUE;
#-------------------------------------------------------------------------------
$Echo =  <<<EOD
<TR>
 <TD colspan="2" class="Standard">%s</TD>
</TR>
EOD;
#-------------------------------------------------------------------------------
      echo SPrintF($Echo,$Test['Comment']);
    }
  }
  #-----------------------------------------------------------------------------
  if($IsError)
echo <<<EOD
<TR>
 <TD colspan="2" class="Standard" style="background-color:#FCE5CC;">В ходе проверки системных требований произошли ошибки. Можно продолжить установку системы, однако ее запуск и кооректная работа не гарантируется.</TD>
</TR>
EOD;
  #-----------------------------------------------------------------------------
echo <<<EOD
 <TR>
  <TD colspan="2" align="right">
   <INPUT type="submit" value="Продолжить" />
  </TD>
 </TR>
 <INPUT type="hidden" name="step-id" value="3" />
</FORM>
EOD;
  #-----------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
if($__STEP_ID == 3){
  #-----------------------------------------------------------------------------
$Echo = <<<EOD
<FORM method="POST">
 <TR>
  <TD class="Separator" colspan="2">Настройки связи c MySQL</TD>
 </TR>
 <TR>
  <TD class="Comment">Адрес сервера</TD>
  <TD>
   <INPUT name="db-server" type="input" size="20" value="%s" />
  </TD>
 </TR>
 <TR>
  <TD class="Comment">Порт сервера</TD>
  <TD>
   <INPUT name="db-port" type="input" size="10" value="%s" />
  </TD>
 </TR>
 <TR>
  <TD colspan="2" class="Separator">Пользователь и база данных</TD>
 </TR>
 <TR>
  <TD colspan="2" class="Standard">
   <INPUT %s name="db-type" type="radio" value="exists" onclick="form['db-root'].disabled = true;">База данных и пользователь уже существуют
   <BR />
   <INPUT %s name="db-type" type="radio" value="create" onclick="form['db-root'].disabled = false;form['db-root'].focus();">Создать базу данных и пользователя автоматически
  </TD>
 </TR>
 <TR>
  <TD class="Comment">Пароль пользователя root в MySQL</TD>
  <TD>
   <INPUT %s name="db-root" type="input" size="20" value="%s" />
  </TD>
 </TR>
 <TR>
  <TD class="Comment">Имя пользователя базы данных</TD>
  <TD>
   <INPUT name="db-user" type="input" size="20" value="%s" />
  </TD>
 </TR>
 <TR>
  <TD class="Comment">Пароль пользователя базы данных</TD>
  <TD>
   <INPUT name="db-password" type="input" size="20" value="%s" />
  </TD>
 </TR>
 <TR>
  <TD class="Comment">Название базы данных</TD>
  <TD>
   <INPUT name="db-name" type="input" size="20" value="%s" />
  </TD>
 </TR>
 <TR>
  <TD align="right" colspan="2">
   <INPUT type="submit" value="Продолжить" />
  </TD>
 </TR>
 <INPUT type="hidden" name="step-id" value="4" />
</FORM>
EOD;
  #-----------------------------------------------------------------------------
  echo SPrintF($Echo,$__SETTINGS['db-server'],$__SETTINGS['db-port'],($__SETTINGS['db-type'] != 'exists'?'none':'checked'),($__SETTINGS['db-type'] != 'create'?'none':'checked'),($__SETTINGS['db-type'] != 'create'?'disabled':'none'),$__SETTINGS['db-root'],$__SETTINGS['db-user'],$__SETTINGS['db-password'],$__SETTINGS['db-name']);
}
#-------------------------------------------------------------------------------
if($__STEP_ID == 5){
  #-----------------------------------------------------------------------------
$Echo = <<<EOD
<FORM method="POST">
 <TR>
  <TD class="Separator">Установка базы данных</TD>
 </TR>
 <TR>
  <TD class="Standard" style="background-color:#FCE5CC;">
   Биллинговая система использует триггеры в MySQL. До версии MySQL 5.1.6 для работы с триггерами необходимы права SUPER. Если Вы используете MySQL ниже версии 5.1.6, то для пользователя <U>%s</U> необходимо назначить такие права, данная операция может быть осуществлена как в ручную, так и автоматически.
   <BR />
   <UL class="Standard">
    <LI>
     Ручное назначение:
     <P>1. Войдите от имени пользователя root:</P>
     <PRE class="Console">
myuser@srv01:~> mysql -u root -p mysql
Enter password:</PRE>
     <P>2. Выполните слудующие запросы в MySQL:</P>
     <PRE class="Console">
GRANT ALL ON `%s`.* TO '%s'@'%%';
GRANT SUPER ON *.* TO '%s'@'%%'
flush privileges;</PRE>
    </LI>
    <LI>
     Автоматическое назначение:
     <P>Пароль пользователя root в MySQL:
      <INPUT name="db-root" type="input" size="20" value="%s" />
     </P>
    </LI>
   </UL>
  </TD>
 </TR>
 <TR>
  <TD align="right">
   <INPUT type="submit" value="Продолжить" />
  </TD>
 </TR>
 <INPUT name="step-id" value="6" type="hidden" />
</FORM>
EOD;
  #-----------------------------------------------------------------------------
  echo SPrintF($Echo,$__SETTINGS['db-user'],$__SETTINGS['db-user'],$__SETTINGS['db-user'],$__SETTINGS['db-name'],$__SETTINGS['db-root']);
}
#-------------------------------------------------------------------------------
if($__STEP_ID == 7){
  #-----------------------------------------------------------------------------
$Echo = <<<EOD
<FORM method="POST">
 <TR>
  <TD class="Separator">Установка базы данных</TD>
  <TR>
   <TD class="Standard" style="background-color:#FCE5CC;">В данный момент с использованием утилиты <U>mysql</U> будет импортированна база данных системы.</TD>
  </TR>
 </TR>
 <TR>
  <TD align="right">
   <INPUT type="submit" value="Продолжить" />
  </TD>
 </TR>
 <INPUT name="step-id" value="8" type="hidden" />
</FORM>
EOD;
  #-----------------------------------------------------------------------------
  echo $Echo;
}
#-------------------------------------------------------------------------------
if($__STEP_ID == 9){
  #-----------------------------------------------------------------------------
$Echo = <<<EOD
<FORM method="POST">
 <TR>
  <TD class="Separator">Завершение установки</TD>
  <TR>
   <TD class="Standard" colspan="2" style="background-color:#FCE5CC;">
    В данный момент будут выпоненны следующие действия:
    <UL class="Standard">
     <LI>Создана временная папка;</LI>
     <LI>Сохранена конфигурация домена;</LI>
     <LI>Создана символическая ссылка на публичный раздел;</LI>
     <LI>Удален временный файл хранения параметров установки;</LI>
    </UL>
   </TD>
  </TR>
 </TR>
 <INPUT name="step-id" value="10" type="hidden" />
</FORM>
EOD;
  #-----------------------------------------------------------------------------
  echo $Echo;
}
#-------------------------------------------------------------------------------
if($__STEP_ID == 11){
  #-----------------------------------------------------------------------------
$Echo = <<<EOD
<FORM method="POST">
 <TR>
  <TD class="Separator">Установка завершена</TD>
  <TR>
   <TD class="Standard" colspan="2" style="background-color:#FCE5CC;">
    Поздравляем! Биллинговая система успешно установлена!
    <BR />
    Для запуска очереди задач биллинговой системы, Вам необходимо добавить в системный планировщик задач операционной системы (crontab) задание с периодом выполнения 1 мин.:
    <PRE class="Console">wget -O - http://%s/Cron</PRE>
    После обновления данной страницы, в случае, если установка завершилась успешно, Вы попадете на страницу авторизации биллинговой системы, для входа используйте:
    <UL class="Standard">
     <LI>Email: admin@company.com</LI>
     <LI>Пароль: default</LI>
    </UL>
    После входа в систему воспользуйтесь руководством <A target="blank" href="http://wiki.joonte.com/index.php?title=Документация:Первый_запуск_системы">[первый запуск]</A>.
    <BR />
    В случае, если, после установки Вы увидите сообщения об ошибках, Вы можете обратиться за их полным описанием в файл <U>jbs-errors.log</U>, который находиться в текущей папке установки или в случае отсутствия прав записи в папке /tmp.
    <BR />
    Так же, если по каким-то причинам Вам необходимо повторить установку системы создайте файл <U>INSTALL</U> в текущей папке установки системы и система определит необходимость повторной установки.
   </TD>
  </TR>
 </TR>
 <INPUT name="step-id" value="6" type="hidden" />
</FORM>
EOD;
  #-----------------------------------------------------------------------------
  echo SPrintF($Echo,HOST_ID);
}
#-------------------------------------------------------------------------------
?>
