<?php
require_once 'util.php';

/**
 * @extends Input<array>
 */
final class DynamicTable extends Input
{
    /**
     * @param Closure(DynamicTable, ?array): void $put_row
     * @param Closure(DynamicTable): void $put_prompt
     * @param array $columns
     * @param string $id
     * @param string $name
     * @param string $form_id
     */
    function __construct(
        private readonly array $columns,
        private readonly Closure $put_row,
        private readonly Closure $put_prompt,
        string $id,
        string $name        = '',
        string $form_id     = '',
        private readonly array $initial_rows = [],
    ) {
        parent::__construct($id, $name, $form_id);
    }

    /**
     * @param array $get_or_post
     * @param bool $required
     * @return ?array
     */
    function get(array $get_or_post, bool $required = true): ?array
    {
        $rows = getarg($get_or_post, $this->name, required: $required);
        if ($rows === null) return null;
        return soa_to_aos($rows);
    }

    /**
     * @inheritDoc
     */
    function put(mixed $current = null): void
    {
        ?>
<table id="<?= $this->id ?>" class="dynamic-table">
    <thead><tr>
        <?php foreach ($this->columns as $column) { ?>
            <th><?= $column ?></th>
        <?php } ?>
    </tr></thead>
    <tbody>
        <?php foreach ($current ?? $this->initial_rows as $row) {
            ?>
            <tr>
                <?php ($this->put_row)($this, $row); ?>
            </tr>
            <?php
        } ?>
    </tbody>
    <tfoot>
        <tr>
            <?= ($this->put_prompt)($this); ?>
        </tr>
    </tfoot>
</table>
<template id="<?= $this->id ?>-tr-template"><tr>
    <?php ($this->put_row)($this, null) ?>
</tr></template>
<?php
    }
}
