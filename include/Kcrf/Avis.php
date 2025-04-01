<?php
namespace Kcrf;

require_once 'DB/db.php';
require_once 'DB/Date.php';
require_once 'Kcrf/OffreFast.php';
require_once 'Kcrf/Membre.php';

use DB;
use DB\Arg;
use DB\BinaryClause;
use DB\BinOp;
use DB\FiniteTimestamp;
use PDO;
use Generator;

final class AvisData
{
    function __construct(
            // Foreign
            ?int $id_membre_auteur,
            // Regular
            public string $commentaire,
            public string $note,
            public DB\Date $date_experience,
            public ?string $contexte,
            public int $likes,
            public int $dislikes,
            public bool $lu,
    )
    {
    }

    static function parse(object $row): self {
        return new self(
            $row->id_membre_auteur,
            $row->commentaire,
            $row->note,
            DB\Date::parse($row->date_experience),
            $row->contexte,
            $row->likes,
            $row->dislikes,
            $row->lu,
        );
    }
}

/**
 * Un commentaire sur une offre par un membre.
 */
class Avis
{
    const TABLE = DB\Table::Avis;

    protected function __construct(
        // Key
        readonly int $id,
        // Computed
        readonly FiniteTimestamp $publie_le,
        public AvisData $data,
    ) {
    }

    /**
     * @var array<int, self|false>
     */
    private static array $cache = [];

    static function from_db(int $id): self|false
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];

        $stmt = DB\select(self::TABLE, ['*'], [new BinaryClause('id', BinOp::Eq, $id, PDO::PARAM_INT)]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return self::$cache[$id] = $row === false ? false : self::from_db_row($row);
    }

    /**
     * Retourne le seul avis qu'un membre est autorisé à publier sur une offre, ou `false` si le membre n'a pas encore déposé d'avis.
     * @param int $id_membre_auteur
     * @param int $id_offre
     * @return Avis|false
     */
    static function from_db_one(int $id_membre_auteur, int $id_offre): self|false
    {
        $stmt = DB\select(self::TABLE, ['*'], [
            new BinaryClause('id_membre_auteur', BinOp::Eq, $id_membre_auteur, PDO::PARAM_INT),
            new BinaryClause('id_offre', BinOp::Eq, $id_offre, PDO::PARAM_INT),
        ]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false :  self::$cache[$row->id] = self::from_db_row($row);
    }

    /**
     * Récupère les avis de la BDD.
     * @param ?int $id_membre_auteur
     * @param ?int $id_offre
     * @param ?bool $blackliste
     * @return \Generator<int, self>
     */
    static function from_db_all(?int $id_membre_auteur = null, ?int $id_offre = null, ?bool $blackliste = null): Generator
    {
        $where = [];
        if (null !== $id_membre_auteur) $where[] = new BinaryClause('id_membre_auteur', BinOp::Eq, $id_membre_auteur, PDO::PARAM_INT);
        if (null !== $id_offre) $where[] = new BinaryClause('id_offre', BinOp::Eq, $id_offre, PDO::PARAM_INT);
        if (null !== $blackliste) $where[] = new BinaryClause('blackliste', BinOp::Eq, $blackliste, PDO::PARAM_BOOL);

        $stmt = DB\select(self::TABLE, ['*'], $where);
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield $row->id => self::$cache[$row->id] = self::from_db_row($row);
        }
    }

    private static function from_db_row(object $row): self
    {
        return new self(
            $row->id,
            $row->publie_le,
            AvisData::parse($row),
        );
    }

    function blacklist(FiniteTimestamp $duree_ban): void
    {
        $stmt = DB\insert_into(DB\Table::Blacklist, [
            'id' => new Arg($this->id, PDO::PARAM_INT),
            'fin_blacklist' => new Arg($duree_ban),
        ]);
        notfalse($stmt->execute());
    }

    function marquerCommeLu(): void
    {
        $stmt = DB\update(self::TABLE, [
            'lu' => new Arg(true, PDO::PARAM_BOOL),
        ], [
            new BinaryClause('id', BinOp::Eq, $this->id, PDO::PARAM_INT),
        ]);

        DB\bind_values($stmt, [1 => [$this->id, PDO::PARAM_INT]]);
        notfalse($stmt->execute());

        $this->data->lu = true;
    }

    // todo: KCRF this
    static function getAvisNonLus(int $id_pro): array
    {
    $stmt = DB\connect()->prepare("
        SELECT a.id, a.commentaire, a.publie_le, m.pseudo, o.id AS auteur
        FROM _avis a
        JOIN _offre o ON a.id_offre = o.id
        JOIN _membre m ON a.id_membre_auteur = m.id
        WHERE o.id_professionnel = ? AND a.lu = FALSE
        ORDER BY a.publie_le DESC
    ");
    DB\bind_values($stmt, [1 => [$id_pro, PDO::PARAM_INT]]);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
