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
    const status = (await fetch(url, {
        credentials: 'include',
    })).status;
    const ok = status == 200;
    if (!ok) console.error(`failed: fetch ${url} (${status})`);
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
 * @param {number} id
 * @param {date} fin_blacklist
 * @returns {string}
 */
export function location_blacklist(id, fin_blacklist) {
    return '/do/blacklister.php?' + new URLSearchParams({ id, fin_blacklist, });
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
 * @param {string} userId
 * @param {string} duration
 * @returns {string} API URL
 */
export function location_blacklist(userId, duration) {
    return `/do/blacklister.php?user=${userId}&duration=${encodeURIComponent(duration)}`;
}
