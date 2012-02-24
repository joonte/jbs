#-------------------------------------------------------------------------------
for HostID in "root" "billing" "hosting"
do
  echo $HostID
  sh ../root/db-install.sh $HostID $1 $2 $3 $4 $5
done
