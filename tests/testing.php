<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'util.php';

use IvoPetkov\HTML5DOMDocument, IvoPetkov\HTML5DOMElement;

function f_never_called(): callable
{
    return fn(...$_) => assert(false, "I wasn't supposed to be called");
}

function assert_strictly_equal(mixed $a, mixed $b): void
{
    assert($a === $b, var_export($a, true) . ' === ' . var_export($b, true));
}

function assert_strictly_unequal(mixed $a, mixed $b): void
{
    assert($a !== $b, var_export($a, true) . ' !== ' . var_export($b, true));
}

function assert_equal(mixed $a, mixed $b): void
{
    assert($a == $b, var_export($a, true) . ' == ' . var_export($b, true));
}

function assert_unequal(mixed $a, mixed $b): void
{
    assert($a != $b, var_export($a, true) . ' != ' . var_export($b, true));
}

function assert_is_in(mixed $x, array $C): void
{
    assert(
        array_search($x, $C) !== false,
        var_export($x, true) . ' ∈ {' . implode(', ', array_map(fn($c) => var_export($c, true), $C)) . '}',
    );
}

/**
 * @param callable(): void $action
 * @param callable(Throwable): bool $exception_predicate
 * @return void
 */
function assert_throws(callable $action, callable $exception_predicate): void
{
    try {
        $action();
        assert(false, 'An exception was expected to be thrown here');
    } catch (Throwable $e) {
        assert($exception_predicate($e), 'The thrown exception does not match the predicate');
    }
}

function submit_form(HTML5DOMDocument $dom, string $form_id): array
{
    $result = [];

    $form_elements = $dom->querySelectorAll("
        input[form=$form_id],
        textarea[form=$form_id],
        #$form_id input,
        #$form_id textarea
    ");
    foreach ($form_elements as /** @var IvoPetkov\HTML5DOMElement */ $e) {
        if (!_is_form_element_relevant($e)) {
            continue;
        }

        // echo "{$e->getLineNo()}: $e->outerHTML" . PHP_EOL;

        $name = $e->getAttribute('name');
        assert_strictly_unequal($name, '');

        $path = _get_path($name);
        $dest = &$result;
        foreach ($path as $key) {
            if ($key === '') {
                $dest = &$dest[];
            } else {
                $dest = &$dest[$key];
            }
        }
        $dest = match ($e->tagName) {
            'input' => match ($e->getAttribute('type')) {
                'radio', 'checkbox' => $e->getAttribute('value') ?: 'on',
                default             => $e->getAttribute('value'),
            },
            'textarea' => $e->textContent,
        };
    }

    return $result;
}

function _is_form_element_relevant(HTML5DOMElement $e): bool
{
    $type = $e->getAttribute('type');
    return $e->hasAttribute('name')
        && !$e->hasAttribute('disabled')
        && ($type !== 'radio' && $type !== 'checkbox' || !$e->hasAttribute('checked'))
        && !_element_has_parent($e, 'template');
}

function _element_has_parent(DOMElement $e, string $parent_tagName): bool
{
    assert_php_version('8.3.0');
    $parent = $e->parentElement;
    return $parent !== null
        && ($parent->tagName === $parent_tagName
            || _element_has_parent($parent, $parent_tagName));
}

/**
 * @param string $name
 * @return string[]
 */
function _get_path(string $name): array
{
    $matches = [];
    assert_strictly_equal(preg_match('/^[^[]+/', $name, $matches), 1);
    $first_key = $matches[0];
    assert_strictly_unequal(preg_match_all('/\G\[([^[\]]*)\]/', $name, $matches, offset: strlen($first_key)), false);
    return [$first_key, ...$matches[1]];
}

function fill_input(HTML5DOMDocument &$dom, string $id, string $value)
{
    $e = $dom->getElementById($id);
    assert_strictly_equal($e?->tagName, 'input');
    $type = $e->getAttribute('type');
    assert_strictly_unequal($type, 'image');

    notfalse($e->setAttribute('value', $value));
}

function fill_textarea(HTML5DOMDocument &$dom, string $id, string $content)
{
    $e = $dom->getElementById($id);
    assert($e?->tagName === 'textarea');

    $e->textContent = $content;
}

function check_input(HTML5DOMDocument &$dom, string $id)
{
    $e = $dom->getElementById($id);
    assert_strictly_equal($e?->tagName, 'input');
    assert_is_in($e->getAttribute('type'), ['checkbox', 'radio']);

    notfalse($e->setAttribute('checked', ''));
}

function uncheck_input(HTML5DOMDocument &$dom, string $id)
{
    $e = $dom->getElementById($id);
    assert_strictly_equal($e?->tagName, 'input');
    assert_is_in($e->getAttribute('type'), ['checkbox', 'radio']);

    notfalse($e->removeAttribute('checked'));
}

function assert_php_version(string $min_version) {
    static $first = true;
    if ($first) {
        $phpversion = notfalse(phpversion());
        assert(version_compare($phpversion, $min_version, '>='), "$phpversion >= $min_version");
        $first = false;
    }

}