#!/bin/env bash

set -eu
cd "$(dirname "${BASH_SOURCE[0]}")"

cd pact
cat data.sql images.sql offre/*.sql
cd ../tchattator
