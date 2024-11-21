# Conventions

## Général

### Nommage

Réutiliser les mêmes noms autant que possible (SQL, PHP, JS, CSS, HTML id, HTML input name...), même si cela signifie enfreindre les conventions de nommage du language actuel.

Cela réduit la quantité de mappage mental à faire et facilite la recherche inter-fichiers.

L'infraction des conventions de nommage aura au moins un côté positif&nbsp;: celui d'indiquer que ce nom à un caractère spécial, et référence un concept inter-languages.

## PHP

### Gestions des errreurs

**Ne jamais utiliser `exit()` ou `die()` pour quitter en cas d'erreur**. Cela empêche le rollback dans `transaction()`, on court donc le risque d'avoir des données incohérentes. À a place, jeter une exception&nbsp;:

```php
throw new Exception("Message de l'erreur");
```

## Arguments&nbsp;: `$_GET`, `$_POST` et `$_FILES`

Tout script PHP recevant des arguments doit avant toute autre opération les aggréger dans un tableau associatif en utilisant la fonction `getarg()`. Exemple&nbsp;:

```php
$args = [
    'adresse_commune' => getarg($_POST, 'adresse_commune'),
    'description' => getarg($_POST, 'description'),
    'resume' => getarg($_POST, 'resume'),
    'age_requis' => getarg($_POST, 'age_requis', arg_filter(FILTER_VALIDATE_INT, ['min_range' => 1]), required: false),
    'adresse_complement_numero' => getarg($_POST, 'adresse_complement_numero', required: false),
    // ...
]
```

Les clés du tableau `$args` représentent le nom de l'argument côté PHP, tandis que le le nom passé à `getarg()` représente le nom de l'argument côté front-end (probablement une valeur d'attribut `name` de `input`).

Pourquoi faire ça&nbsp;?

- Identifier facilement tous les arguments attendus par un script
- Regrouper la logique de récupération et validation des arguments à un seul endroit
- Afficher des erreurs compréhensibles en HTML (à la fois pour l'utilisateur et le débogage) quand un argument est manquant ou invalide.

Dans l'absolu, il ne doit y avoir acune accès à `$_GET`, `$_POST` ou `$_FILES` après le remplissage de `$args`

## HTML

### Ordre des attributs

#### `<input>`

1. `form`
2. `id`
3. `name`
4. `type`
5. autre
6. `required`

#### `<textarea>`

1. `id`
2. `name`
3. autre
4. `required`

## SQL

### Nommage

**Lowercase** everything that is case-insensitive: keywords, table, attributes&hellip;

Use **snake_case**.

#### Paramètres

Utiliser le préfixe `p_` pour distinguer les paramètres lors de la substitution dans les requêtes. Exemple&nbsp;: `p_id_offre`.

#### Attributs et variables temporels

type|nom|exemple
-|-|-
`timestamp`| `{participe passé}_le`|`cree_le`, `ouvert_le`
`time`|`heure_{nom}`|`heure_creation`, `heure_ouverture`
`date`|`date_{nom}`|`date_creation`, `date_ouverture`
`interval`|`duree_{nom}`, `{participe passé}_pendant`|`duree_creation`, `duree_ouverture`, `cree_pendant`, `ouvert_pendant`

#### Contraintes

`{nom table}` ignores 1 leading underscore `_` in the table name.

type de contrainte|nom attribut|nom contrainte|explication
-|-|-
clé primaire|`id`|`{nom table}_pk`|
clé étrangère|`{attribut référencé}[_{nom table référencée}][_{rôle}]`|`{nom table}_fk_{nom table référencée}[_{rôle}]`|La partie `_{rôle}` est optionelle.
clé étrangère représentant un héritage|`{nom table}_inherits_{nom table référencée}`

### Ordre des attributs

La **clé primaire** doit toujours être placée en **premier**.

Les attributs `not null` doivent être placés **avant** les attributs nullables.

### Placement des contraintes

Placer les contraintes aussi près du ou des attributs concernés que possible.

Pour les contraintes concernant plusieurs attribut, placer la contrainte just sous les attributs et les séparer de lignes vides:

```sql
-- ...
precision_ext varchar(255),

latitude decimal,
longitude decimal,
check ((latitude is null) = (longitude is null)),

commune_code_insee char(5) not null,
-- ...
```

### Contraintes nommées

Lorsqu'un attribut est associée à une ou plusieurs contraintes nommées (commençant avec le mot-clé `constraint`), elles sont identées sur les lignes suivantes

```sql
id int
    constraint professionnel_pk primary key
    constraint professionnel_inherits_compte foreign key (email) references _compte(email),
```

### Héritage

Réutiliser la clé primaire de la table de base pour la clé primaire de la table enfant quand cela est possible. Cela évite d'avoir 2 clés `serial` sans valeur sémantique.

Les contraintes d'héritage doivent être spécifiées le plus tôt possible après la clé primaire.

### Valeurs par défaut

Une table pour laquelle une vue existe ne doit pas avoir de contraintes `default`, à la place, on utilise des `coalesce` dans son trigger `insert`.

### [Common Table Expressions](https://www.postgresql.org/docs/current/queries-with.html) ne retournant pas de valeur

Les CTE ne retournant pas de valeur ou dont la valeur est inutilisée doivent quand même avoir un nom unique, bien que celui si ne serve à rien. Utiliser un nommage incrémental tel `s1`, `s2`, `s3`... pour *statement 1*, *statement 2*, *statement 3*.

```sql
-- ...
with id_offre as (
        -- ...
    ), s1 as (
        -- ...
    ), s2 as (
        -- ...
    )
-- ...
