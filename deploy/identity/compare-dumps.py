#!/usr/bin/env python3
"""Compare pg_dump COPY records as multisets; never print stored record values."""
import argparse
from collections import Counter
from pathlib import Path
import re
from uuid import UUID


def snapshot(path):
    tables = {}
    statements = []
    current = None
    with Path(path).open(encoding='utf-8') as source:
        for raw in source:
            line = raw.rstrip('\r\n')
            if current is not None:
                if line == r'\.':
                    current = None
                else:
                    tables[current][line] += 1
                continue
            if not line.strip() or line.startswith('--') or re.match(r'^\\(?:un)?restrict\s', line):
                continue
            if line.startswith('COPY ') and line.endswith(' FROM stdin;'):
                if line in tables:
                    raise ValueError('Duplicate COPY section')
                tables[line] = Counter()
                current = line
            else:
                statements.append(line)
    if current is not None or not tables:
        raise ValueError('Incomplete or empty COPY dump')
    return tables, Counter(statements)


def records(tables, name):
    result = []
    matches = 0
    for header, rows in tables.items():
        match = re.fullmatch(r'COPY (\S+) \(([^)]+)\) FROM stdin;', header)
        if not match or match[1] != name:
            continue
        matches += 1
        columns = match[2].split(', ')
        for line, count in rows.items():
            values = line.split('\t')
            if len(columns) != len(values):
                raise ValueError('Invalid COPY column count')
            result.extend([dict(zip(columns, values))] * count)
    if matches != 1:
        raise ValueError('Required COPY table missing or duplicated')
    return result


def valid_admin_notifications(before_tables, old, new, header, profiles_before, profiles_after):
    if old - new:
        return False  # Every original notification must remain byte-for-byte intact.
    old_profiles = records(snapshot(profiles_before)[0], 'presensi.profiles')
    current_profiles = records(snapshot(profiles_after)[0], 'presensi.profiles')
    old_ids = {row['id'] for row in old_profiles}
    if not old_ids.issubset({row['id'] for row in current_profiles}):
        return False
    new_admins = [row['id'] for row in current_profiles if row['id'] not in old_ids and row['role'] == 'admin']
    expected = Counter()
    for name, kind in [('presensi.izin', 'izin'), ('presensi.absen_susulan', 'susulan'), ('presensi.pembatalan_pulang', 'pembatalan')]:
        for request in records(before_tables, name):
            if request['status_persetujuan'] == 'menunggu':
                for recipient in new_admins:
                    expected[(recipient, request['user_id'], kind, request['id'], 'menunggu', request['created_at'], r'\N')] += 1
    actual = Counter()
    keys = ['recipient_id', 'pengaju_id', 'jenis', 'request_id', 'status', 'created_at', 'read_at']
    for row in records({header: new - old}, 'presensi.notifications'):
        UUID(row['id'])
        actual[tuple(row[key] for key in keys)] += 1
    if actual != expected:
        return False
    print(f'PASS verified new-admin queue notifications: {sum(actual.values())}; all original notifications intact.')
    return True


def compare(before_path, after_path, profiles_before=None, profiles_after=None):
    before, before_statements = snapshot(before_path)
    after, after_statements = snapshot(after_path)
    equal = True
    for header in sorted(before.keys() | after.keys()):
        table = header.split(' (', 1)[0].removeprefix('COPY ')
        old = before.get(header, Counter())
        new = after.get(header, Counter())
        same = header in before and header in after and old == new
        trigger_additions = False
        if table == 'presensi.notifications' and profiles_before and profiles_after and header in before and header in after:
            trigger_additions = valid_admin_notifications(before, old, new, header, profiles_before, profiles_after)
            same = trigger_additions
        note = 'original records intact; trigger additions verified' if trigger_additions else ('identical' if same else 'differ')
        print(f'{"PASS" if same else "FAIL"} {table}: before={sum(old.values())} after={sum(new.values())}; records {note}')
        equal = equal and same
    if before_statements != after_statements:
        print('FAIL non-COPY dump statements differ; contents omitted.')
        equal = False
    if equal:
        print('PASS original records preserved and dump statements match; any new notifications satisfy the new-admin queue rule.')
    return equal


if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument('before', type=Path)
    parser.add_argument('after', type=Path)
    parser.add_argument('--profiles-before', type=Path)
    parser.add_argument('--profiles-after', type=Path)
    args = parser.parse_args()
    try:
        if bool(args.profiles_before) != bool(args.profiles_after):
            raise ValueError('Both profile snapshots are required')
        raise SystemExit(0 if compare(args.before, args.after, args.profiles_before, args.profiles_after) else 1)
    except (ValueError, OSError, UnicodeError, KeyError):
        raise SystemExit('Dump comparison could not be completed; record contents omitted.')
