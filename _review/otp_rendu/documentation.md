# Documentation otp équipe 413

### Sommaire
- [Role des fichiers](#Role_des_fichiers)
- [Description des fichiers](#Description_des_fichiers)
- [Graphe des intractions](#Graphe_des_intractions)
- [Video](#Video)

## Rôle des fichiers <a id="Role_des_fichiers"></a>

[otp.php](#otp.php)
- Fichier au se trouve les fonctions de bases de l'otp : sauvegarde, génération du code qr, vérification du secret ...

[otp-qr.php](#otp-qr.php)
- Page qui affiche le qr code, qui s'assure que l'utilisateur est en possession du secret et qui déclenche la sauvegarde du secret dans la base de données.


[otp_verify.php](#otp_verify.php)
- Fichier qui permet l'appel en frontend de la fonction "verify()" du fichier otp.php dans otp-qr.php et login.php

[otp_save.php](#otp_save.php)
- Fichier qui permet l'appel en frontend de la fonction "save_otp()" du fichier otp.php dans otp-qr.php

[connexion.php](#connexion.php)
- Page de connection, sur cette page se trouve le champ "Code sécurisé".

[login.php](#login.php)
- Fichier qui assure la validité des informations de la connexion et qui connecte l'utilisateur.

[modif_compte.php](#modif_compte.php)
- Page qui affiche les détails d'un compte le bouton pour générer un code qr est sur cette page.

[modif_compte.js](#modif_compte.js)
- Fichier qui supervise les actions de modif_compte.php s'occupe de faire apparaître la page otp-qr.php



## Description des fichiers <a id="Description_des_fichiers"></a>

### otp.php <a id="otp.php"></a>
#### Rôle principal :  
Bibliothèque centrale de gestion OTP (One-Time Password) basée sur TOTP.
#### Fonctions :
- **verify()** : Vérifie un code OTP contre un secret avec une fenêtre de validité de 30s
- **save_otp()** : Persiste le secret OTP en base de données pour un utilisateur
- **generate_totp()** : Crée un nouveau générateur TOTP avec paramètres par défaut
- **generate_secret()** : Extrait le secret (base32) d'un objet TOTP
- **get_url_otp()** : Génère l'URI standard pour les applications d'authentification

### otp-qr.php <a id="otp-qr.php"></a>
#### Rôle principal :  
Interface visuelle d'activation OTP avec génération de QR Code.
#### Fonctions :
- Génère et affiche un QR Code scannable
- Fournit un formulaire de validation du premier code
- Communique avec `otp_verify.php` et `otp_save.php` en AJAX
- Ferme automatiquement la fenêtre après activation réussie ou après un abondon

### otp_verify.php <a id="otp_verify.php"></a>
#### Rôle principal :  
Endpoint API pour la vérification des codes OTP.
#### Fonctions :
- Reçoit le code et le secret en POST
- Utilise `OTP\verify()` pour la validation
- Retourne une réponse binaire (1/0) au format texte
- Gère le cas où le secret est déjà en base de données

### otp_save.php <a id="otp_save.php"></a>
#### Rôle principal :  
Endpoint API pour la persistance du secret OTP.
#### Fonctions :
- Utilise `OTP\save_otp()` pour la sauvegarde

<!-- - Sécurisé par authentification préalable
- Stocke le secret dans la table `_compte`
- Retourne un statut booléen simplifié
- Garantit l'intégrité des données via PDO -->

### connexion.php <a id="connexion.php"></a>
#### Rôle principal :  
Page de connexion principale avec intégration OTP.
#### Fonctions :
- Affiche le champ OTP conditionnellement
- Gère les erreurs spécifiques OTP
- Préserve l'URL de retour après authentification
- Intègre le JavaScript pour la gestion dynamique du champ OTP

### login.php <a id="login.php"></a>
#### Rôle principal :  
Traitement central de l'authentification.
#### Fonctions :
<!-- - Vérifie les credentials standard (login/mdp) -->
- Applique la vérification OTP si le secret existe
<!-- - Différencie membres et professionnels -->
<!-- - Gère la régénération de l'ID de session
- Redirige avec messages d'erreur contextuels -->

### modif_compte.php <a id="modif_compte.php"></a>
#### Rôle principal :  
Interface de gestion des paramètres du compte.
#### Fonctions :
- Affiche le bouton d'activation OTP conditionnel
- Intègre la popup d'activation via JavaScript
- Bloque la modification si OTP déjà activé
<!-- - Gère le formulaire complet de modification de compte -->

### modif_compte.js <a id="modif_compte.js"></a>
#### Rôle principal :  
Gestion coté client de l'activation OTP.
#### Fonctions :
- Ouvre la popup `otp-qr.php` dans une nouvelle fenêtre
<!-- - Gère les interactions des boutons (générer/supprimer API key)
- Formatte les champs spécifiques (SIREN, téléphone) -->
- Communique avec les endpoints OTP via fetch API


## Graphe des intractions <a id="Graphe_des_intractions"></a>

```mermaid
graph TB
    subgraph "GESTION DE L'OTP"
    direction TB
    otp([otp.php])
    otp_qr([otp-qr.php])
    verify([otp_verify.php])
    save([otp_save.php])
    connexion_([connexion.php])
    login([login.php])
    modif([modif_compte.php])
    js([modif_compte.js])


    otp --->|"save_otp()"|save
    otp -->|"verify()"|verify
    verify -.->|"verify()"|otp_qr
    verify -.->|"verify()"|login
    save -.->|"save_otp()"|otp_qr


    otp-->|"generate_totp()"|otp_qr
    otp-->|"generate_secret()"|otp_qr
    otp-->|"get_url_otp()"|otp_qr


    subgraph Connexion
    connexion_ -->|"otp_secret"|login
    end 

    otp_qr-->|"window.open()"|js

    subgraph Affichage fenêtre
    modif-->|"button_generate_otp"|js
    end
    end

```

## Video <a id="Video"></a>

[Video : OTP équipe 413](https://youtu.be/RT3qcZEA3Dw)