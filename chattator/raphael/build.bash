#!/usr/bin/env bash
set -eu
cd "$(dirname "${BASH_SOURCE[0]}")"
mkdir -p lib/json-c
cd lib/json-c

cp ../../json-c/arraylist.h .
cmake ../../json-c -DCMAKE_BUILD_TYPE=release -DBUILD_SHARED_LIBS=OFF -DDISABLE_EXTRA_LIBS=ON