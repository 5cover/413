<?php

use DB\Action;
use DB\Arg;
use DB\BinaryClause, DB\BinOp;

require_once 'db.php';
require_once 'ValueObjects/MultiRange.php';

final class OuvertureHebdomadaire
{
    /**
     * @var array<int, MultiRange<Time>>
     */
    private array $ouvertures_hebdomadaires = [];
    /**
     * @var array<int, Action>
     */
    private array $diff = [];

    function __construct(
        private readonly int $id_offre,
    )
    {
        $stmt = DB\select(DB\Table::OuvertureHebdomadaire, ['dow', 'horaires'], [
            new BinaryClause('id_offre', BinOp::Eq, $this->id_offre, PDO::PARAM_INT),
        ]);
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $this->ouvertures_hebdomadaires[$row->dow] = MultiRange::parse($row->horaires, Time::parse(...));
        }
    }

    function get(int $dow): MultiRange
    {
        assert(0 <= $dow and $dow <= 6);
        return $this->ouvertures_hebdomadaires[$dow] ?? MultiRange::empty();
    }

    function set(int $dow, MultiRange $horaires): void
    {
        assert(0 <= $dow and $dow <= 6);
        $this->diff[$dow] ??= isset($this->ouvertures_hebdomadaires) ? Action::Insert : Action::Update;
        $this->ouvertures_hebdomadaires[$dow] = $horaires;
    }

    function push(): void
    {
        /** @var Arg[] $to_insert */
        $to_insert = [];

        foreach ($this->diff as $dow => $action) {
            $h = new Arg($this->ouvertures_hebdomadaires[$dow], PDO::PARAM_STR);
            switch ($action) {
                case Action::Insert:
                    $to_insert[] = new Arg($this->id_offre, PDO::PARAM_INT);
                    $to_insert[] = new Arg($dow, PDO::PARAM_INT);
                    $to_insert[] = $h;
                    break;
                case Action::Update:
                    $stmt = DB\update(DB\Table::OuvertureHebdomadaire, [
                        'horaires' => $h,
                    ], [
                        new BinaryClause('id_offre', BinOp::Eq, $this->id_offre, PDO::PARAM_INT),
                        new BinaryClause('dow', BinOp::Eq, $dow, PDO::PARAM_INT),
                    ]);
                    notfalse($stmt->execute());
                    break;
                default:
                    assert(false, "usupported action $action->name");
            }
        }
        $stmt = DB\insert_into_multiple(DB\Table::OuvertureHebdomadaire, ['id_offre', 'dow', 'horaires'], $to_insert);
        notfalse($stmt->execute());

        $this->diff = [];
    }
}
