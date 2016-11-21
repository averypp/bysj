#!/bin/bash
 for i in 1 2 3 4 5
 do
 sleep 1;echo 1>>aa && echo ”done!”;
 done
 cat aa|wc -l
 rm aa
