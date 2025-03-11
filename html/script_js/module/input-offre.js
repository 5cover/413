import DynamicTable from './DynamicTable.js';
import { requireElementById } from './util.js';

// Grille tarifaire
const table_tarifs = requireElementById('table-tarifs');
if (table_tarifs !== null) {
    const i_nom = 0, i_montant = 1;
    const tarifs = new DynamicTable(
        table_tarifs,
        requireElementById('table-tarifs-tr-template'),
        function (tr) {
            return !this.has_row((/** @type {string[]} */ row) => row[i_nom] === nom(tr).value);
        },
        (tr, row) => {
            nom(tr).value = row[i_nom];
            montant(tr).value = row[i_montant];
        },
        1,
    );
    tarifs.setup();

    /**
     * @param {HTMLTableRowElement} tr 
     * @returns {HTMLInputElement}
     */
    function nom(tr) {
        return tr.cells[i_nom].children[0];
    }
    /**
     * @param {HTMLTableRowElement} tr 
     * @returns {HTMLInputElement}
     */
    function montant(tr) {
        return tr.cells[i_montant].children[0];
    }
}

// Périodes
{
    const i_debut = 0, i_fin = 1;

    const periodes = new DynamicTable(
        requireElementById('table-periodes'),
        requireElementById('table-periodes-tr-template'),
        tr => {
            fin(tr).min = debut(tr).value;
            return true;
        },
        (tr, row) => {
            debut(tr).value = row[i_debut];
            fin(tr).value = row[i_fin];
        }
    );
    periodes.setup();

    /**
     * @param {HTMLTableRowElement} tr 
     * @returns {HTMLInputElement}
     */
    function debut(tr) {
        return tr.cells[i_debut].children[0];
    }
    /**
     * @param {HTMLTableRowElement} tr 
     * @returns {HTMLInputElement}
     */
    function fin(tr) {
        return tr.cells[i_fin].children[0];
    }
}

// Horaires
{
    for (let dow = 0; dow < 7; ++dow) {
        const button_add_horaire = requireElementById('button-add-horaire-' + dow);
        const tbody_horaires = requireElementById('table-horaires-' + dow).querySelector('tbody');
        button_add_horaire.addEventListener('click', () => tbody_horaires.appendChild(create_horaire_tr(dow)));
    }

    /**
     * @param {number} dow 
     * @return {HTMLTableRowElement}
     */
    function create_horaire_tr(dow) {
        /**@type {HTMLTableRowElement}*/ const tr_horaire = requireElementById('template-horaire-tr-' + dow).content.children[0].cloneNode(true);
        /**@type {HTMLInputElement}*/ const debut = tr_horaire.children[0];
        /**@type {HTMLInputElement}*/ const fin = tr_horaire.children[1];
        /**@type {HTMLButtonElement}*/ const btn_remove = tr_horaire.children[2];

        debut.addEventListener('input', () => fin.min = debut.value);

        btn_remove.addEventListener('click', () => tr_horaire.remove());

        return tr_horaire;
    }
}
