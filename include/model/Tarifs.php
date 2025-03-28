<?php

use DB\Action;
use DB\Arg;
use DB\BinOp;
use DB\BinaryClause;
use DB\InListClause;

require_once 'db.php';

/**
 * @implements IteratorAggregate<string, float>
 */
final class Tarifs implements IteratorAggregate
{
    /**
     * @var array<string, float>
     */
    private array $tarifs = [];
    /**
     * @var array<string, Action>
     */
    private array $diff = [];

    function __construct(
        private readonly int $id_offre,
    ) {
        $stmt = DB\select(DB\Table::Tarif, ['nom', 'montant'], [
            new BinaryClause('id_offre', BinOp::Eq, $this->id_offre, PDO::PARAM_INT),
        ]);
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $this->tarifs[$row->nom] = $row->horaires;
        }
    }

    function set(string $nom, float $montant): void
    {
        $this->diff[$nom] ??= isset($this->tarifs[$nom]);
        $this->tarifs[$nom] = $montant;
    }

    function unset(string $nom): void
    {
        if (isset($this->tarifs[$nom]) and isset($this->diff[$nom])) $this->diff[$nom] = Action::Delete;
        unset($this->tarifs[$nom]);

    }

    function push(): void
    {
        /** @var Arg[] $to_insert */
        $to_insert = [];
        /** @var string[] $to_insert */
        $to_delete = [];

        foreach ($this->diff as $nom => $action) {
            $montant = new Arg($this->tarifs[$nom], PDO::PARAM_STR);
            switch ($action) {
                case Action::Insert:
                    $to_insert[] = new Arg($this->id_offre, PDO::PARAM_INT);
                    $to_insert[] = new Arg($nom, PDO::PARAM_STR);
                    $to_insert[] = $montant;
                    break;
                case Action::Update:
                    $stmt = DB\update(DB\Table::Tarif, [
                        'montant' => $montant,
                    ], [
                        new BinaryClause('id_offre', BinOp::Eq, $this->id_offre, PDO::PARAM_INT),
                        new BinaryClause('nom', BinOp::Eq, $nom, PDO::PARAM_STR),
                    ]);
                    notfalse($stmt->execute());
                    break;
                case Action::Delete:
                    $to_delete[] = $nom;
                    break;
            }

        }
        $stmt = DB\insert_into_multiple(DB\Table::Tarif, ['id_offre', 'nom', 'montant'], $to_insert);
        notfalse($stmt->execute());

        $stmt = DB\delete(DB\Table::Tarif, [
            new BinaryClause('id_offre', BinOp::Eq, $this->id_offre, PDO::PARAM_INT),
            new InListClause('nom', $to_delete, PDO::PARAM_STR),
        ]);
        notfalse($stmt->execute());

        $this->diff = [];
    }

    /**
     * @inheritDoc
     */
    function getIterator(): Traversable
    {
        return new ArrayIterator($this->tarifs);
    }
}
