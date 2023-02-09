echo "Joonte Software Company 2007-2023"
echo "JBs database install programm"
echo ""
#-------------------------------------------------------------------------------
jHost=$1
if [ "$jHost" = "" ]
  then
    echo "JBs host is empty"
    exit
fi
dUser=$2
if [ "$dUser" = "" ]
  then
    echo "Database user name is empty"
    exit
fi
dName=$3
if [ "$dName" = "" ]
  then
    echo "Database name is empty"
    exit
fi
dPassword=$4
if [ "$dPassword" = "" ]
  then
    dPassword=""
fi
dServer=$5
if [ "$dServer" = "" ]
  then
    dServer="localhost"
fi
dPort=$6
if [ "$dPort" = "" ]
  then
    dPort="3306"
fi
#-------------------------------------------------------------------------------
if [ "$MYSQL_BIN" = "" ]
  then
    MYSQL_BIN="mysql"
  else
    echo "Use mysql in ($MYSQL_BIN)"
fi
#-------------------------------------------------------------------------------
for File in "structure" "views" "permissions" "triggers" "functions" "db"
do
  Path="./../../db/$jHost/$File.sql"
  if (test -e $Path)
    then
      echo "Install $File"
      MySQL="$MYSQL_BIN -u $dUser --password=$dPassword --host=$dServer --port=$dPort $dName"
      Result=`$MySQL < $Path 2>&1`
      if [ "$Result" ]
        then
          echo $Result
          echo "Error"
          exit
      fi
  fi
done
#-------------------------------------------------------------------------------
echo "Ok"
