import { fetchDo, location_signaler, requireElementById, location_blacklist, location_like } from './util.js';

for (const e of document.getElementsByClassName('input-duration')) setup_input_duration(e);
for (const e of document.getElementsByClassName('input-address')) setup_input_address(e);
for (const e of document.getElementsByClassName('input-image')) setup_input_image(e);
for (const e of document.getElementsByClassName('button-signaler')) setup_button_signaler(e);
for (const e of document.getElementsByClassName('button-blacklist')) setup_button_blacklist(e);
for (const e of document.getElementsByClassName('liker')) setup_liker(e);

const notif = requireElementById('btn-notifications');
notif.addEventListener('click', toggleMenu);

function toggleMenu() {
    var menu = document.getElementById("notif-list");
    if (menu.style.display === "none" || menu.style.display === "") {
        menu.style.display = "block";
    } else {
        menu.style.display = "none";
    }
}

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
            input_next.min = input.valueAsNumber > 0 || parseInt(input.min) === -1 ? '-1' : '0';
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
    let is_signaled = img.src.endsWith('filled.svg');
    element.addEventListener('click', async () => {
        let raison;
        if (is_signaled || (raison = prompt('Raison de votre signalement'))) {
            element.disabled = true;
            if (await fetchDo(location_signaler(element.dataset.idcco, element.dataset.avisId, raison))) {
                is_signaled ^= true;
                img.src = fill_src('flag', is_signaled);
            }
            element.disabled = false;

        }
    });
}

const BLACKLIST_DURATION = {
    years: 0,
    months: 0,
    weeks: 0,
    days: 0,
    hours: 0,
    minutes: 5
};

/**
 * @param {HTMLButtonElement} element
 */
function setup_button_blacklist(element) {
    element.addEventListener('click', async () => {
        if (confirm("⚠️ Attention : un blacklist est définitif et ne pourra pas être annulé.\n\nVoulez-vous vraiment continuer ?")) {
            element.disabled = true;
            const durationStr = calculeBlacklistEndDate(BLACKLIST_DURATION);

            if (await fetchDo(location_blacklist(element.dataset.avisId, durationStr))) {
                element.textContent = `Blacklisté`;
            }
        }
    });
}

/**
 * Formate la durée du blacklist en chaîne de caractères
 * @param {{ years: number, months: number, weeks: number, days: number, hours: number, minutes: number }} duration
 * @returns {string}
 */
function formatDuration(duration) {
    return `${duration.years}Y ${duration.months}M ${duration.weeks}W ${duration.days}D ${duration.hours}H ${duration.minutes}M`;
}

/**
 * Calcule la fin du blacklist
 * @param {{ years: number, months: number, weeks: number, days: number, hours: number, minutes: number }} duration
 * @returns {string} - Date au bon format (YYYY-MM-DD HH:MM:SS)
 */
function calculeBlacklistEndDate(duration) {
    let now = new Date();

    now.setFullYear(now.getFullYear() + duration.years);
    now.setMonth(now.getMonth() + duration.months);
    now.setDate(now.getDate() + (duration.weeks * 7) + duration.days);
    now.setHours(now.getHours() + duration.hours);
    now.setMinutes(now.getMinutes() + duration.minutes);

    // Converti au format YYYY-MM-DD HH:MM:SS
    return now.toISOString().slice(0, 19).replace("T", " ");
}

/**
 * @param {HTMLElement} element 
 */
function setup_liker(element) {
    const [button_like, button_dislike] = element.getElementsByTagName('button');
    const [span_like_count, span_dislike_count] = element.getElementsByTagName('span');

    const button_like_img = button_like.children[0];
    const button_dislike_img = button_dislike.children[0];

    let state = button_like_img.src.endsWith('filled.svg') ? true : button_dislike_img.src.endsWith('filled.svg') ? false : null;

    button_like.addEventListener('click', async () => {
        disabled(true);
        const dec = state === false;
        state = state !== true ? true : null;
        if (!(await update())) return;
        update_likes();
        update_dislikes();
        change_value(span_like_count, state === true ? 1 : -1);
        if (dec) change_value(span_dislike_count, -1);
        disabled(false);
    });

    button_dislike.addEventListener('click', async () => {
        disabled(true);
        const dec = state === true;
        state = state !== false ? false : null;
        if (!(await update())) return;
        update_likes();
        update_dislikes();
        change_value(span_dislike_count, state === false ? 1 : -1);
        if (dec) change_value(span_like_count, -1);
        disabled(false);
    });

    function disabled(value)
    {
        button_like.disabled = value;
        button_dislike.disabled = value;
    }

    function update_likes() {
        button_like_img.src = fill_src('thumb', state === true);
    }

    function update_dislikes() {
        button_dislike_img.src = fill_src('thumb', state === false);
    }

    function change_value(span, delta)
    {
        span.textContent = parseInt(span.textContent) + delta;
    }

    function update() {
        return fetchDo(location_like(element.dataset.commentId, state));
    }

}

/**
 * 
 * @param {string} name 
 * @param {boolean} filled 
 * @returns {string}
 */
function fill_src(name, filled) {
    return '/images/' + name + (filled ? '-filled.svg' : '.svg');
}
