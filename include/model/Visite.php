<?php

use function DB\bind_values;

require_once 'model/Offre.php';
require_once 'model/Langue.php';

/**
 * @inheritDoc
 * @property-read Langue[] $langues
 */
final class Visite extends Offre
{
    protected static function fields()
    {
        return parent::fields() + [
            'indication_duree' => [null, 'indication_duree', PDO::PARAM_STR],
        ];
    }

    private ?array $langues = null;

    function __get(string $name): mixed
    {
        if ($name === 'langues' and $this->langues === null and $this->id !== null) {
            $stmt = DB\connect()->prepare('select _langue.* from _langue_visite inner join _langue on _langue.code = _lange_visite.code_langue where id_visite = ?');
            bind_values($stmt, [1 => [$this->id, PDO::PARAM_INT]]);
            $stmt->execute();
            $this->langues = array_map(fn($row) => new Langue($row['code'], $row['libelle']), $stmt->fetchAll());
        }
        return parent::__get($name);
    }

    function push_to_db(): void
    {
        parent::push_to_db();
        if ($this->langues !== null) {
            $stmt = DB\connect()->prepare('delete from _language_visite where id_visite = ?');
            bind_values($stmt, [1 => [$this->id, PDO::PARAM_INT]]);
            $stmt->execute();

            if (count($this->langues) > 0) {
                $stmt = DB\connect()->prepare('insert into _langue_visite (code_langue, id_visite) values (?,?)'
                    . str_repeat(',(?,?)', count($this->langues) - 1));
                $i    = 1;
                foreach ($this->langues as $l) {
                    notfalse($stmt->bindValue($i++, $l->code));
                    notfalse($stmt->bindValue($i++, $this->id));
                }
                $stmt->execute();
            }
        }
    }

    function __construct(
        array $args_offre,
        public Duree $indication_duree,
    ) {
        parent::__construct(...$args_offre);
    }

    const CATEGORIE = 'visite';
    const TABLE     = 'visite';
}
