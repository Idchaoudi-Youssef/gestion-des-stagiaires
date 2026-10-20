# Gestion des Stagiaires

Application web de gestion des internships: elle permet à une entreprise
d'attribuer des stagiaires à des encadrants, de leur suivre des tâches, de
recevoir leurs rapports (hebdomadaires et final) et de générer leur attestation
de fin de stage.

Le projet est écrit en **Laravel 12** et fonctionne avec **SQLite** par défaut
(ou MySQL/MariaDB si vous préférez).

---

## Fonctionnalités

### Administrateur

- Tableau de bord avec les compteurs (interns, encadrants, rapports) et trois
  graphiques (interns créés par mois, répartition des stages, utilisateurs
  par département).
- Création, modification et suppression des comptes (interns, encadrants).
- Gestion des départements.
- Attribution des interns à un ou plusieurs encadrants.
- Saisie des informations de stage (dates, durée, poste occupé).
- Validation ou rejet des rapports finaux, avec motif de rejet.
- Suppression des rapports rejetés.

### Encadrant (supervisor)

- Tableau de bord : interns rattachés et état de leurs tâches.
- Création, modification et suppression des tâches, avec une date limite.
- Consultation des rapports hebdomadaires reçus.

### Intern (stagiaire)

- Tableau de bord : jours restants, tâches par état, autres interns du
  département.
- Liste de ses tâches, filtrage par encadrant et recherche par mot-clé.
- Passage d'une tâche en « terminée » (uniquement les tâches qui lui
  appartiennent).
- Envoi d'un rapport hebdomadaire ou final (PDF, Word).
- Attestation de fin de stage, imprimable, une fois le rapport final validé.

### Compte en attente (pending)

Toute personne qui s'inscrit elle-même obtient un compte **pending** : elle
peut se connecter mais elle n'accède à aucun espace tant qu'un administrateur
ne lui a pas attribué un rôle. Avant cette correction, une inscription
donneait l'accès complet à l'espace stagiaire.

---

## Rôles et autorisations

Le rôle est stocké en base et relu à **chaque requête** : révoquer un rôle
prend effet immédiatement, sans attendre l'expiration de la session.

| Rôle       | Peut                                                                  |
|------------|-----------------------------------------------------------------------|
| `admin`    | tout                                                                |
| `supervisor` | gérer ses tâches, voir les rapports de ses interns                   |
| `intern`   | voir et compléter ses tâches, envoyer ses rapports                   |
| `pending`  | rien, seulement la page « en attente de validation »                 |

Les règles fines sont appliquées par `App\Policies\TaskPolicy` :

- seul l'encadrant qui a créé une tâche peut la modifier ou la supprimer ;
- seul l'intern concerné peut passer une tâche en « terminée ».

---

## Technologies

| Outil              | Version / rôle                                    |
|--------------------|---------------------------------------------------|
| PHP                | 8.2+                                              |
| Laravel            | 12                                                |
| Eloquent           | ORM de Laravel                                    |
| SQLite / MySQL     | base de données                                   |
| Blade              | vues (SB Admin 2 + template DigiMedia)            |
| Pest               | tests (77 tests)                                  |
| Chart.js           | graphiques des tableaux de bord (CDN)             |
| DataTables         | tri et recherche des tableaux (CDN)               |

Le projet **n'a besoin ni de Node.js ni d'un build front** pour tourner : les
vues utilisent les assets statiques de `public/`. Le fichier `package.json` est
conservé pour pouvoir ajouter du Tailwind plus tard, mais il n'est pas requis
pour faire tourner l'application.

---

## Installation

### Prérequis

- PHP **8.2** ou plus, avec les extensions `mbstring`, `openssl`, `pdo`,
  `tokenizer`, `xml`, `ctype`, `fileinfo`, `curl`.
- [Composer](https://getcomposer.org/).

### Étapes

```bash
# 1. Récupérer le projet
git clone https://github.com/Idchaoudi-Youssef/gestion-des-stagiaires.git
cd gestion-des-stagiaires

# 2. Installer les dépendances PHP
composer install

# 3. Créer le fichier de configuration
cp .env.example .env        # Windows : copy .env.example .env

# 4. Générer la clé de l'application
php artisan key:generate

# 5. Créer la base de données SQLite
touch database/database.sqlite       # Windows PowerShell : New-Item database\database.sqlite

# 6. Créer les tables et les données de démonstration
php artisan migrate --seed

# 7. Donner accès au dossier des fichiers envoyés
php artisan storage:link

# 8. Démarrer le serveur
php artisan serve
```

L'application est disponible sur <http://127.0.0.1:8000>.

### Comptes de démonstration

Créés par `php artisan migrate --seed`. Le mot de passe est `password` pour
tous.

| Rôle         | Adresse e-mail            |
|--------------|---------------------------|
| Administrateur | `demo.admin@example.com`     |
| Encadrant    | `demo.supervisor@example.com`  |
| Stagiaire    | `demo.intern@example.com`      |
| En attente   | `demo.pending@example.com`     |

---

## Configuration

### Base de données

Par défaut l'application utilise SQLite. Pour MySQL ou MariaDB, décommentez le
bloc correspondant dans `.env` :

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_des_stagiaires
DB_USERNAME=root
DB_PASSWORD=
```

### Identité de l'entreprise

Les informations imprimées sur l'attestation de stage et affichées dans la
barre latérale se configurent dans `.env`. **Les valeurs sont vides par
défaut** : remplissez-les dans votre propre fichier `.env`, qui n'est jamais
commité, pour qu'aucune donnée réelle d'entreprise ne se retrouve dans le
dépôt.

```dotenv
COMPANY_NAME=""
COMPANY_CITY=""
COMPANY_PHONE=""
COMPANY_EMAIL=""
COMPANY_SIRET=""
COMPANY_LOGO="img/logo-placeholder.svg"

SIGNATORY_NAME=""
SIGNATORY_POSITION=""
SIGNATURE_IMAGE=""
```

`COMPANY_LOGO` et `SIGNATURE_IMAGE` sont des chemins de fichiers situés dans
le dossier `public/`. Un logo de remplacement neutre
(`public/img/logo-placeholder.svg`) est fourni : déposez votre propre logo
dans `public/` et indiquez son chemin. Laissez `SIGNATURE_IMAGE` vide pour
imprimer l'attestation sans signature.

---

## Tâches planifiées

Deux commandes Artisan font le ménage et sont enregistrées dans le
planificateur de Laravel :

```bash
php artisan tasks:update-states        # passe en « unfinished » les tâches en retard
php artisan internships:close-finished # cloture les stages dont la date de fin est passée
php artisan schedule:list              # voir le planning
```

Pour qu'elles tournent automatiquement, ajoutez cette ligne dans la crontab
du serveur :

```cron
* * * * * cd /chemin/vers/gestion-des-stagiaires && php artisan schedule:run >> /dev/null 2>&1
```

---

## Tests

```bash
php artisan test
```

Le suite couvre :

- l'accès selon le rôle (qui peut voir quoi) ;
- l'isolation des tâches d'un intern (impossible de compléter ou de chercher
  les tâches des autres) ;
- l'isolation des tâches d'un encadrant ;
- la protection des comptes administrateur et la suppression des départements ;
- le cycle de vie des états d'une tâche et d'un stage ;
- l'ouverture de **toutes** les pages de l'application (test de fumée : une
  page qui répond avec une erreur 500 fait échouer le test).

La base de données des tests est une base SQLite en mémoire, créée et
détruite automatiquement. Aucun fichier `.env` n'est nécessaire pour lancer
la suite.

---

## Structure du projet

```
app/
├── Enums/              # UserType, TaskState, InternshipStatus, ReportState, ReportType
├── Console/Commands/   # tasks:update-states, internships:close-finished
├── Http/
│   ├── Controllers/    # Admin, Supervisor, Intern, Dashboard, Profile, Auth
│   ├── Middleware/     # EnsureUserType : contrôle du rôle
│   └── Requests/       # LoginRequest
├── Models/             # User, Department, Internship, Task, Report, InternSupervisor
├── Policies/           # TaskPolicy
└── Services/           # TaskStateService, InternshipService
config/
├── internship.php      # société et signataire de l'attestation
database/
├── factories/          # User, Department, Task, Report, Internship
├── migrations/         # schéma de la base
└── seeders/            # données de démonstration
resources/views/        # vues Blade (welcome, auth, admin, supervisor, intern)
routes/
├── web.php             # routes de l'application
├── auth.php            # routes d'authentification
└── console.php         # commandes et planificateur
tests/
├── Feature/            # tests fonctionnels
└── Unit/               # tests unitaires des services
```

---

## Notes de sécurité

- Les mots de passe sont hachés par le cast `hashed` du modèle `User`.
- Les fichiers envoyés par les interns sont limités à 2 Mo et aux formats
  `pdf`, `doc`, `docx` (`mimes:pdf,doc,docx`).
- Les requêtes qui modifient une donnée passent par des formulaires
  protégée par `@csrf` et par la méthode HTTP adaptée (`@method('PUT')`,
  `@method('DELETE')`).
- La suppression d'un compte ou d'un département passe par des routes
  `DELETE` protégées par le rôle administrateur.
- `composer audit` ne remonte plus aucune vulnérabilité connue sur les
  dépendances installées.

---

## Licence

MIT — voir le fichier [LICENSE](LICENSE).
