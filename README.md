# PACT - Équipe 413

![CodeRabbit Pull Request Reviews](https://img.shields.io/coderabbit/prs/github/5cover/413?utm_source=oss&utm_medium=github&utm_campaign=5cover%2F413&labelColor=171717&color=FF570A&link=https%3A%2F%2Fcoderabbit.ai&label=CodeRabbit+Reviews)

SAÉ 3&4 BUT2 2025

## Liens utiles

[Documentation Tchatator](https://5cover.github.io/413/)

[JIRA](https://bonjourceciestlenomdecesite.atlassian.net/jira/software/projects/C11/boards/34)

## Équipe

Raphaël Bardini

Romain Grandchamp

Maëlan Potier

Marius Chartier--Le Goff

Maël Gogdet

Benjamin Dumont-Girard

## Todo

- [ ] Composer autoload instead of include-path (remove it from phpstorm config)
- [ ] rename modelfast ::get* -> ::from_db*
- [ ] put all table name constants in enum Tables
- [ ] remove Fast suffix
- [ ] namespaces and directory organization
- [ ] one type per file
- [ ] periodes_ouverture is an R, not a C.
- [ ] use psr namespaces
- [ ] db query helpers -- no one single query spelled entirely. used views where there are joins: we should have to do Table->value enum  

## ORM: The KCRF system

Alright, let’s build you a proper **Context Seeder Prompt™**, tailored for serious conversations like yours. Think of it as a time capsule you can drop into a new chat to *reboot* the feel and content of a rich session.

---

### 🧠 Context Seeder Prompt Template

> Reclaim the ORM of perf*ORM*ance today!

Type of attributes

- K : key
- C : computed
- RF : regular, foreign keys values (not the object themselves)

K and C are immutable. RF is mutable.

Basic CRUD operations:

- private or protected constructor 
- Create: static function insert(RF): self
  - creates an instance, pushes it and returns it
- Read: static function from_db(K): self
- Update: function push()
  - update attributes RF in the DB.
- Delete: function Model::delete() 
  - makes K invalid. The caller better forget about this object.

Possible optimizations: Make RF able to track their own "dirty" state through __get and __set, so that the model can tailor the UPDATE query accordingly:
  
- Either a boolean "udpdate all" or "update none"
- Or a list of every modified column for even more efficiency

RF attributes are in a mutabled class called ModelData (e.g. AddressData..)

- ModelData does not know K.
- ModelData must be flat and must not reference other ModelData.
- ModelData must not assume the existence of a particular database or data persistence system.

### Inheritance

Only inheritance of Model classes is permitted (not ModelData)

Derived classes must start with the base class name for clarity: Offer > OfferVisit > OfferVisitGuided

To add more data -> Composition over inheritance -> Accept an OfferData, an OfferVisitData, and OfferVisitGuidedData.

Why not inheritance? To avoiding restating parent attributes in constructor and instanciation.

And as usual, limit inheritance levels. 2 seems like a good limit to keep things simple. The explicit base class prefix notation helps maintain this limit, because it forces you to think when you're about to write a long clas name (like OfferVisitGuided). 

### Optional

#### ModelKey

Composite primary keys (K has multiple attibutes) can be composed in a readonly "ModelKey" class.

#### ModelComputed

Computed attributes (C) can be composediin a readonly "ModelComputed" class. This is useful for DRY when building a Model instance from either:
 
- A SELECTed row
- The computed fields in a RETURNING clause of an INSERT INTO

The latter only needs to contain computed attributes. Other attributes are redundant as they are already provided to the INSERT INTO, so it would be wasteful to send back the same values

### Sample code (using 413's DB abstraction layer)

A simple Comment > CommentRestaurant

```php
class Comment
{
    protected function __construct(
        readonly int $id, // K
        readonly CommentComputed $computed, // C
        public CommentData $data // RF
    ) { }

    /**
     * @var array<int, self|false>
     */
    private static array $cache = [];

    // Create

    static function insert(CommentData $data) {
        $stmt = DB\insert_into(
            DB\Table::Comment,
            $data->to_args(),
            columns_returning: array_merge(['id'], CommentComputed::COLUMNS),
        );
        notfalse($stmt->execute());
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        assert (!(self::$cache[$row->id] ?? false), 'new row already in cache somehow');

        return self::$cache[$row->id] = new self($row->id, $data, CommentComputed::parse($row));
    }

    // Read

    static function from_db(int $id): self|false
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];

        $stmt = DB\select(
            DB\Table::Comment,
            ['*'],
            [new BinaryClause('id', BinOp::Eq, $id, PDO::PARAM_INT)],
        );
        notfalse($stmt->execute());
        $row = $stmt->fetch(PDO::FETCH_OBJ);

        return self::$cache[$id] = $row === false ? false : self::from_db_row($row);
    }

    static function from_db_all(): Generator
    {
        $stmt = DB\select(DB\Table::Offre, ['*']);
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch(PDO::FETCH_OBJ)) {
            yield self::from_db_row($row);
        }
    }

    // Update

    function push(): void
    {
        $stmt = DB\update(DB\Table::Comment, $this->data->to_args(), [
            new BinaryClause('id', BinOp::Eq, $this->id, PDO::PARAM_INT),
        ]);
        notfalse($stmt->execute());
    }

    // Delete

    function delete(): void
    {
        $stmt = DB\delete(DB\Table::Comment, [
            new BinaryClause('id', BinOp::Eq, $this->id, PDO::PARAM_INT),
        ]);
        notfalse($stmt->execute());
        self::$cache[$this->id] = false;
    }

    // Internals

    private static function from_db_row(object $row): self
    {
        return self::$cache[$row->id] ??= new self(
            $row->id,
            OffreData::parse($row),
            OffreComputed::parse($row),
        );
    }
}

final class CommentData
{
    function __construct(
        // Foreign key
        public int $id_author,

        // Regular
        public string $contents,
        public int $n_likes,
        public int $n_dislikes,
    ){}

    static function parse(object $row): self {
        return new self(
            $row->id_author,
            $row->contents,
            $row->n_likes,
            $row->n_dislikes,
        );
    }

    /**
     * @return array<string, Arg>
     */
    function to_args(): array {
        return [
            'id_author' => new Arg($this->id_author, PDO::PARAM_INT),
            'contents' => new Arg($this->contents), // default is PDO::PARAM_STR
            'n_likes' => new Arg($this->n_likes, PDO::PARAM_INT),
            'n_dislikes' => new Arg($this->n_dislikes, PDO::PARAM_INT),
        ];
    }
}

final readonly class CommentComputed
{
    private function __construct(
        public FiniteTimestamp     $published_on,
    ) { }

    static function parse(object $row): self
    {
        return new self(
            FiniteTimestamp::parse($row->published_on),
        );
    }

    const COLUMNS = [
        'published_on',
    ];
}

final class CommentRestaurant extends Comment
{
    protected function __construct(
        int $id,
        CommentComputed $computed,
        CommentData $data,

        public CommentRestaurantData $restaurant_data,
    )
    {
        parent::__construct($id, $computed, $data);
    }

    /**
     * @var array<int, self|false>
     */
    private static array $cache = [];

    // Create

    static function insert_restaurant(CommentData $data, CommentRestaurantData $restaurant_data): self
    {
        $stmt = DB\insert_into(
            DB\Table::CommentRestaurant,
            $data->to_args() + $restaurant_data->to_args(),
            array_merge(['id'], CommentComputed::COLUMNS),
        );
        notfalse($stmt->execute());
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        assert (!(self::$cache[$row->id] ?? false), 'new row already in cache somehow');

        return self::$cache[$row->id] = new self($row->id, CommentComputed::parse($row), $data, $restaurant_data);
    }

    // Read

    static function from_db(int $id): self
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];

        $stmt = DB\connect()->prepare('select * from ' .  DB\Table::Visite->value . ' where id=?');
        notfalse($stmt->execute());
        $row = $stmt->fetch(PDO::FETCH_OBJ);

        return self::$cache[$id] = $row === false ? false : self::from_db_row($row);
    }

    // ...

    // Update

    // ...

    // Delete

    // ...

    // Internals

    private static function from_db_row(object $row): self
    {
        return self::$cache[$row->id] ??= new self(
            $row->id,
            CommentComputed::parse($row),
            CommentData::parse($row),
            CommentRestaurantData::parse($row),
        );
    }
}

final readonly class CommentRestaurantData {
    function __construct(
        ?string $associated_yelp_review_url,
    )
    {
    }

    // parse implementation...

    // to_args implementation...
}
```
