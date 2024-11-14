# SQL et Données

## Départements

[Dataset du gouvernement](https://www.data.gouv.fr/fr/datasets/communes-de-france-base-des-codes-postaux/) : [CSV](dataset/departements-france.csv)

### 1. Créer la table

```sql
create table _departement (
  code_departement text,
  nom_departement text,
  code_region text,
  nom_region text
);
```

### 2. WbImport

```sql
 -table=_departement -emptyStringIsNull -header -delimiter=',' -type=text -file='dataset/departements-france.csv'
```

### 3. Sélection

```sql
select
    code_departement numero,
    nom_departement nom
from
    _departement;
```

## Communes

[Dataset du gouvernement](https://www.data.gouv.fr/fr/datasets/communes-de-france-base-des-codes-postaux/) : [CSV](dataset/communes-departement-region.csv)

### 1. Créer la table

```sql
create table _commune (
  code_commune_INSEE text,
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
  code_region text,
  nom_region text
);
```

### 2. WbImport

```sql
WbImport -type=text -table=_commune -emptyStringIsNull -header -delimiter=',' -file='dataset/communes-departement-region.csv'
```

### 3. Sélection

Communes

```sql
select distinct on (code_commune_insee)
    code_commune code,
    code_departement numero_departement,
    nom_commune_complet nom
from
    _commune
where
    code_departement in (select code_departement from _departement);
```

Codes postaux

```sql
select distinct
    code_commune code,
    code_departement numero_departement,
    code_postal
from
    _commune
where
    code_departement in (select code_departement from _departement);
```

### 4. Copie des résultats en SQL INSERT

1. <kbd>Ctrl+A</kbd> dans le panneau des résultats dans SQL workbench
2. Clic droit > Copy Selected > As SQL Insert
3. Coller
