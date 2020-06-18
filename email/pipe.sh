#!/bin/sh

# 0755
# Assuming this file is located in ~/mail/bloomlocal.com/<account>/

cd $(dirname $0)
cp new/* .Archive/cur/ > /dev/null 2>&1
cp cur/* .Archive/cur/ > /dev/null 2>&1

cd .Archive/cur/
ls *, >/dev/null 2>&1 | xargs -I {} mv {} "{}S" > /dev/null 2>&1
ls | grep -v ":2,S" | xargs -I {} mv {} "{}:2,S" > /dev/null 2>&1
