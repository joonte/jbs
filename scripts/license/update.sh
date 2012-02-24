User="update"
Password="Update#Passwd"
Folder="/work/releases"
#-------------------------------------------------------------------------------
echo "1. Экспорт из репозитария"
#-------------------------------------------------------------------------------
if !([[ -d $Folder/jbs ]])
  then
    echo "Директория $Folder/jbs не найдена"
    #exit
fi
rm -rf $Folder/jbs/*
SVN="svn export --username=$User --password=$Password svn://joonte.com/jbs/trunk"
for Source in "core" "hosts" "styles" "db" "scripts" "patches" "others"
do
  $SVN/$Source $Folder/jbs/$Source > /dev/null
done

$SVN/index.php $Folder/jbs/index.php > /dev/null
$SVN/.htaccess $Folder/jbs/.htaccess > /dev/null
touch $Folder/jbs/INSTALL
echo "Выполненно"
#-------------------------------------------------------------------------------
echo "2. Проверка на ошибки"
#-------------------------------------------------------------------------------
for File in `find $Folder/jbs -name *.comp`
do
  Result=`php -l $File 2>&1`
  if [ `expr "$Result" : 'PHP Parse error.*'` -gt 0 ];
    then
      echo $Result
      exit
  fi
done
echo "Выполненно"
#-------------------------------------------------------------------------------
echo "3. Формирование архива версии"
#-------------------------------------------------------------------------------
wget -O - http://joonte.com/GetSnapshot > /dev/null
echo "Выполненно"
#-------------------------------------------------------------------------------
