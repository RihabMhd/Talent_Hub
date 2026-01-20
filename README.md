# 🎯 Talent HUB

Plateforme de mise en relation candidats / recruteurs développée en PHP 8 avec architecture MVC.

---

## 🚀 Installation Rapide

### 1. Cloner le projet
```bash
git clone https://github.com/votre-username/talent-hub.git
cd talent-hub
```

### 2. Créer la base de données
```sql
CREATE DATABASE talent_hub;
```

Importer le schéma :
```bash
mysql -u root -p talent_hub < database/schema.sql
```

### 3. Configuration
Copier et éditer le fichier de config :
```bash
cp config/config.example.php config/config.php
```

Modifier les informations de connexion dans `config/config.php` :
```php
'db' => [
    'host' => 'localhost',
    'dbname' => 'talent_hub',
    'username' => 'root',
    'password' => 'votre_mot_de_passe'
]
```

### 4. Créer les dossiers nécessaires
```bash
mkdir -p storage/uploads/{cv,images}
chmod -R 755 storage
```

### 5. Lancer l'application
Ouvrir dans le navigateur : `http://localhost/talent-hub`

---

## 📁 Structure du Project

```
│   .env
│   README.md
│   
├───app
│   │   db.php
│   │   
│   ├───Controllers
│   │   │   AuthController.php
│   │   │   
│   │   ├───Admin
│   │   │       ApplicationController.php
│   │   │       CategoryController.php
│   │   │       DashboardController.php
│   │   │       JobOfferController.php
│   │   │       TagController.php
│   │   │
│   │   ├───Candidate
│   │   │       ApplicationController.php
│   │   │       JobController.php
│   │   │       ProfileController.php
│   │   │
│   │   └───Recruiter
│   │           ApplicationController.php
│   │           DashboardController.php
│   │           JobOfferController.php
│   │
│   ├───Middleware
│   │       admin.php
│   │       auth.php
│   │       recruiter.php
│   │
│   ├───Models
│   │       Application.php
│   │       CandidateProfile.php
│   │       Category.php
│   │       Company.php
│   │       JobOffer.php
│   │       Tag.php
│   │       User.php
│   │
│   ├───Repositories
│   │       ApplicationRepository.php
│   │       CandidateProfileRepository.php
│   │       CategoryRepository.php
│   │       CompanyRepository.php
│   │       JobOfferRepository.php
│   │       TagRepository.php
│   │       UserRepository.php
│   │
│   ├───Services
│   │       ApplicationService.php
│   │       AuthService.php
│   │       FileUploadService.php
│   │       JobOfferService.php
│   │       RecommendationService.php
│   │       StatisticsService.php
│   │
│   └───Views
│       ├───admin
│       ├───candidate
│       ├───partials
│       └───recruiter
├───database
│   └───migrations
├───public
│   │   index.php
│   │
│   ├───assets
│   └───uploads
│       ├───avatars
│       └───cvs
└───routes
        admin.php
        api.php
        recruiter.php
        web.php
```

---

## 👥 Rôles

- **Admin** : Gestion complète (catégories, tags, offres, utilisateurs)
- **Recruteur** : Publier des offres, gérer les candidatures
- **Candidat** : Rechercher et postuler aux offres

---

## ✨ Fonctionnalités Principales

### Authentification
- Inscription / Connexion / Déconnexion
- Hashage sécurisé des mots de passe
- Protection des routes par rôle

### Admin
- Dashboard avec statistiques
- CRUD Catégories et Tags
- Gestion des offres (archivage soft delete)
- Modération des candidatures

### Recruteur
- Inscription entreprise
- Créer/éditer/supprimer des offres
- Consulter les candidatures reçues
- Télécharger les CV

### Candidat
- Recherche d'offres (avec filtres AJAX)
- Profil avec compétences
- Upload de CV sécurisé
- Recommandations personnalisées

---

## 🛠 Technologies

- PHP 8.2+
- MySQL 8.0+
- PDO (requêtes préparées)
- Architecture MVC
- Repository Pattern
- JavaScript (AJAX)

---

## 🔒 Sécurité

- Requêtes préparées PDO (protection SQL injection)
- Hashage bcrypt des mots de passe
- Validation des uploads (type, taille)
- Protection XSS (htmlspecialchars)
- Soft delete pour l'archivage

---

## 📝 Routes Principales

```
GET  /                          # Accueil
GET  /login                     # Connexion
POST /login                     # Traiter connexion
GET  /register                  # Inscription candidat
GET  /register/recruiter        # Inscription recruteur
POST /logout                    # Déconnexion

GET  /admin/dashboard           # Dashboard admin
GET  /admin/categories          # Gérer catégories
GET  /admin/tags                # Gérer tags

GET  /recruiter/dashboard       # Dashboard recruteur
GET  /recruiter/offers          # Mes offres
POST /recruiter/offers          # Créer offre

GET  /candidate/dashboard       # Dashboard candidat
GET  /candidate/recommendations # Offres recommandées
POST /candidate/apply/{id}      # Postuler

GET  /api/jobs/search           # API recherche (AJAX)
```

---

## 👨‍💻 Équipe

- **DEV 1** : Catégories, Tags, Recherche AJAX
- **DEV 2** : Recruteurs, Candidatures, Upload fichiers
- **DEV 3** : Admin Dashboard, Statistiques, Recommandations

---

## 📞 Support

Pour toute question, créer une issue GitHub ou contacter l'équipe.

---

**Développé avec ❤️ en PHP 8 - Architecture MVC sans framework**