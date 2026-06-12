#!/bin/bash

cd `dirname $0`
chmod -R a+w storage
./artisan migrate
./artisan db:seed
