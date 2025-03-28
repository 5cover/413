<?php
require_once 'db.php';

final class ImageData
{
    function __construct(

    public int $taille,
    public string $mime_subtype,
    public ?string $legende,
    ){}

    static function parse(object $row): self {
        return new self ($row->taille,
            $row->mime_subtype,
            $row->legende,
        );
    }
}

final class ImageFast
{
    function __construct(
        public readonly int $id,
        public ImageData $data,
    ) {}

    /**
     * @var array<int, self|false>
     */
    private static array $cache = [];

    // UPDATE

    function push(): void
    {
        $stmt = DB\update(DB\Table::Image, [
            'taille' => [$this->data->taille, PDO::PARAM_INT],
            'mime_subtype' => [$this->data->mime_subtype, PDO::PARAM_STR],
            'legende' => [$this->data->legende, PDO::PARAM_STR],
        ], ['id' => [$this->id, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
    }

    // READ

    /**
     * Récupère une image de la BDD.
     * @param int $id L'ID de l'image.
     */
    static function from_db(int $id): self|false
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];

        $stmt = notfalse(DB\connect()->prepare('select * from ' . DB\Table::Image->value . ' where id=?'));
        DB\bind_values($stmt, [1 => [$id, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch(PDO::FETCH_OBJ);

        return self::$cache[$id] = $row === false ? false : self::from_db_row($row);
    }

    /**
     * @return Generator<self>
     */
    public static function get_galerie(int $id_offre): Generator
    {
        $stmt = notfalse(DB\connect()->prepare('select _image.* from _galerie inner join _image on _image.id=_galerie.id_image where _galerie.id_offre=?'));
        DB\bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch(PDO::FETCH_OBJ)) {
            yield self::$cache[$row->id] = self::from_db_row($row);
        }
    }

    function src(): string
    {
        return "/images_utilisateur/$this->id.{$this->data->mime_subtype}";
    }

    private static function from_db_row(object $row): self
    {
        return new self($row->id, ImageData::parse($row));
    }
}
