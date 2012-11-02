#!/bin/sh

if [ "x$1" = "x" ]
then
	echo "Usage: `basename $0` /path/to/svn/dir"
	exit 1;
fi

tmp_dir="/tmp/$$.bill"
mkdir -p $tmp_dir
file_list="$tmp_dir/file.list.txt"

dir_list="install core hosts/billing hosts/hosting hosts/root db others scripts/billing scripts/hosting scripts/root styles/billing styles/hosting styles/root patches"

cd $1 || exit 2;

for dir in $dir_list
do
	find $dir -type f | grep -v .git >> $file_list
done

# add some files
echo index.php >> $file_list
echo .htaccess >> $file_list

# create archive
tar --create --file=- -T $file_list > $tmp_dir/JBs.tar

cd $tmp_dir
touch INSTALL
echo hosting,billing,root > HostsIDs.txt
tar --append --file=$tmp_dir/JBs.tar INSTALL
tar --append --file=$tmp_dir/JBs.tar HostsIDs.txt

#ls -alh JBs.tar

gzip $tmp_dir/JBs.tar
mv $tmp_dir/JBs.tar.gz /tmp/

echo "distr can be found in /tmp/JBs.tar.gz"

rm -Rf $tmp_dir

