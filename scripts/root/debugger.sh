REMOTE_ADDR='127.0.0.1'
export REMOTE_ADDR
HTTP_HOST='joonte'
export HTTP_HOST
REQUEST_URI='/Debugger'
export REQUEST_URI
clear
echo "Debugger have runned. Waiting events."
php-cgi ./../../core/Load.php
