#!/bin/bash

str=$0
find='/run_if_not_run.sh'
FILEDIR=${str//$find/}

if ps axww | egrep "node $FILEDIR/$1" | grep -v grep | grep -v '/bin/sh -c' > /dev/null
then
    echo "$FILEDIR/$1 is running"
else
    echo "$FILEDIR/$1 is not running"
    node "$FILEDIR/$1" > "$2" 2>&1 &
fi

