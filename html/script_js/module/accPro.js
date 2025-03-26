import { requireElementById } from './util';


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

function fetchNotifications() {
    fetch('/json/fetch_notifications.php', { 'credentials': 'include', })
        .then(response => response.json())
        .then(data => {
            const notifCount = document.getElementById("notif-count");
            const notifList = document.getElementById("notif-items");

            notifCount.textContent = data.nb_avis_non_lus;

            notifList.innerHTML = "";
            data.avis.forEach(notif => {
                const li = document.createElement("li");
                li.innerHTML = `
                    <a href="detail_offre_pro.php?id=${notif.auteur}#1">
                        <strong>${notif.titre_offre}</strong> : 
                        ${notif.commentaire.length > 25 ? notif.commentaire.substring(0, 25) + "…" : notif.commentaire}
                    </a>
                `;
                notifList.appendChild(li);
            });
        })
        .catch(error => console.error("Erreur lors de la récupération des notifications:", error));
}

setInterval(fetchNotifications, 10000);
fetchNotifications();