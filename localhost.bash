#!/usr/bin/env bash

set -eu
cd "$(dirname "${BASH_SOURCE[0]}")"

php -c cfg -S localhost:8080 -t html -d "include_path=$PWD/include"
