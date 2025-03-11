<?php

require_once 'db.php';
require_once 'model/Offre.php';
require_once 'model/Date.php';
require_once 'model/Membre.php';

/**
 * @property-read ?int $id L'ID. `null` si cet avis n'existe pas dans la BDD.
 * @property-read ?FiniteTimestamp $publie_le Calculé. `null` si cet avis n'existe pas dans la BDD.
 * @property-read ?bool $lu Calculé. `null` si cet avis n'existe pas dans la BDD.
 */
class Avis extends Model
{
    protected static function key_fields()
    {
        return [
            'id' => [null, 'id', PDO::PARAM_INT],
        ];
    }

    protected static function computed_fields()
    {
        return [
            'publie_le' => [FiniteTimestamp::parse(...), 'publie_le', PDO::PARAM_STR],
            'lu'        => [null, 'lu', PDO::PARAM_BOOL],
        ];
    }

    protected static function fields()
    {
        return [
            'commentaire'      => [null, 'commentaire', PDO::PARAM_STR],
            'note'             => [null, 'note', PDO::PARAM_INT],
            'date_experience'  => [null, 'date_experience', PDO::PARAM_STR],
            'contexte'         => [null, 'contexte', PDO::PARAM_STR],
            'id_membre_auteur' => [fn($x) => $x?->id, 'membre_auteur', PDO::PARAM_INT],
            'id_offre'         => [fn($x) => $x->id, 'offre', PDO::PARAM_INT],
        ];
    }

    function __construct(
        protected ?int $id,
        public string $commentaire,
        public int $note,
        public Date $date_experience,
        public string $contexte,
        public ?Membre $membre_auteur,
        public Offre $offre,
        //
        protected ?bool $lu                   = null,
        protected ?FiniteTimestamp $publie_le = null,
    ) {}

    static function from_db(int $id_avis): self|false
    {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' where ' . static::TABLE . '.id = ?'));
        DB\bind_values($stmt, [1 => [$id_avis, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : self::from_db_row($row);
    }

    /**
     * Retourne le seul avis qu'un membre est autorisé à publier sur une offre, ou `false` si le membre n'a pas encore déposé d'avis.
     * @param int $id_membre_auteur
     * @param int $id_offre
     * @return Avis|false
     */
    static function from_db_one(int $id_membre_auteur, int $id_offre): self|false
    {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' where ' . static::TABLE . '.id_membre_auteur = ? and ' . static::TABLE . '.id_offre = ?'));
        DB\bind_values($stmt, [1 => [$id_membre_auteur, PDO::PARAM_INT], 2 => [$id_offre, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : self::from_db_row($row);
    }

    /**
     * Récupère les avis de la BDD.
     * @param ?int $id_membre_auteur
     * @param ?int $id_offre
     * @param ?bool $blackliste
     * @return Iterator<int, self>
     */
    static function from_db_all(?int $id_membre_auteur = null, ?int $id_offre = null, ?bool $blackliste = null): Iterator
    {
        $args = DB\filter_null_args([
            'id_membre_auteur' => [$id_membre_auteur, PDO::PARAM_INT],
            'id_offre'         => [$id_offre, PDO::PARAM_INT],
            'blackliste'       => [$blackliste, PDO::PARAM_BOOL]
        ]);
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . DB\where_clause(DB\BoolOperator::AND, array_keys($args), static::TABLE)));
        DB\bind_values($stmt, $args);
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield $row['id'] => self::from_db_row($row);
        }
    }

    private static function from_db_row(array $row): self
    {
        self::require_subclasses();
        $args_avis = [
            $row['id'],
            $row['commentaire'],
            $row['note'],
            Date::parse($row['date_experience']),
            $row['contexte'],
            mapnull($row['id_membre_auteur'], Membre::from_db(...)),
            Offre::from_db($row['id_offre']),
            $row['lu'],
            FiniteTimestamp::parse($row['publie_le']),
        ];

        $id_restaurant = $row['id_restaurant'] ?? null;
        return $id_restaurant
            ? new AvisRestaurant(
                $args_avis,
                $row['note_cuisine'],
                $row['note_service'],
                $row['note_ambiance'],
                $row['note_qualite_prix'],
            )
            : new self(...$args_avis);
    }

    private static function make_select(): string
    {
        self::require_subclasses();
        return 'select
            ' . static::TABLE . '.id,
            ' . static::TABLE . '.commentaire,
            ' . static::TABLE . '.note,
            ' . static::TABLE . '.date_experience,
            ' . static::TABLE . '.contexte,
            ' . static::TABLE . '.id_membre_auteur,
            ' . static::TABLE . '.id_offre,
            ' . static::TABLE . '.lu,
            ' . static::TABLE . '.publie_le,

            v.id_restaurant,
            v.note_cuisine,
            v.note_service,
            v.note_ambiance,
            v.note_qualite_prix
         from ' . self::TABLE . '
            left join ' . AvisRestaurant::TABLE . ' v using (id)';
    }

    private static function require_subclasses(): void
    {
        require_once 'model/AvisRestaurant.php';
    }

    const TABLE = 'avis';
}
