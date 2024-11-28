<?php
require_once 'const.php';
require_once 'util.php';

final class Image
{
    private ?int $id;
    private ?string $tmp_name;
    readonly int $taille;
    readonly string $mime_subtype;
    readonly ?string $legende;

    private const TABLE = '_image';

    function __construct(
        int $taille,
        string $mime_subtype,
        ?string $legende,
        ?string $tmp_name = null,
        ?int $id = null,
    ) {
        $this->taille = $taille;
        $this->mime_subtype = $mime_subtype;
        $this->legende = $legende;
        $this->id = $id;
        $this->tmp_name = $tmp_name;
    }

    static function from_db(int $id_image): Image|false
    {
        $stmt = notfalse(DB\connect()->prepare('select taille, mime_subtype, legende from ' . self::TABLE . ' where id = ?'));
        DB\bind_values($stmt, [1 => [$id_image, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : new Image(
            $row['taille'],
            $row['mime_subtype'],
            $row['legende'],
            $id_image,
        );
    }

    /**
     * Déplace cette image téléversée vers le dossier des images utilisateur.
     * @throws \LogicException
     * @return void
     */
    function move_uploaded_image()
    {
        if (!$this->tmp_name) {
            throw new LogicException("Impossible de déplacer l'image. Soit l'image a déjà été déplacée, soit elle provient de la BDD");
        }
        notfalse(move_uploaded_file($this->tmp_name, DOCUMENT_ROOT . $this->upload_location()));
        $this->tmp_name = null;
    }

    function display_location(): string
    {
        return $this->tmp_name
            ? notfalse(self::image_data_uri($this->tmp_name))
            : $this->upload_location();
    }

    function push_to_db()
    {
        $args = [
            'taille' => [$this->taille, PDO::PARAM_INT],
            'mime_subtype' => [$this->mime_subtype, PDO::PARAM_STR],
            'legende' => [$this->legende, PDO::PARAM_STR],
        ];
        if ($this->id === null) {
            $stmt = DB\insert_into_returning_id(self::TABLE, $args);
            DB\bind_values($stmt, $args);
            notfalse($stmt->execute());
            $this->id = notfalse($stmt->fetchColumn());
        } else {
            $stmt = DB\update(self::TABLE, $args, [
                'id' => [$this->id, PDO::PARAM_INT]
            ]);
            notfalse($stmt->execute());
        }
    }

    private function upload_location(): string
    {
        return "/images_utilisateur/$this->id.$this->mime_subtype";
    }

    /**
     * Retourne la représentation data-uri du fichier image spécifié.
     * @param string $path Chemin du fichier
     * @param bool $forceBase64 Toujours utiliser la forme Base64, utilisé uniquement pour les SVGs
     * @return string|false La chaine data-uri, ou false en cas d'erreur
     */
    private static function image_data_uri(string $path, bool $forceBase64 = false): string|false
    {
        // Vérifie si le fichier est lisible
        if (!$path || !@is_readable($path)) return false;

        // Lit le contenu du fichier
        $data = file_get_contents($path);
        if ($data === false) return false;

        // Supprime le marqueur utf8-bom du contenu s'il est présent
        if ("\u{FEFF}" == substr($data, 0, 3)) $data = substr($data, 3);

        // Détermine le type MIME du contenu
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (!$finfo) return false;
        $mime = finfo_buffer($finfo, $data);
        finfo_close($finfo);
        if (!$mime) return false;

        // Correction du type MIME dans certains cas
        if ($mime == 'image/svg') $mime = 'image/svg+xml';
        if ($mime == 'text/xml') $mime = 'image/svg+xml';

        // Correction du code SVG si nécessaire
        if ($mime == 'image/svg+xml') {
            if ('<svg' != substr($data, 0, 4)) $data = substr($data, strpos($data, '<svg'));
            if (strpos($data, 'http://www.w3.org/2000/svg') === false) $data = str_replace('<svg', '<svg xmlns="http://www.w3.org/2000/svg"', $data);
        }

        // Génération data-uri en texte URL
        if ($mime == 'image/svg+xml' && !$forceBase64) {
            $data = trim($data);
            $data = preg_replace('/\s+/', ' ', $data);
            $data = preg_replace('/"/', "'", $data);
            $data = rawurlencode($data);
            $data = str_replace(['%20', '%27', '%2C', '%3D', '%3A', '%2F'], [' ', "'", ',', '=', ':', '/'], $data);

            $result = "data:$mime,";
            $result .= $data;
            return $result;
        }

        // Génération data-uri en Base64
        $result = "data:$mime;base64,";
        $result .= base64_encode($data);
        return $result;
    }
}