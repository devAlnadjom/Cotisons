# 🧾 Descriptif du Projet -- Application de Gestion de Cotisations

## 🎯 Objectif

Développer une application **web-mobile first**, sécurisée et
collaborative, permettant à des utilisateurs de **créer et gérer des
groupes de cotisation** avec des participants, des montants récurrents,
des invitations par email, et des preuves de paiement.

------------------------------------------------------------------------

## ⚙️ Stack Technique

-   **Backend** : Laravel 11
-   **Frontend** : Vue.js 3 + Inertia.js
-   **Authentification** : Laravel Breeze ou Fortify (auth par défaut
    activée)
-   **Base de données** : SQLite pour dev local, MySQL prévu pour
    production
-   **Upload de fichiers** : Preuve de cotisation (image)

------------------------------------------------------------------------

## 🧩 Fonctionnalités principales

### 🔐 Authentification

-   Utilisateur peut s'inscrire, se connecter
-   Auth obligatoire pour accéder aux fonctionnalités

### 📦 Groupes de cotisation

-   Un utilisateur peut **créer un groupe** (nom, description,
    périodicité)
-   Le **créateur devient automatiquement administrateur**
-   Il peut **modifier ou supprimer** (soft delete) le groupe
-   Il peut **inviter des participants par email** (voir point suivant)
-   Accès à un tableau de bord listant tous ses groupes (créés ou
    joints)

### ✉️ Invitations par email

-   L'administrateur d'un groupe peut **inviter un participant via son
    email**
-   Si le participant **n'a pas encore de compte**, un **compte est
    automatiquement généré** (avec validation via lien)
-   Un token d'invitation unique est généré et envoyé
-   Lors de l'acceptation, l'utilisateur est lié au groupe comme
    participant actif

### 👥 Participants

-   Un groupe a plusieurs participants
-   Chaque participant a :
    -   un **montant de cotisation par défaut**
    -   une **date d'ajout**
    -   un **statut** (`actif`, `pause`, `retiré`)
    -   un rôle éventuel d'**admin délégué**

### 💰 Cotisations

-   À chaque période (manuelle), l'admin peut **ajouter les cotisations
    dues**
-   Chaque cotisation est liée à :
    -   un **groupe**
    -   un **participant**
    -   un **montant**
    -   une **preuve (image)**
    -   une **date de versement**
    -   un **auteur (créateur de l'enregistrement)**
-   Chaque utilisateur peut **consulter toutes ses cotisations**

### 🛠️ Gestion

-   Un créateur ou admin peut :
    -   Ajouter ou retirer un participant à tout moment
    -   Ajouter des cotisations
-   Un utilisateur peut :
    -   Voir les groupes dans lesquels il est inscrit
    -   Voir l'historique de ses cotisations
    -   Ajouter une preuve s'il est autorisé

------------------------------------------------------------------------

## 📁 Structure des modèles

### `User`

-   Authentifié par défaut
-   Peut être créateur ou participant

### `Group`

-   name, description, created_by, periodicity
-   soft deletes activé

### `GroupParticipant`

-   user_id, group_id, montant_par_defaut, date_ajout, statut, is_admin
-   relation pivot entre User et Group

### `Cotisation`

-   group_id, user_id, montant, preuve_path, date_versement, created_by
-   soft deletes activé

### `Invitation`

-   group_id, email, token, status (pending/accepted)

------------------------------------------------------------------------

## 📚 Modules déjà couverts

-   ✅ Migrations avec `softDeletes` et `index`
-   ✅ Modèles Eloquent avec relations
-   ✅ Contrôleur `GroupController` (Inertia compatible)
-   ✅ Routes `resource` protégées
-   ✅ InvitationController (envoi + acceptation d'invitation)
-   ✅ Politique `GroupPolicy` (update, delete)

------------------------------------------------------------------------

## ✅ À faire ensuite

-   Interface Vue/Inertia :
    -   `Groups/Index.vue`
    -   `Groups/Create.vue`
    -   `Groups/Edit.vue`
-   Gestion des participants du groupe
-   UI pour validation d'invitation
-   Module de cotisation (création, listing, preuve d'upload)
-   Notifications (emails, flash Inertia)
-   Interface responsive mobile-first
