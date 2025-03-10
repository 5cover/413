#include <arpa/inet.h>
#include <netinet/in.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <unistd.h>
#include <sys/select.h>

int connexion(int token, int sock) {
    char util[256];
    char mdp[256];
    char buffer[100];
    ssize_t bytes_read;
    int confirmation,option_connexion;
    option_connexion = 16;

    if (token == 0) {
        write(sock, &option_connexion, sizeof(option_connexion)); // envoi option connexion
        printf("Veuillez vous connecter pour continuer : quel est votre nom d'utilisateur ?");
        fgets(util, sizeof(util), stdin);  // Read username
        write(sock, util, strlen(util));  // Send username
        fflush(stdout);

        bytes_read = read(sock, buffer, sizeof(buffer) - 1);  // 5-second timeout
        if (bytes_read > 0) {
            buffer[bytes_read] = '\0';
            printf("Réponse du serveur pour le nom d'utilisateur: %s", buffer);
        } else {
            printf("Erreur en tentant de lire la réponse du serveur pour le nom d'utilisateur\n");
        }
        memset(buffer, 0, sizeof(buffer));
        printf("Quel est votre mot de passe ?");
        fgets(mdp, sizeof(mdp), stdin);  // Read password
        write(sock, mdp, strlen(mdp));  // Send password
        fflush(stdout);

        bytes_read = read(sock, buffer, sizeof(buffer) - 1);
        if (bytes_read > 0) {
            buffer[bytes_read] = '\0';
            printf("Réponse du serveur pour le mot de passe: %s", buffer);
        } else {
            printf("Erreur en tentant de lire la réponse du serveur pour le mot de passe\n");
        }
        printf("Buffer content before printing: '%s'\n", buffer);
        memset(buffer, 0, sizeof(buffer));
        // Receive token from the server
        printf("Attente du token du serveur...\n");  // Debug line
        bytes_read = read(sock, &token, sizeof(token));  // 5-second timeout
        if (bytes_read > 0) {
            printf("Token reçu du serveur: %d\n", token);
        } else {
            printf("Erreur en tentant de recevoir le token. Bytes read: %zd\n", bytes_read);
            return token; // Exit if token is not received
        }

        // Set confirmation to 1
        confirmation = 1;
        printf("Envoi de la confirmation: %d\n", confirmation);  // Debugging line

        // Send the confirmation value (1) back to the server
        write(sock, &confirmation, sizeof(confirmation));  // Send confirmation
        printf("Confirmation (1) envoyée au serveur\n");
    }

    return token;
}


int main() {
    int sock,ret,option,token,num_message;
    struct sockaddr_in addr;
    char buffer[10000];
    char message[1000];
    ssize_t bytes_read;

    sock = socket(AF_INET, SOCK_STREAM, 0);
    addr.sin_addr.s_addr = inet_addr("127.0.0.1");
    addr.sin_family = AF_INET;
    addr.sin_port = htons(8080);

    ret = connect(sock, (struct sockaddr *)&addr, sizeof(addr));
    if (ret == -1) {
        printf("Connexion impossible.(Code 404)\n");
        perror("connect");
        _exit(EXIT_FAILURE);
    } else {
        printf("Connexion établie avec le serveur.(Code 200)\n");
        printf("Bienvenue dans votre espace de chat.\n");
    }

    option = 0;
    token=0;
    while (option != 15) {
        printf("Que voulez-vous faire ? \n");
        printf("Tapez 1 pour voir tous vos messages.\n");
        printf("Tapez 2 pour voir vos messages reçus.\n");
        printf("Tapez 3 pour voir vos messages envoyés.\n");
        printf("Tapez 4 pour voir vos messages reçus non lus.\n");
        printf("Tapez 5 pour envoyer un message.\n");
        printf("Tapez 6 pour supprimer un message.\n");
        printf("Tapez 7 pour modifier un message.\n");
        printf("Tapez 8 pour récupérer vos messages dans un fichier JSON.\n");
        printf("Tapez 9 pour rechercher un utilisateur.\n");
        printf("Tapez 10 pour bloquer un utilisateur.\n");
        printf("Tapez 11 pour débloquer un utilisateur.\n");
        printf("Tapez 12 pour bannir un utilisateur.\n");
        printf("Tapez 13 pour débannir un utilisateur.\n");
        if (token!=0){
            printf("Tapez 14 pour vous déconnecter\n");
            printf("Tapez 15 pour quitter.\n");
        }
        else {
            printf("Tapez 14 pour quitter.\n");
        }
        scanf("%d", &option);
        getchar();
        if (token ==0){
            if (option ==14) {
                option=15;
            }
            else if (option == 1 || option==2 || option == 3 || option == 4 || option == 5 || option == 6 || option == 7 || option == 8 || option == 10 || option == 11 || option == 12 || option == 13) {
                token=connexion(token,sock);
            } else if (option != 9) {
                option=50;
            }
        }
        write(sock, &option, sizeof(option));
        printf("Option envoyée: %d\n", option);
        fflush(stdout);

        switch (option) {
            case 1:
                bytes_read = read(sock, buffer, sizeof(buffer) - 1);
                if (bytes_read > 0) {
                    buffer[bytes_read] = '\0';
                    printf("%s", buffer);
                }
                memset(buffer, 0, sizeof(buffer));
                break;
            case 2:
                bytes_read = read(sock, buffer, sizeof(buffer) - 1);
                if (bytes_read > 0) {
                    buffer[bytes_read] = '\0';
                    printf("%s", buffer);
                }
                memset(buffer, 0, sizeof(buffer));
                break;
            case 3:
                bytes_read = read(sock, buffer, sizeof(buffer) - 1);
                if (bytes_read > 0) {
                    buffer[bytes_read] = '\0';
                    printf("%s", buffer);
                }
                memset(buffer, 0, sizeof(buffer));
                break;
            case 4:
                bytes_read = read(sock, buffer, sizeof(buffer) - 1);
                if (bytes_read > 0) {
                    buffer[bytes_read] = '\0';
                    printf("%s", buffer);
                }
                memset(buffer, 0, sizeof(buffer));
                break;
            case 5:
                bytes_read = read(sock, buffer, sizeof(buffer) - 1);
                if (bytes_read > 0) {
                    buffer[bytes_read] = '\0';
                    printf("%s", buffer);
                }
                memset(buffer, 0, sizeof(buffer));
                fgets(message, sizeof(message), stdin);
                message[strcspn(message, "\n")] = 0;
                write(sock, message, strlen(message));
                fflush(stdout);
                bytes_read = read(sock, buffer, sizeof(buffer) - 1);
                if (bytes_read > 0) {
                    buffer[bytes_read] = '\0';
                    printf("%s", buffer);
                }
                memset(buffer, 0, sizeof(buffer));
                break;
            case 6:
                bytes_read = read(sock, buffer, sizeof(buffer) - 1);
                if (bytes_read > 0) {
                    buffer[bytes_read] = '\0';
                    printf("%s", buffer);
                }
                memset(buffer, 0, sizeof(buffer));
                scanf("%d", &num_message);
                write(sock, &num_message, sizeof(num_message));
                bytes_read = read(sock, buffer, sizeof(buffer) - 1);
                if (bytes_read > 0) {
                    buffer[bytes_read] = '\0';
                    printf("%s", buffer);
                }
                memset(buffer, 0, sizeof(buffer));
                fgets(message, sizeof(message), stdin);
                message[strcspn(message, "\n")] = 0;
                write(sock, message, strlen(message));
                fflush(stdout);
                bytes_read = read(sock, buffer, sizeof(buffer) - 1);
                if (bytes_read > 0) {
                    buffer[bytes_read] = '\0';
                    printf("%s", buffer);
                }
                memset(buffer, 0, sizeof(buffer));
                break;
            case 7:
                bytes_read = read(sock, buffer, sizeof(buffer) - 1);
                if (bytes_read > 0) {
                    buffer[bytes_read] = '\0';
                    printf("%s", buffer);
                }
                memset(buffer, 0, sizeof(buffer));
                break;
            case 8:
                bytes_read = read(sock, buffer, sizeof(buffer) - 1);
                if (bytes_read > 0) {
                    buffer[bytes_read] = '\0';
                    printf("%s", buffer);
                }
                memset(buffer, 0, sizeof(buffer));
                break;
            case 9:
                bytes_read = read(sock, buffer, sizeof(buffer) - 1);
                if (bytes_read > 0) {
                    buffer[bytes_read] = '\0';
                    printf("%s", buffer);
                }
                memset(buffer, 0, sizeof(buffer));
                break;
            case 10:
                bytes_read = read(sock, buffer, sizeof(buffer) - 1);
                if (bytes_read > 0) {
                    buffer[bytes_read] = '\0';
                    printf("%s", buffer);
                }
                memset(buffer, 0, sizeof(buffer));
                break;
            case 11:
                bytes_read = read(sock, buffer, sizeof(buffer) - 1);
                if (bytes_read > 0) {
                    buffer[bytes_read] = '\0';
                    printf("%s", buffer);
                }
                memset(buffer, 0, sizeof(buffer));
                break;
            case 12:
                bytes_read = read(sock, buffer, sizeof(buffer) - 1);
                if (bytes_read > 0) {
                    buffer[bytes_read] = '\0';
                    printf("%s", buffer);
                }
                memset(buffer, 0, sizeof(buffer));
                break;
            case 13:
                bytes_read = read(sock, buffer, sizeof(buffer) - 1);
                if (bytes_read > 0) {
                    buffer[bytes_read] = '\0';
                    printf("%s", buffer);
                }
                memset(buffer, 0, sizeof(buffer));
                break;
            case 14:
                bytes_read = read(sock, buffer, sizeof(buffer) - 1);
                if (bytes_read > 0) {
                    buffer[bytes_read] = '\0';
                    printf("%s", buffer);
                }
                memset(buffer, 0, sizeof(buffer));
                break;
            default:
                bytes_read = read(sock, buffer, sizeof(buffer) - 1);
                if (bytes_read > 0) {
                    buffer[bytes_read] = '\0';
                    printf("%s", buffer);
                }
                memset(buffer, 0, sizeof(buffer));
                break;
        }

    }
    bytes_read = read(sock, buffer, sizeof(buffer) - 1);
    if (bytes_read > 0) {
        buffer[bytes_read] = '\0';
        printf("%s", buffer);
    }
    memset(buffer, 0, sizeof(buffer));
    close(sock);
    return EXIT_SUCCESS;
}