<?php

/**
 * Un composant d'entrée d'un modèle.
 * @template T Le modèle entré
 */
abstract class Input
{
    protected readonly string $form_attr;
    protected readonly string $id_attr;
    /**
     * @param string $form_id l'ID du formulaire auquel appartient le contrôle. Pas nécéssaire de le spécifier si l'élément est déjà dans un `<form>`.
     * @param string $id L'ID de l'élément à ajouter. Optionnel, ne pas spécifier pour pas d'ID.
     * @param string $name L'attribut "name" à utiliser. En PHP, ce sera un tableau avec les différents champs du composants.
     */
    function __construct(
        readonly string $id = '',
        readonly string $name = '',
        readonly string $form_id = '',
        
    ) {
        $this->form_attr = $this->form_id ? 'form="' . h14s($this->form_id) . '"' : '';
        $this->id_attr = $this->id ? 'id="' . h14s($this->id) . '"' : '';
    }

    /**
     * @return string L'ID à utiliser comme valeur de l'attribut `for` sur un élement `label` pointant vers ce contrôle.
     */
    function for_id(): string
    {
        return $this->id;
    }

    /**
     * Affiche l'HTML du composant
     * @param ?T $current La valeur à modifier, `null` pour une création.
     * @return void
     */
    abstract function put(mixed $current = null): void;

    /**
     * Gets a H14Sed field "name" attribute.
     * @param string $name The inner name.
     * @return string
     */
    protected function name(string $name): string
    {
        return h14s($this->name ? "$this->name[$name]" : $name);
    }

    /**
     * Gets a H14Sed field "id" attribute.
     * @param string $id The inner id. Whitespace characters are replaced by underscores.
     * @return string
     */
    protected function id(string $id): string
    {
        $id = notnull(preg_replace('/\s/', '_', $id));
        return h14s($this->id ? "{$this->id}_$id" : $id);
    }
}
