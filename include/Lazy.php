<?php

/**
 * @template T
 * @property-read T $value
 */
final class Lazy
{
    private mixed $value;

    function __get(string $name)
    {
        return match ($name) {
            'value' => $this->value ??= ($this->get_value)(),
        };
    }

    /**
     * @param Closure(): T $get_value
     */
    function __construct(
        private readonly Closure $get_value,
    ) {}

    /**
     * @param T $value
     * @return Lazy
     */
    static function of(mixed $value): self
    {
        return new self(fn() => $value);
    }
}
