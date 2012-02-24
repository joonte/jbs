REMOTE_ADDR='127.0.0.1'
export REMOTE_ADDR
SERVER_NAME=$1
export SERVER_NAME
HTTP_HOST=$1
export HTTP_HOST
REQUEST_URI='/Cron'
export REQUEST_URI
REDIRECT_STATUS=200
export REDIRECT_STATUS
SCRIPT_FILENAME=$2/core/Load.php
export SCRIPT_FILENAME

if [ "$PHP_BIN" = "" ]
  then
    PHP_BIN="php-cgi"
  else
    echo "Use php in ($PHP_BIN)"
fi

$PHP_BIN 2>&1