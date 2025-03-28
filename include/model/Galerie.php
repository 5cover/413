<?php

use DB\Arg;
use DB\BinOp;
use DB\BinaryClause;
use DB\InListClause;

require_once 'db.php';

/**
 * @property-read array<int, ImageFast> $images
 */
final class Galerie
{
    function __get(string $name): array
    {
        return match ($name) {
            'images' => $this->images,
        };
    }

    /**
     * @var array<int, ImageFast>
     */
    private array $images = [];

    /**
     * @var array<int, true>
     */
    private array $db_images = [];

    function __construct(
        private readonly int $id_offre,
    ) {
        $stmt = DB\select(DB\Table::ImageFromGalerie, ['*'], [
            new BinaryClause('id_offre', BinOp::Eq, $this->id_offre, PDO::PARAM_INT)
        ]);
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $this->images[$row->id] = new ImageFast(
                $row->id,
                ImageData::parse($row),
            );
            $this->db_images[$row->id] = true;
        }
    }

    function add(ImageFast $image): void
    {
        $this->images[$image->id] = $image;
    }

    function remove(ImageFast $image): void
    {
        unset($this->images[$image->id]);
    }

    function push(): void
    {
        // Compute diff
        $to_insert = array_diff_key($this->images, $this->db_images);     // in memory, not in DB
        $to_delete = array_diff_key($this->db_images, $this->images);     // in DB, not in memory

        // todo: let's just hope that this doesn't fail halfway or the caller is in a transaction block

        if (!$to_insert and !$to_delete) return;

        // Delete removed
        $stmt = DB\delete(DB\Table::Galerie, [
            new BinaryClause('id_offre', BinOp::Eq, $this->id_offre, PDO::PARAM_INT),
            new InListClause('id_image', array_keys($to_delete), PDO::PARAM_INT),
        ]);
        notfalse($stmt->execute());

        // Insert new
        $values = [];
        foreach ($to_insert as $id_image => $_) {
            $values[] = new Arg($this->id_offre, PDO::PARAM_INT);
            $values[] = new Arg($id_image, PDO::PARAM_INT);
        }
        $stmt = DB\insert_into_multiple(DB\Table::Galerie, ['id_offre', 'id_image'], $values);
        notfalse($stmt->execute());

        $this->db_images = array_fill_keys(array_column($this->images, 'id'), true);
    }

}
