#!/usr/bin/env bash

root=$(cd "$(dirname "$0")"; cd ..; pwd)

rsync -avP $root --exclude-from=$root/bin/exclude.txt root@47.52.30.33:/web/carpool
