#!/bin/bash

rotateFiles()
{
    maxFile=5
    fileName=$1
    for((i=$maxFile; i>=0; i=i-1))
    do
        if [ $i -ne 0 ]
        then
            rotateFile=$fileName$i
        else
            rotateFile=$fileName
        fi

        if [ -f $rotateFile ]
        then
            if [ $i -eq $maxFile ]
            then
                /bin/rm -rf $rotateFile
                continue
            fi
            /bin/cp $fileName $rotateFile$(($i + 1))
            echo '' > $fileName
        fi
    done
}

cd /var/www/crossborder/runtime/logs/cron

files=`/usr/bin/find . -name "*.log*" -size +35M -type f`

for log in $files
do
    rotateFiles $log
done

exit 0