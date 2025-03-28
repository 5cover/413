/**
 * @param {?HTMLElement} target : element html vers lequel scroller
 */
export function scroller(target) {
    if (target) {
        target.scrollIntoView({ behavior: "instant", block: "start" });
    } else {
        console.log("Impossible de scroller");
    }
}

/**
 * @param {string} id 
 * @returns {HTMLElement}
 */
export function requireElementById(id) {
    const e = document.getElementById(id);
    if (e === null) throw new Error(`Missing element id '${id}'`);
    return e;
}

/**
 * @param {string} url 
 * @return {bool}
 */
export async function fetchDo(url) {
    const resp = (await fetch(url, {
        credentials: 'include',
    }));
    const ok = resp.status == 200;
    if (!ok) console.error(`failed: fetch ${url} (${resp.status}): ${await resp.text()}`);
    return ok;
}

/**
 * @param {number} id_compte
 * @param {number} id_signalable
 * @param {string} raison
 * @returns {string}
 */
export function location_signaler(id_compte, id_signalable, raison) {
    return '/do/signaler.php?' + new URLSearchParams({ id_compte, id_signalable, raison, });
}

/**
 * @param {number} id_offre 
 * @returns {string}
 */
export function location_delete_offer(id_offre) {
    return '/do/delete_offer.php?' + new URLSearchParams({ id_offre, });
}

/**
 * @param {number} id_avis,
 * @param {boolean|null=null} new_state,
 * @returns {string}
 */
export function location_like(id_avis, new_state = null) {
    return '/do/like.php?' + new URLSearchParams({ id_avis, new_state });
}

/**
 * Generate API URL for blacklisting
 * @param {number} avisId
 * @param {string} duration
 * @returns {string} API URL
 */
export function location_blacklist(avisId, duration) {
    return `/do/blacklister.php?id=${avisId}&finblacklist=${encodeURIComponent(duration)}`;
}


/**
 * Formate la durée du blacklist en chaîne de caractères
 * @param {{ years: number, months: number, weeks: number, days: number, hours: number, minutes: number }} duration
 * @returns {string}
 */
export function formatDuration(duration) {
    return `${duration.years}Y ${duration.months}M ${duration.weeks}W ${duration.days}D ${duration.hours}H ${duration.minutes}M`;
}


/**
 * @param {HTMLElement[]} elements
 * @param {Promise} promise 
 */
export async function enable_disable(elements, promise) {
    for (const e of elements) e.disabled = true;
    await promise;
    for (const e of elements) e.disabled = false;
}