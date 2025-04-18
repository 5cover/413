<?php
require_once 'db.php';
require_once 'Equatable.php';

/**
 * @implements Equatable<Galerie>
 * @property-read Image[] $images
 */
final class Galerie implements Equatable
{
    function __get(string $name): mixed
    {
        return match ($name) {
            'images' => $this->images,
        };
    }

    /**
     * @var Image[]
     */
    private array $images = [];

    function __construct(
        private readonly Offre $offre,
    ) {
        if ($this->offre->id !== null) {
            $stmt = notfalse(DB\connect()->prepare('select * from ' . self::TABLE . ' join _image i on i.id = id_image where id_offre = ?'));
            DB\bind_values($stmt, [1 => [$this->offre->id, PDO::PARAM_INT]]);
            notfalse($stmt->execute());
            while (false !== $row = $stmt->fetch()) {
                $this->images[] = new Image(
                    $row['id_image'],
                    $row['taille'],
                    $row['mime_subtype'],
                    $row['legende']
                );
            }
        }
    }

    function add(Image $image): void
    {
        $this->images[] = $image;
        $this->insert_galerie($image);
    }

    function remove(Image $image): void
    {
        $this->images = array_diff($this->images, [$image]);
        if (null !== $args = $this->args($image)) {
            notfalse(DB\delete_from(self::TABLE, $args));
        }
    }

    function push_to_db(): void
    {
        foreach ($this->images as $image) {
            $this->insert_galerie($image);
        }
    }

    /**
     * @inheritDoc
     */
    function getIterator(): Traversable
    {
        return new ArrayIterator($this->images);
    }

    private function args(Image $image): ?array
    {
        if ($this->offre->id === null) return null;
        $image->push_to_db();
        return [
            'id_offre' => [$this->offre->id, PDO::PARAM_INT],
            'id_image' => [$image->id, PDO::PARAM_INT],
        ];
    }

    /**
     * @inheritDoc
     */
    function equals(mixed $other): bool
    {
        return $other->images === $this->images;
    }

    private function insert_galerie(Image $image): void
    {
        if (null !== $args = $this->args($image)) {
            notfalse(DB\insert_into(self::TABLE, $args));
        }
    }

    const TABLE = '_galerie';
}
