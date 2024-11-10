#!/usr/bin/env python3

# Run: support/dataset/copy_sample_imgs.py | xsel -ib

from pathlib import Path
import argparse as ap
from dataclasses import dataclass
from shutil import copy
from subprocess import run


@dataclass(frozen=True)
class Args:
    dry_run: bool
    output: Path
    indent: int


def mime_type(path: Path) -> tuple[str, str]:
    return run(
        ('file', '-b', '--mime-type', '--', path),
        check=True, text=True, capture_output=True).stdout.rstrip().split(
        '/', 2)


if __name__ == '__main__':
    DIR = Path(__file__).parent

    parser = ap.ArgumentParser(formatter_class=ap.ArgumentDefaultsHelpFormatter)
    parser.add_argument('-n', '--dry-run', action='store_true', help='do not copy images')
    parser.add_argument(
        '-o', '--output', default=Path(DIR, '../../main/html/images_utilisateur').resolve(),
        type=Path, help='output dir')
    parser.add_argument('-i', '--indent', default=4, help='indent width')
    a = Args(**vars(parser.parse_args()))
    indent = ' ' * a.indent

    if not a.dry_run:
        a.output.mkdir(parents=True, exist_ok=True)

    print('begin;')
    print()
    print("set schema 'pact';")
    print()
    print('insert into')
    print(f'{indent}_image (taille, mime_subtype, legende)')
    print('values')

    i = 0
    for f in sorted(Path(DIR, 'sample_imgs').iterdir(), key=lambda f: f.stat().st_mtime):
        if i > 0:
            print(f', -- {i}')
        i += 1

        ftype = mime_type(f)
        if ftype[0] != 'image':
            parser.error(f"all files must be images, but '{f}' is not an image")

        mime_subtype = ftype[1].replace("'", "''")
        size = f.stat().st_size
        legend = f.stem.replace("'", "''")

        print(f"{indent}({size}, '{mime_subtype}', '{legend}')", end='')

        if not a.dry_run:
            copy(f, Path(a.output, f'{i}.{mime_subtype}'))

    print(f" -- {i}")
    print(';')
    print()
    print('commit;')
