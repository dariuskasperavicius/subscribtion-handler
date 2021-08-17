#!/usr/bin/env bash
export HOST_IP=$(ifconfig | grep inet | grep 192 | head -1 | awk '{print $2}')

> .env.local
echo 'HOST_IP='$HOST_IP >> .env.local
echo 'Host ip' $HOST_IP 'added to .env.local file.'

#sed 1,1d .env
cat .env >> .env.tmp
> .env
echo 'HOST_IP='$HOST_IP >> .env
echo '' >> .env
cat .env.tmp >> .env
rm -rf .env.tmp

