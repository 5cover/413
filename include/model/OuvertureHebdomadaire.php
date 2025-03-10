<?php

require_once 'db.php';
require_once 'model/MultiRange.php';
require_once 'Equatable.php';

/**
 * @implements Equatable<OuvertureHebdomadaire>
 * @implements ArrayAccess<int, MultiRange<Time>>
 */
final class OuvertureHebdomadaire implements ArrayAccess, Equatable
{
    /**
     * @var array<int, MultiRange<Time>>
     */
    private array $ouvertures_hebdomadaires = [];

    function __construct(
        private readonly Offre $offre,
    ) {}

    private function args(int $dow): ?array
    {
        return $this->offre->id === null ? null : [
            'id_offre' => [$this->offre->id, PDO::PARAM_INT],
            'dow'      => [$dow, PDO::PARAM_INT],
        ];
    }

    /**
     * @inheritDoc
     */
    function equals(mixed $other): bool
    {
        return $other->ouvertures_hebdomadaires === $this->ouvertures_hebdomadaires;
    }

    /**
     * @inheritDoc
     */
    function offsetExists(mixed $dow): bool
    {
        return isset($this->ouvertures_hebdomadaires[$dow]);
    }

    /**
     * @inheritDoc
     */
    function offsetGet(mixed $dow): MultiRange
    {
        return $this->ouvertures_hebdomadaires[$dow];
    }

    /**
     * @inheritDoc
     */
    function offsetSet(mixed $dow, mixed $horaires): void
    {
        $this->ouvertures_hebdomadaires[$dow] = $horaires;
    }

    /**
     * @inheritDoc
     */
    function offsetUnset(mixed $dow): void
    {
        unset($this->ouvertures_hebdomadaires[$dow]);
        if (null !== $args = $this->args($dow)) {
            notfalse(DB\delete_from(self::TABLE, $args)->execute());
        }
    }

    function push_to_db(): void
    {
        foreach ($this->ouvertures_hebdomadaires as $dow => $horaires) {
            $this->insert_ouverture_hebdomadaire($dow, $horaires);
        }
    }

    private function insert_ouverture_hebdomadaire(int $dow, MultiRange $horaires): void
    {
        if (null !== $args = $this->args($dow)) {
            notfalse(DB\insert_into(self::TABLE, $args + [
                'horaires' => [$horaires, PDO::PARAM_STR],
            ])->execute());
        }
    }

    const TABLE = '_ouverture_hebdomadaire';
}
