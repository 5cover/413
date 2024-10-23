# SQL et Données

## Départements

[Dataset du gouvernement](https://www.data.gouv.fr/fr/datasets/communes-de-france-base-des-codes-postaux/) : [CSV](dataset/departements-france.csv)

### 1. Créer la table

```sql
create table _departements {
  code_departement text,
  nom_departement text,
  code_region text,
  nom_region text
);
```

### 2. WbImport

```sql
WbImport -type=text -file='dataset/departements-france.csv' -table=_departements -emptyStringIsNull -header -delimiter=','
```

### 3. Sélection

```sql
select
    code_departement,
    nom_departement
from
    _departement;
```

## Communes

[Dataset du gouvernement](https://www.data.gouv.fr/fr/datasets/communes-de-france-base-des-codes-postaux/) : [CSV](dataset/communes-departement-region.csv)

### 1. Créer la table

```sql
create table _communes (
    code_commune_insee text,
    nom_commune_postal text,
    code_postal text,
    libelle_acheminement text,
    ligne_5 text,
    latitude text,
    longitude text,
    code_commune text,
    article text,
    nom_commune text,
    nom_commune_complet text,
    code_departement text,
    nom_departement text,
    code_region text
);
```

### 2. WbImport

```sql
WbImport -type=text -file='dataset/communes-departement-region.csv' -table=_communes -emptyStringIsNull -header -delimiter=','
```

### 3. Sélection

```sql
select
    code_commune_insee,
    nom_commune_complet,
    code_departement,
    code_postal
from
    _communes
where
    code_commune_insee is not null
    and nom_commune_complet is not null
    and code_departement is not null
    and code_postal is not null;
```

### 4. Copie des résultats en SQL INSERT

1. <kbd>Ctrl+A</kbd> dans le panneau des résultats dans SQL workbench
2. Clic droit > Copy Selected > As SQL Insert
3. Coller
