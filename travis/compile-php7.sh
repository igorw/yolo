#!/bin/sh

rm -rf php-src
git clone https://github.com/php/php-src.git
cd php-src
./buildconf --force
./configure --quiet
make --quiet
cp sapi/cli/php ../php
cd ..
