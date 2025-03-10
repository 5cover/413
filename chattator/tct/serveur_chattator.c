#include <arpa/inet.h>
#include <netinet/in.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/select.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <unistd.h>

int main() {
    int sock, cnx, option, ret, size, opt, token, num_message, confirmation;
    struct sockaddr_in addr;
    struct sockaddr_in conn_addr;
    char reponse[1019];
    char message[1000];
    char utilisateur[256];
    char mdp[256];
    ssize_t bytes_read;
    fd_set readfds;
    struct timeval timeout;

    opt = 1;
    sock = socket(AF_INET, SOCK_STREAM, 0);
    if (sock == -1) {
        perror("socket");
        _exit(EXIT_FAILURE);
    }

    if (setsockopt(sock, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt)) < 0) {
        perror("setsockopt");
        _exit(EXIT_FAILURE);
    }

    addr.sin_addr.s_addr = inet_addr("127.0.0.1");
    addr.sin_family = AF_INET;
    addr.sin_port = htons(8080);

    ret = bind(sock, (struct sockaddr *)&addr, sizeof(addr));
    if (ret == -1) {
        perror("bind");
        _exit(EXIT_FAILURE);
    }

    ret = listen(sock, 1);
    if (ret == -1) {
        perror("listen");
        _exit(EXIT_FAILURE);
    }

    printf("Serveur démarré.\n");

    size = sizeof(conn_addr);
    cnx = accept(sock, (struct sockaddr *)&conn_addr, (socklen_t *)&size);

    while (1) {
        FD_ZERO(&readfds);
        FD_SET(cnx, &readfds);
        timeout.tv_sec = 10;
        timeout.tv_usec = 0;

        // attend l'option choisie par le client
        ret = select(cnx + 1, &readfds, NULL, NULL, &timeout);

        if (ret == -1) {
            perror("select()");
        } else if (ret == 0) {
            // indique que le client n'a rien envoyé et attend de nouveau
            printf("En attente du client...\n");
        } else if (FD_ISSET(cnx, &readfds)) {
            // reçoit ce que le client a envoyé
            bytes_read = read(cnx, &option, sizeof(option));

            if (bytes_read > 0) {
                printf("Option reçue: %d\n", option);
            } else {
                printf("Erreur en tentant de lire l'option choisie, bytes lus : %zd\n", bytes_read);
                break;
            }

            // choisi la bonne option selon ce qu'a envoyé le client
            switch (option) {
            case 1: // "Affichage des messages"
                snprintf(reponse, sizeof(reponse), "Affichage de vos messages :\r\n");
                break;
            case 2: // "Affichage des messages reçus"
                snprintf(reponse, sizeof(reponse), "Affichage des messages reçus :\r\n");
                break;
            case 3: // "Affichage des messages envoyés"
                snprintf(reponse, sizeof(reponse), "Affichage des messages envoyés :\r\n");
                break;
            case 4: // "Affichage des messages reçus non lus"
                snprintf(reponse, sizeof(reponse), "Affichage des messages reçus non lus :\r\n");
                break;
            case 5:                                   // "Reception du nouveau message (et envoi au destinataire)"
                snprintf(reponse, sizeof(reponse), "Entrez votre message :\r\n");
                write(cnx, reponse, strlen(reponse)); // Demande au client son message

                bytes_read = read(cnx, message, sizeof(message) - 1);
                if (bytes_read > 0) {
                    message[bytes_read] = '\0';
                    printf("Message reçu : %s\n", message);
                    snprintf(reponse, sizeof(reponse), "Message envoyé: %s\r\n", message);
                } else {
                    snprintf(reponse, sizeof(reponse), "Erreur lors de la réception du message\r\n");
                }
                break;
            case 6:                                   // "Supprimage du message"
                snprintf(reponse, sizeof(reponse), "Entrez le numéro du message :\r\n");
                write(cnx, reponse, strlen(reponse)); // Demande au client quel message il veut modifier
                bytes_read = read(cnx, &num_message, sizeof(num_message));
                if (bytes_read > 0) {
                    message[bytes_read] = '\0';
                    printf("Numéro reçu : %s\n", message);
                }
                snprintf(reponse, sizeof(reponse), "Entrez la nouvelle version du message :\r\n");
                write(cnx, reponse, strlen(reponse)); // Demande au client quelles modifications il veut apporter au message
                bytes_read = read(cnx, message, sizeof(message) - 1);
                if (bytes_read > 0) {
                    message[bytes_read] = '\0';
                    printf("Message reçu : %s\n", message);
                    snprintf(reponse, sizeof(reponse), "Message envoyé: %s\r\n", message);
                } else {
                    snprintf(reponse, sizeof(reponse), "Erreur lors de la réception du message\r\n");
                }
                break;
            case 7: // "Modification du message"
                snprintf(reponse, sizeof(reponse), "Message modifié\r\n");
                break;
            case 8: // "Récupération des messages dans un fichier"
                snprintf(reponse, sizeof(reponse), "Récupération de vos messages effectuée.\r\n");
                break;
            case 9: // "Rechercher un utilisateur"
                snprintf(reponse, sizeof(reponse), "Données de l'utilisateur :\r\n");
                break;
            case 10: // "Blocage de l'utilisateur effectué"
                snprintf(reponse, sizeof(reponse), "Blocage de l'utilisateur effectué\r\n");
                break;
            case 11: // "Blocage de l'utilisateur annulé"
                snprintf(reponse, sizeof(reponse), "Blocage de l'utilisateur annulé\r\n");
                break;
            case 12: // "Bannissement de l'utilisateur effectué"
                snprintf(reponse, sizeof(reponse), "Bannissement de l'utilisateur effectué\r\n");
                break;
            case 13: // "Bannissement de l'utilisateur annulé"
                snprintf(reponse, sizeof(reponse), "Bannissement de l'utilisateur annulé\r\n");
                break;
            case 15: // Déconnexion de l'utilisateur
                snprintf(reponse, sizeof(reponse), "Au revoir.\n");
                write(cnx, reponse, strlen(reponse));
                close(cnx);          // Ferme la connection
                printf("Le serveur s'arrête.\r\n");
                return EXIT_SUCCESS; // Cas où la connection est fermée normalement par le client
            case 16:                 // connexion au compte
                bytes_read = read(cnx, utilisateur, sizeof(utilisateur) - 1);
                if (bytes_read > 0) {
                    utilisateur[bytes_read] = '\0';
                } else {
                    printf("Erreur en tentant de lire le nom d'utilisateur, bytes lus : %zd\n", bytes_read);
                }
                printf("Nom d'utilisateur reçu: %s\n", utilisateur);
                snprintf(reponse, sizeof(reponse), "Nom d'utilisateur reçu\r\n");
                write(cnx, reponse, strlen(reponse));
                memset(reponse, 0, sizeof(reponse));
                bytes_read = read(cnx, mdp, sizeof(mdp) - 1);
                if (bytes_read > 0) {
                    mdp[bytes_read] = '\0';
                    printf("Mot de passe reçu: %s\n", mdp);
                } else {
                    printf("Erreur en tentant de lire le mot de passe, bytes lus : %zd\n", bytes_read);
                }
                snprintf(reponse, sizeof(reponse), "Mot de passe reçu\n");
                write(cnx, reponse, strlen(reponse));
                memset(reponse, 0, sizeof(reponse));

                // rajouter vérif utilisateur-mot de passe

                // Envoi du token de connexion
                token = 101;
                write(cnx, &token, sizeof(token));
                fflush(stdout);
                printf("Token envoyé au client : %d\n", token);

                // Now wait for the confirmation from the client
                bytes_read = read(cnx, &confirmation, sizeof(confirmation));
                if (bytes_read > 0) {
                    printf("Confirmation reçue du client: %d\n", confirmation);
                    if (confirmation == 1) {
                        printf("Client a confirmé la réception du token.\n");
                    } else {
                        printf("Erreur: Confirmation non reçue ou incorrecte.\n");
                    }
                } else {
                    printf("Erreur en tentant de lire la confirmation du client\n");
                }
                break;
            default:
                snprintf(reponse, sizeof(reponse), "Commande inconnue\r\n");
                break;
            }

            // envoi la réponse du serveur au client
            write(cnx, reponse, strlen(reponse));
            fflush(stdout);
            memset(reponse, 0, sizeof(reponse));
        }
    }
    close(sock);
    printf("Le serveur s'arrête suite à une déconnexion innatendue.\r\n");
    return EXIT_FAILURE; // Cas où la connection n'est pas arrêtée en utilisant l'option 14 ou 15
}
