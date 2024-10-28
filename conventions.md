# Conventions

## SQL

### Naming

**Lowercase** everything that is case-insensitive: keywords, table, attributes&hellip;

Use **snake_case**.

### Nommage des contraintes

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
# Conventions

## SQL

### Naming

**Lowercase** everything that is case-insensitive: keywords, table, attributes&hellip;

Use **snake_case**.

### Nommage des contraintes

`{nom table}` ignores 1 leading underscore `_` in the table name.

type de contrainte|nom|explication
-|-|-
clé primaire|`{nom table}_pk`|
clé étrangère|`{nom table}_fk_{nom table référencée}[__{rôle}]`|La partie `__{rôle}` est optionelle. Le double undescore `__` permet de séparer `{rôle}` d'un `{nom table référencée}` en plusieurs mots
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
id_professionnel serial
    constraint professionnel_pk primary key
    constraint professionnel_inherits_compte foreign key (email) references _compte(email),
```

### Héritage

Réutiliser la clé primaire de la table de base pour la clé primaire de la table enfant quand cela est possible. Cela évite d'avoir 2 clés `serial` sans valeur sémantique.

Les contraintes d'héritage doivent être spécifiées le plus tôt possible après la clé primaire.
