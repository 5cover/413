'use strict';

for (const e of document.getElementsByClassName('input-duration')) setup_input_duration(e);
for (const e of document.getElementsByClassName('input-address')) setup_input_address(e);
for (const e of document.getElementsByClassName('input-image')) setup_input_image(e);
for (const e of document.getElementsByClassName('button-signaler')) setup_button_signaler(e);

/**
 * @param {HTMLElement} element
 */
function setup_input_duration(element) {
    // Behaviors
    // - Increase/Decrease next/previous field when reaching max/min value
    // - Disable to preven going below zero
    const inputs = element.getElementsByTagName('input');

    for (let i = 0; i < inputs.length; ++i) {
        inputs.item(i).addEventListener('input', () => check_input(i));
    }

    /**
     * @param {number} i
     */
    function check_input(i) {
        const input = inputs.item(i);
        const input_prev = inputs.item(i - 1); // larger unit
        const input_next = inputs.item(i + 1); // smaller unit
        if (input_prev !== null) {
            if (input.valueAsNumber === -1) {
                decrement_input(i);
            } else {
                const excess = Math.trunc(input.valueAsNumber / input.max);
                if (excess > 0) {
                    input.valueAsNumber %= input.max;
                    input_prev.valueAsNumber += excess;
                    check_input(i - 1);
                }
            }
        }
        if (input_next !== null) {
            // for a min to be 0 means that every larger input is 0
            // for a min to be -1 means that some larger input is > 0
            input_next.min = input.valueAsNumber > 0 || input.min == -1 ? -1 : 0;
            check_input(i + 1);
        }
    }

    /**
     * 
     * @param {number} i &gt; 0
     */
    function decrement_input(i) {
        const input = inputs.item(i);
        const input_prev = inputs.item(i - 1); // larger unit

        if (input_prev.min) {
            input.valueAsNumber = Number(input.max) - 1;
        }
        if (input_prev.valueAsNumber > 0) {
            input_prev.stepDown();
        } else if (i > 0) {
            decrement_input(i - 1);
        }
    }
}


/**
 * @param {HTMLElement} element
 */
function setup_input_address(element) {
    // Behaviors
    // - Update readonly summary accordingly
    const input_summary = element.querySelector('summary input');
    const inputs = Array.from(element.querySelectorAll('label input'));
    inputs.forEach(input => {
        input.addEventListener('input', format_summary);
    });
    format_summary();

    function format_summary() {
        input_summary.value = format_adresse(...inputs.map(i => i.value));
    }

    function format_adresse(commune, localite, nom_voie, numero_voie, complement_numero, precision_int, precision_ext) {
        return elvis(precision_ext, ', ')
            + elvis(precision_int, ', ')
            + elvis(numero_voie, ' ')
            + elvis(complement_numero, ' ')
            + elvis(nom_voie, ', ')
            + elvis(localite, ', ')
            + commune;
    }

    function elvis(value, suffix) {
        return value ? `${value}${suffix}` : '';
    }
}

/**
 * @param {HTMLElement} element 
 */
function setup_input_image(element) {
    // Behaviors
    // - Dynamic preview
    const e_input_image = element.querySelector('input[type=file]');
    const e_preview = document.getElementById(element.id + '-preview');
    element.addEventListener('change', () => preview_image(e_input_image, e_preview));
}

/**
 * @param {HTMLInputElement} e_input_image 
 * @param {HTMLElement} e_preview 
*/
function preview_image(e_input_image, e_preview) {
    e_preview.textContent = '';
    for (const file of e_input_image.files) {
        if (!file.type.match('image.*')) {
            continue;
        }
        const reader = new FileReader();
        reader.addEventListener('load', function (event) {
            const image = new Image();
            image.addEventListener('load', function () {
                e_preview.appendChild(image);
            });
            image.src = event.target.result;
        });

        reader.readAsDataURL(file);
    }
}

/**
 * @param {HTMLButtonElement} element
 */
function setup_button_signaler(element) {
    const img = element.children[0];
    const is_signaled = img.src.endsWith('flag-filled.svg');
    element.addEventListener('click', async () => {
        let raison;
        if (is_signaled || (raison = prompt('Raison de votre signalement'))) {
            element.disabled = true;
            const ok = (await fetch(location_signaler(element.dataset.idcco, element.dataset.avisId, raison))).status == 200;
            element.disabled = false;
            if (ok) {
                img.src = is_signaled ? 'flag.svg' : 'flag-filled.svg';
            } else {
                console.error('failed signaler avis');
            }

        }
    });
}

/**
 * @param {any} id_compte
 * @param {any} id_signalable
 * @param {any} raison
 */
function location_signaler(id_compte, id_signalable, raison) {
    return '/do/signaler.php?' + new URLSearchParams({
        id_compte: id_compte,
        id_signalable: id_signalable,
        raison: raison,
    });
}