<?php

require_once 'testing.php';
require_once 'ValueObjects/MultiRange.php';

test_case('{}', 0, f_never_called());
test_case('{[1,5]}', 1, parse_int(...));
test_case('{  [1,5]  }', 1, parse_int(...));
test_case('{ [1,5] ,  (8,19) }', 2, parse_int(...));

test_throws('', f_never_called(), fn($e) => $e instanceof DomainException);
test_throws('{', f_never_called(), fn($e) => $e instanceof DomainException);
test_throws('{[', f_never_called(), fn($e) => $e instanceof DomainException);
test_throws('{[5', f_never_called(), fn($e) => $e instanceof DomainException);
test_throws('{[5,', f_never_called(), fn($e) => $e instanceof DomainException);
test_throws('{[5,1', f_never_called(), fn($e) => $e instanceof DomainException);
test_throws('{[5,1]', f_never_called(), fn($e) => $e instanceof DomainException);
test_throws('{[5,1]}', parse_int(...), fn($e) => $e instanceof RangeException);

//echo MultiRange::parse("{[1,2]}", parse_int(...)) . PHP_EOL;

/**
 * Cas de test nominal.
 * @param string $output
 * @param int $range_count
 * @param callable $parse_bound
 * @return void
 */
function test_case(string $output, int $range_count, callable $parse_bound): void {
    $r = MultiRange::parse($output, $parse_bound);
    assert_strictly_equal(count($r->ranges), $range_count);
    $roundtripped = MultiRange::parse($r, $parse_bound);
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
    assert_throws(fn() => MultiRange::parse($output, $parse_bound), $exception_predicate);
}