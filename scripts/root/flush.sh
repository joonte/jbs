if test -f /usr/bin/nc
then
	echo flush_all | nc localhost 11211
else
telnet localhost 11211 << EOF
flush_all
quit
EOF
fi