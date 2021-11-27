﻿<?php
#-------------------------------------------------------------------------------
/** @author Бреславский А.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
Error_Reporting(E_ALL);
#-------------------------------------------------------------------------------
echo <<<EOD
Joonte Software Company 2007-2022
JBs database install programm

EOD;
#-------------------------------------------------------------------------------
# Using:
# %1 - JBs host
# %2 - database user name
# %3 - database name
# %4 - database user password
# %5 - database server
# %6 - database port
# Example:
# php db-install.php root root jbs
#-------------------------------------------------------------------------------
$jHost = @$argv[1];
#-------------------------------------------------------------------------------
if(!$jHost)
  Exit("JBs host is empty\n");
#-------------------------------------------------------------------------------
$dUser = @$argv[2];
#-------------------------------------------------------------------------------
if(!$dUser)
  Exit("Database user name is empty\n");
#-------------------------------------------------------------------------------
$dName = @$argv[3];
#-------------------------------------------------------------------------------
if(!$dName)
  Exit("Database name is empty\n");
#-------------------------------------------------------------------------------
$dPassword = @$argv[4];
#-------------------------------------------------------------------------------
if(!$dPassword)
  $dPassword = '';
#-------------------------------------------------------------------------------
$dServer = @$argv[5];
#-------------------------------------------------------------------------------
if(!$dServer)
  $dServer = 'localhost';
#-------------------------------------------------------------------------------
$dPort = @$argv[6];
#-------------------------------------------------------------------------------
if(!$dPort)
  $dPort = 3306;
#-------------------------------------------------------------------------------
$MYSQL_BIN = @$_ENV['MYSQL_BIN'];
#-------------------------------------------------------------------------------
if($MYSQL_BIN)
  echo SPrintF("Use mysql in (%s)\n",$MYSQL_BIN);
else
  $MYSQL_BIN = 'c:\Program Files\MySQL\MySQL Server 5.1\bin\mysql.exe';
#-------------------------------------------------------------------------------
foreach(Array('structure','views','permissions','triggers','functions','db') as $File){
  #-----------------------------------------------------------------------------
  $Path = SPrintF('./../../db/%s/%s.sql',$jHost,$File);
  #-----------------------------------------------------------------------------
  if(File_Exists($Path)){
    #---------------------------------------------------------------------------
    echo SPrintF("Install %s\n",$File);
    #---------------------------------------------------------------------------
    $MySQL = SPrintF('%s -u %s --password=%s --host=%s --port=%u %s',$MYSQL_BIN,$dUser,$dPassword,$dServer,$dPort,$dName);
    #---------------------------------------------------------------------------
    $Result = Exec(SPrintF('%s < %s 2>&1',$MySQL,$Path));
    #---------------------------------------------------------------------------
    if($Result){
      #-------------------------------------------------------------------------
      echo SPrintF("%s\n",$Result);
      #-------------------------------------------------------------------------
      Exit("Error\n");
    }
  }
}
#-------------------------------------------------------------------------------
?>
