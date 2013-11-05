#!/bin/sh

# $1 - /path/to/php.ini
# $2 - /path/to/php
# $3 - billing.hostname.su

PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/sbin:/usr/local/bin
TMPDIR="${TMPDIR:="/tmp"}"
TmpFile="$TMPDIR/jbs.$$.txt"

# проверяем что запись во временный файл возможна
if ! echo -n > $TmpFile >/dev/null 2>&1
then
	echo "cannot create tmp file = $TmpFile"
	exit 1;
fi

#find php
if test -x "$2" -a -f "$2"
then
	echo $2 > $TmpFile
else
	for cli in /usr/bin/php /usr/local/bin/php /usr/bin/php-cgi /usr/local/bin/php-cgi
	do
		test -x $cli && echo $cli > $TmpFile
	done
fi
#------------------------------------------------
if ! test -s $TmpFile
then
	echo "cannot find php interpretator"
	echo 1;
fi
#------------------------------------------------
if test -f "$1"
then
	export PHP_BIN="`cat $TmpFile` -c $1"
else
	export PHP_BIN="`cat $TmpFile`"
fi

rm -f $TmpFile
#------------------------------------------------
# топаем в директорию со скриптами
ScriptsDir="`dirname $0`"
if ! cd $ScriptsDir
then
	echo "cannot change dir to $ScriptsDir"
	exit 1;
fi
ScriptsDir="`pwd`"
#------------------------------------------------
if test -n "$3"
then
	echo "$3" > $TmpFile
else
	# ищщем host.ini
	for dir in ../../hosts/*
	do
		if test -f $dir/host.ini
		then
			eval `cat $dir/host.ini | grep 'HostsIDs=' | awk -F ',' '{print $1}' | tr -d '"' `
			echo $HostsIDs > $TmpFile
		fi
	done
fi
#------------------------------------------------
if ! test -s $TmpFile
then
	echo "cannot find billing hostname"
	exit 1;
else
	HostsID=`cat $TmpFile`
	rm -f $TmpFile
fi
#------------------------------------------------
# достаём корневую директорию биллинга
RootDir=`dirname $ScriptsDir`
RootDir=`dirname $RootDir`

#------------------------------------------------
#------------------------------------------------
marker="$RootDir/hosts/$HostsID/tmp/TaskLastExecute.txt"
# проверяем, запущен скрипт или нет
if [ ! `ps auxww | grep "sh demon.sh $HostsID" | grep -v grep | wc -l` -gt 0 ]
then
	rm -f $marker
	# let Mortal Combat begin! =)
	sh demon.sh $HostsID $RootDir >> $RootDir/demon.log &
fi

#------------------------------------------------
#------------------------------------------------
# проверяем, как давно выполнялось последнее задание
if test -f $marker
then
	# определяем время на час назад, в разных системах по разному
	if [ `uname` = "Linux" ]
	then
		params="date --date='1 hour ago'"
	else
		params="-v-1H"
	fi

	executed=`cat $marker`
	if [ `date $params +%Y%m%d%H%M%S` -ge $executed ]
	then
		echo "" >> $RootDir/demon.log
		echo "`date +%Y-%m-%d` in `date +%H:%M:%S`: php-cgi auto killed, no executed tasks more than one hour" >> $RootDir/demon.log
		echo "" >> $RootDir/demon.log
		killall php-cgi
	fi
fi

# delete tmp file
rm -f $TmpFile

