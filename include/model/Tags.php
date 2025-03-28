<?php

use DB\Arg;
use DB\InListClause;
use DB\BinOp;
use DB\BinaryClause;

require_once 'db.php';

/**
 * @implements IteratorAggregate<string, true>
 */
final class Tags implements IteratorAggregate
{
    /** @var array<string, true> */
    private array $tags;
    /** @var array<string, true> **/
    private array $db_tags;

    function __construct(
        private readonly int $id_offre,
    ) {
        $stmt = DB\select(DB\Table::Tags, ['tag'], [
            new BinaryClause('id_offre', BinOp::Eq, $this->id_offre, PDO::PARAM_INT),
        ]);
        notfalse($stmt->execute());
        $this->db_tags = $this->tags = array_fill_keys($stmt->fetchAll(PDO::FETCH_COLUMN), true);
    }

    function add(string $tag): void
    {
        $this->tags[$tag] = true;
    }

    function remove(string $tag): void
    {
        unset($this->tags[$tag]);
    }

    function push(): void
    {
        // Compute diff
        $to_insert = array_diff_key($this->tags, $this->db_tags);     // in memory, not in DB
        $to_delete = array_diff_key($this->db_tags, $this->tags);     // in DB, not in memory

        // todo: let's just hope that this doesn't fail halfway or the caller is in a transaction block

        if (!$to_insert and !$to_delete) return;

        // Delete removed
        $stmt = DB\delete(DB\Table::Galerie, [
            new InListClause('id_offre', $to_delete, PDO::PARAM_INT),
        ]);
        notfalse($stmt->execute());

        // Insert new
        $values = [];
        foreach ($to_insert as $tag => $_) {
            $values[] = new Arg($this->id_offre, PDO::PARAM_INT);
            $values[] = new Arg($tag, PDO::PARAM_STR);
        }
        $stmt = DB\insert_into_multiple(DB\Table::Galerie, ['id_offre', 'id_image'], $values);
        notfalse($stmt->execute());

        $this->db_tags = $this->tags;
    }

    /**
     * @inheritDoc
     */
    function getIterator(): Traversable
    {
        return new ArrayIterator(array_keys($this->tags));
    }
}
