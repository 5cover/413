import { fetchDo, location_signaler, requireElementById, location_blacklist } from './util.js';

for (const e of document.getElementsByClassName('input-duration')) setup_input_duration(e);
for (const e of document.getElementsByClassName('input-address')) setup_input_address(e);
for (const e of document.getElementsByClassName('input-image')) setup_input_image(e);
for (const e of document.getElementsByClassName('button-signaler')) setup_button_signaler(e);
for (const e of document.getElementsByClassName('button-blacklist')) setup_button_blacklist(e);
for (const e of document.getElementsByClassName('button-like')) setup_button_like(e);
for (const e of document.getElementsByClassName('button-dislike')) setup_button_dislike(e);

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
    const e_preview = requireElementById(element.id + '-preview');
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
    let is_signaled = img.src.endsWith('flag-filled.svg');
    element.addEventListener('click', async () => {
        let raison;
        if (is_signaled || (raison = prompt('Raison de votre signalement'))) {
            element.disabled = true;
            if (await fetchDo(location_signaler(element.dataset.idcco, element.dataset.avisId, raison))) {
                is_signaled ^= true;
                img.src = '/images/' + (is_signaled ? 'flag-filled.svg' : 'flag.svg');
            }
            element.disabled = false;

        }
    });
}

/**
 * @param {HTMLButtonElement} element
 */
function setup_button_blacklist(element) {
    element.addEventListener('click', async () => {
        const duration = await promptBlacklistDuration();
        if (duration) {
            element.disabled = true; // Disable button to prevent unblacklisting

            if (await fetchDo(location_blacklist(element.dataset.userId, duration))) {
                element.textContent = `Blacklisted (${duration})`;
            }
        }
    });
}

/**
 * Displays a modal with number pickers for blacklist duration.
 * @returns {Promise<string|null>} Selected duration or null if canceled.
 */
function promptBlacklistDuration() {
    return new Promise((resolve) => {
        // Create the modal
        let modal = document.createElement('div');
        modal.innerHTML = `
            <div style="
                position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
                background: white; padding: 20px; box-shadow: 0px 0px 10px rgba(0,0,0,0.2);
                border-radius: 8px; text-align: center; z-index: 1000;
            ">
                <p style="color: red; font-weight: bold;">
                    ⚠️ Attention : Ce blacklist est définitif et ne pourra pas être annulé.
                </p>
                <h2>Choisissez la durée du blacklist</h2>
                <label>Années: <input type="number" id="years" min="0" max="100" value="1"></label><br>
                <label>Mois: <input type="number" id="months" min="0" max="11" value="0"></label><br>
                <label>Semaines: <input type="number" id="weeks" min="0" max="4" value="0"></label><br>
                <label>Jours: <input type="number" id="days" min="0" max="6" value="0"></label><br>
                <label>Heures: <input type="number" id="hours" min="0" max="23" value="0"></label><br>
                <label>Minutes: <input type="number" id="minutes" min="0" max="59" value="0"></label><br>
                <button id="confirm">Confirmer</button>
                <button id="cancel">Annuler</button>
            </div>
        `;

        document.body.appendChild(modal);

        // Handle confirmation
        modal.querySelector('#confirm').addEventListener('click', () => {
            let years = parseInt(document.getElementById('years').value, 10);
            let months = parseInt(document.getElementById('months').value, 10);
            let weeks = parseInt(document.getElementById('weeks').value, 10);
            let days = parseInt(document.getElementById('days').value, 10);
            let hours = parseInt(document.getElementById('hours').value, 10);
            let minutes = parseInt(document.getElementById('minutes').value, 10);

            let duration = `${years}Y ${months}M ${weeks}W ${days}D ${hours}H ${minutes}M`;
            modal.remove();
            resolve(duration); // Send selected duration
        });

        // Handle cancel
        modal.querySelector('#cancel').addEventListener('click', () => {
            modal.remove();
            resolve(null); // Cancel blacklist
        });
    });
}

/**
 * Calculate blacklist end date (Now + User Chosen Duration)
 * @param {{ years: number, months: number, weeks: number, days: number, hours: number, minutes: number }} duration
 * @returns {string} - Formatted date (YYYY-MM-DD HH:MM:SS)
 */
function calculateBlacklistEndDate(duration) {
    let now = new Date();

    now.setFullYear(now.getFullYear() + duration.years);
    now.setMonth(now.getMonth() + duration.months);
    now.setDate(now.getDate() + (duration.weeks * 7) + duration.days);
    now.setHours(now.getHours() + duration.hours);
    now.setMinutes(now.getMinutes() + duration.minutes);

    // Convert to YYYY-MM-DD HH:MM:SS format
    return now.toISOString().slice(0, 19).replace("T", " ");
}

/**
 * @param {HTMLButtonElement} element 
 */
function setup_button_like(element) {

}

/**
 * 
 * @param {HTMLButtonElement} element 
 */
function setup_button_dislike(element) {

}