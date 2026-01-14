# Application de Gestion de Réservations - Restaurant

## 1. Objectif

Application **interne** pour un seul restaurant permettant de :
- Gérer les réservations clients
- Visualiser les tables par zone et filtrer par disponibilité
- Suivre le statut des tables : `Disponible → Réservée → Occupée → Disponible`
- Dashboard avec statistiques pour le gérant

---

## 2. Stack Technique

| Technologie | Utilisation |
|-------------|-------------|
| **Laravel** | Backend / API |
| **MySQL** | Base de données |
| **Laravel Breeze** | Authentification |
| **Blade** | Templates |
| **Tailwind CSS** | Styles |
| **Alpine.js** | Interactivité JS |

---

## 3. Rôles Utilisateurs

| Rôle | Accès |
|------|-------|
| **Admin** | Tout (dashboard, paramètres, zones, tables, réservations) |
| **Réception** | Planning, tables, réservations, arrivées clients |

---

## 4. Entités / Base de données

### Users
| Champ | Type | Description |
|-------|------|-------------|
| id | bigint | Clé primaire |
| name | string | Nom complet |
| email | string | Email unique |
| password | string | Mot de passe hashé |
| role | enum | admin / reception |
| timestamps | | created_at, updated_at |

### Zones
| Champ | Type | Description |
|-------|------|-------------|
| id | bigint | Clé primaire |
| nom | string | Nom de la zone (ex: Terrasse, VIP) |
| description | text | Description optionnelle |
| timestamps | | created_at, updated_at |

### Tables
| Champ | Type | Description |
|-------|------|-------------|
| id | bigint | Clé primaire |
| numero | string | Numéro de table (ex: T1, 12) |
| capacite | integer | Nombre de places |
| zone_id | foreignId | Lien vers zones |
| statut | enum | disponible / reservee / occupee |
| timestamps | | created_at, updated_at |

### Reservations
| Champ | Type | Description |
|-------|------|-------------|
| id | bigint | Clé primaire |
| client_nom | string | Nom du client |
| client_telephone | string | Téléphone |
| nombre_personnes | integer | Nombre de convives |
| date_reservation | date | Date de la réservation |
| heure_reservation | time | Heure de la réservation |
| table_id | foreignId | Lien vers tables |
| statut | enum | en_attente / confirmee / terminee / annulee |
| commentaire | text | Notes optionnelles |
| user_id | foreignId | Qui a créé la réservation |
| timestamps | | created_at, updated_at |

### Parametres (settings)
| Champ | Type | Description |
|-------|------|-------------|
| id | bigint | Clé primaire |
| cle | string | Clé du paramètre |
| valeur | text | Valeur du paramètre |

---

## 5. Modules / Fonctionnalités

### V1 - Fonctionnalités de base

| Module | Description |
|--------|-------------|
| **Auth** | Login / Logout avec Breeze |
| **Dashboard** | Stats (réservations jour/semaine/mois, taux occupation) |
| **Zones** | CRUD zones (admin) |
| **Tables** | CRUD tables + gestion statuts |
| **Réservations** | Création, modification, recherche, annulation |
| **Arrivée clients** | Recherche client, passage Occupée, libération table |
| **Paramètres** | Horaires, logo, config restaurant (admin) |

### V2 - Fonctionnalités futures
- Événements spéciaux (bloquer tables)
- Notifications email/SMS
- Historique / CRM léger

---

## 6. Flow des Statuts

### Statut des Tables
```
Disponible ──(réservation)──> Réservée ──(client arrive)──> Occupée ──(fin repas)──> Disponible
```

### Statut des Réservations
```
En attente ──(confirmation)──> Confirmée ──(client part)──> Terminée
                                    │
                                    └──(annulation)──> Annulée
```

---

## 7. Design / UI

### Typographie
- **Titres** : Playfair Display
- **Textes** : Montserrat

### Couleurs
| Usage | Couleur |
|-------|---------|
| Principale (accents) | `#B08968` (or/bronze) |
| Texte | `#2E2E2E` (anthracite) |
| Fond | `#FFFFFF` |
| Disponible | `#4CAF50` (vert) |
| Réservée | `#FF9800` (orange) |
| Occupée | `#F44336` (rouge) |

### UX
- Sidebar verticale avec icônes
- Tables en cartes avec numéro, statut, capacité
- Responsive (tablette pour utilisation en salle)
- Feedback visuel (toast après chaque action)

---

## 8. Sécurité

- Validation de tous les inputs
- Hashage des mots de passe (bcrypt)
- Protection CSRF
- Autorisation par rôle (middleware)
- User ne voit que ses données autorisées

---

## 9. Architecture des Fichiers

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── ZoneController.php
│   │   ├── TableController.php
│   │   ├── ReservationController.php
│   │   └── ParametreController.php
│   └── Middleware/
│       └── CheckRole.php
├── Models/
│   ├── User.php
│   ├── Zone.php
│   ├── Table.php
│   ├── Reservation.php
│   └── Parametre.php
└── Services/
    └── ReservationService.php

resources/views/
├── layouts/
│   └── app.blade.php
├── dashboard/
├── zones/
├── tables/
├── reservations/
└── parametres/
```
