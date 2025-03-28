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

new system:

Type of attributes

- R : regular
- P : primary key
- C : computed
  
- Model\Adresse -> Persistent instance
  - private constructor 
  - static function Model::insert(R) -> Create
    - creates an instance, pushes it and returns it
  - static function Mpdel::pull(P) -> Read (pull)
  - function Model::push() -> Update
    - update attributes R in the DB based on (possibly) mutated attrbiutes. P and C are readonly.
  - function Model::delete() -> Delete
    - makes P invalid

R attributes are in a class ModelData (e.g. AddressData..)

- ModelData does not know P (primary keys).
- ModelData must be flat and must not reference other ModelData.
- ModelData must not assume assume the existence of a particular database or data persistence system.
