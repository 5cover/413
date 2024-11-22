#!/usr/bin/env python3

# This script splits a big file into smaller files in the current directory

# Currently setup for splitting SQL offres insert intos

import os
import re
from pathlib import Path
INPUT = '../offres.sql'
PATTERN = r'^-- (.+)((?:.|\n)+?)(?=^-- .+|^commit;$)'


os.chdir(Path(__file__).parent)

offres_sql = open(INPUT).read()

# mettre dans les bons fichiers
for name, sql in re.findall(PATTERN, offres_sql, re.M):
    with open(name + '.sql', 'w') as f:
        print('begin;', file=f)
        print(file=f)
        print("set schema 'pact';", file=f)
        print(file=f)
        print(sql, file=f)
        print("commit;", file=f)
