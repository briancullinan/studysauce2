#!/bin/sh
if ps -ef | grep -v grep | grep test\.studysauce ; then
        exit 0
else
        wget --no-check-certificate -O /dev/null -o /dev/null https://test.studysauce.com/cron
        exit 0
fi