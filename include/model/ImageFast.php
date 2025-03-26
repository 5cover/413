<?php
require_once 'db.php';

final class ImageFast
{
    function __construct(
        public int $id,
        public int $taille,
        public string $mime_subtype,
        public ?string $legende,
    ) { }

    /**
     * @var array<int, self>
     */
    private static $cache = [];

    /**
     * Récupère une image de la BDD.
     * @param int $id L'ID de l'image.
     */
    static function get(int $id): self|false
    {   
        if (isset(self::$cache[$id])) return self::$cache[$id];
        $stmt = self::select('where id=?');
        DB\bind_values($stmt, [1 => [$id, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return self::$cache[$id] = $row === false ? false : new self(
            $row->id,
            $row->taille,
            $row->mime_subtype,
            $row->legende,
        );
    }

    function src(): string
    {
        return "/images_utilisateur/$this->id.$this->mime_subtype";
    }

    private static function select(string $query_rest = '')
    {
        return notfalse(DB\connect()->prepare("select * from _image $query_rest"));
    }
}
