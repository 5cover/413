#!/bin/env bash

set -eu
cd "$(dirname "${BASH_SOURCE[0]}")"

cd pact
cat data.sql
cd ../tchattator
cat data.sql
