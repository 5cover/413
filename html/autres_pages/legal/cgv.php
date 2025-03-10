<?php
require_once 'component/Page.php';
require_once 'Parsedown.php';

$page = new Page('Conditions Générales de Vente', main_class: 'text');

// 4 pages
// communes au membre et pro

$pd = new Parsedown();

$page->put($pd->text(file_get_contents('doc/cgv.md', use_include_path: true)));
