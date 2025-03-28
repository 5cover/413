<?php

require_once 'testing.php';
require_once 'ValueObjects/NonEmptyRange.php';

test_case('[,]', false, null, null, false, f_never_called());
test_case('[,1]', false, null, 1, true, parse_int(...));
test_case('[1,2]', true, 1, 2, true, parse_int(...));
test_case('(1,5)', false, 1, 5, false, parse_int(...));
test_case('(1,5]', false, 1, 5, true, parse_int(...));
test_case('[1,5)', true, 1, 5, false, parse_int(...));

test_throws('[5,1]', parse_int(...), fn($e) => $e instanceof RangeException);
test_throws('[', parse_int(...), fn($e) => $e instanceof DomainException);
test_throws('[5,1', parse_int(...), fn($e) => $e instanceof DomainException);
test_throws('[1,5', parse_int(...), fn($e) => $e instanceof DomainException);
test_throws('[fff,fff]', parse_int(...), fn($e) => $e instanceof DomainException);

test_case('["1296  ","3299 "]', true, 1296, 3299, true, parse_int(...));
test_case('[" 1296  ","  3299"]', true, 1296, 3299, true, parse_int(...));
test_case('[" 1296  ",  3299]', true, 1296, 3299, true, parse_int(...));
test_case('[1296  ,  3299]', true, 1296, 3299, true, parse_int(...));
test_case('(1296  ,  3299]', false, 1296, 3299, true, parse_int(...));
test_case('[" 1296  ",]', true, 1296, null, false, parse_int(...));

test_case('[fff,ggg]', true, 'fff', 'ggg', true);
test_case('["fff","ggg"]', true, 'fff', 'ggg', true);
test_case('["fff",ggg]', true, 'fff', 'ggg', true);
test_case('["""\"\\\\",£££]', true, '""\\', '£££', true);
test_case('[ plg , £££ ]', true, ' plg ', ' £££ ', true);
test_case('[" \m","\n "]', true, ' m', 'n ', true);

/**
 * Cas de test nominal.
 * @template T
 * @param string $output
 * @param bool $lower_inc
 * @param T $lower
 * @param T $upper
 * @param bool $upper_inc
 * @param ?callable(string): T $parse_bound
 * @return void
 */
function test_case(string $output, bool $lower_inc, mixed $lower, mixed $upper, bool $upper_inc, ?callable $parse_bound = null): void
{
    $r = NonEmptyRange::parse($output, $parse_bound);
    assert_strictly_equal($r->lower_inc, $lower_inc);
    assert_strictly_equal($r->lower, $lower);
    assert_strictly_equal($r->upper, $upper);
    assert_strictly_equal($r->upper_inc, $upper_inc);

    $roundtripped = NonEmptyRange::parse($r, $parse_bound);
    assert_equal($r, $roundtripped);
}

/**
 * Cas de test d'erreur.
 * @template T
 * @param string $output
 * @param callable(string): T $parse_bound
 * @param callable(Throwable): bool $exception_predicate
 * @return void
 */
function test_throws(string $output, callable $parse_bound, callable $exception_predicate): void
{
    assert_throws(fn() => NonEmptyRange::parse($output, $parse_bound), $exception_predicate);
}
