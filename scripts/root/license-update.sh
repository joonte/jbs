UniqID=`cat ./../../$1.uniq`
wget --no-check-certificate -O - "https://joonte.com/GetLicense?UniqID=$UniqID"