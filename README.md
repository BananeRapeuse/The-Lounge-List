# The Lounge List 🎮

**Plateforme de classement compétitif pour Geometry Dash** - Inspirée de Pointercrate, construite avec PHP + MySQL.

## ✨ Fonctionnalités

- **Listes principales** : Main List / Platformer List / Challenge List
- **Liste secondaire** : In-Completion List
- **Détails des niveaux** : Scoring, records et historique des positions
- **Soumission d'enregistrements** : Validation par la communauté
- **Panneau d'administration** : Gestion des niveaux, rôles utilisateurs
- **Notifications Discord** : Webhooks et widget serveur (optionnel)
- **Vérification e-mail** : Activation via SMTP (optionnel)
- **Multi-langues** : 15+ langues disponibles

## 🛠️ Requirements

- PHP >= 8.1
- MySQL ou MariaDB >= 10.4
- reCAPTCHA v2 (optionnel, désactivable facilement)

## 📦 Installation Rapide

### ⚠️ Important : GitHub Pages n'est PAS supporté !

GitHub Pages ne supporte **pas PHP nativement**. Utilisez plutôt :
- Votre **homelab** (Docker ou PHP natif) - Recommandé ✅
- Un **hébergement traditionnel** (Hostinger, OVH, etc.)
- Docker sur Windows/Linux/Mac avec WSL2

### Option 1 : Homelab avec Docker (Recommandé) 💻

```bash
# Étape 1 : Préparer l'environnement
mkdir -p /var/docker/the-lounge-list
cd /var/docker/the-lounge-list

# Étape 2 : Clonez ce dépôt ou copiez manuellement les fichiers
git clone https://github.com/votre-username/The-Lounge-List.git .

# Étape 3 : Configurer les variables d'environnement
cp .env.example .env
nano .env  # Modifier DB_HOST, DB_NAME, etc. (voir .env.example)

# Étape 4 : Créer docker-compose.yml (voir GUIDE_DEPLOIEMENT.md ou INSTALLATION.md)

# Étape 5 : Lancer les conteneurs
docker-compose up -d

# Étape 6 : Accéder à l'interface d'installation
http://votre-ip-local:8080/setup.php
```

### Option 2 : Homelab sans Docker (PHP natif)

Voir [INSTALLATION.md](./INSTALLATION.md) pour les instructions détaillées.

### Option 3 : Hébergement traditionnel (Hostinger, OVH, etc.)

1. Téléchargez ce projet sur votre hébergement
2. Créez une base de données MySQL dans votre panneau d'hébergement
3. Accédez à `https://votre-domaine.com/the-lounge-list/setup.php`
4. Suivez le formulaire d'installation

## 🔧 Configuration

### 📖 Documentation Complète

| Fichier | Description |
|---------|-------------|
| [INSTALLATION.md](./INSTALLATION.md) | Guide d'installation rapide (5 minutes !) |
| [GUIDE_DEPLOIEMENT.md](./GUIDE_DEPLOIEMENT.md) | Guide complet étape par étape avec Docker et natif |
| `.env.example` | Modèle de variables d'environnement avec commentaires détaillés |

### Variables importantes (config.php - généré automatiquement)

| Variable | Description | Valeur par défaut |
|----------|-------------|-------------------|
| `name` | Nom du site | "The Lounge List" |
| `tagline` | Slogan du site | Personnalisable |
| `base_url` | Chemin de l'application | `/the-lounge-list` |
| `public_url` | URL publique complète (optionnel) | Vide par défaut |
| `timezone` | Fuseau horaire du serveur | UTC (modifiable) |

### Base de données

Le projet utilise MySQL/MariaDB avec les tables suivantes :
- **users** : Utilisateurs et rôles
- **demons** : Liste des niveaux Geometry Dash
- **submissions** : Soumissions d'enregistrements à valider
- **level_comments** : Commentaires sur les niveaux
- **discord_*** : Configuration Discord (optionnel)

### reCAPTCHA v2 - Désactivable Facilement !

**Déjà implémenté !** Dans `setup.php`, vous verrez une checkbox :

> [ ] **Désactiver Google reCAPTCHA**

- ✅ **Décochez** = reCAPTCHA activé (protection anti-spam)
- ✅ **Cochez** = reCAPTCHA désactivé (pour débuter sans configuration)

Pour réactiver plus tard, ajoutez simplement vos clés reCAPTCHA :

1. Allez sur https://www.google.com/recaptcha/admin
2. Créez un nouveau site avec votre domaine
3. Collez les clés dans `.env` ou `config.php` :
   ```bash
   RECAPTCHA_SITE_KEY=votre_clé_site_ici
   RECAPTCHA_SECRET_KEY=votre_clé_secrète_ici
   ```

**Par défaut, reCAPTCHA est désactivé pour faciliter l'installation initiale.**

### E-mail/Vérification SMTP - Activable via Checkbox !

Pour activer la vérification par e-mail, cochez simplement la case dans `setup.php` lors de l'installation. Ensuite, configurez votre serveur SMTP :
   ```php
   'email' => [
       'enabled' => true,           // Activer l'e-mail
       'smtp_host' => 'smtp.gmail.com',  // Ex: smtp.gmail.com, smtp.office365.com
       'smtp_port' => 587,          // TLS ou SSL
       'smtp_encryption' => 'tls',  // tls | ssl | none
       'smtp_username' => '',       // Votre adresse e-mail
       'smtp_password' => '',       // Votre mot de passe/app password
   ],
   ```

2. Exemple Gmail :
   - `smtp_host`: `smtp.gmail.com`
   - `smtp_port`: `587` (TLS) ou `465` (SSL)
   - Utilisez un "App Password" si vous avez 2FA activé

### Discord - Configuration optionnelle 🎮

Pour activer les notifications Discord plus tard :

1. Créez un bot sur [Discord Developer Portal](https://discord.com/developers/applications)
2. Obtenez le webhook URL ou le token de bot
3. Ajoutez dans `config.php` :
   ```php
   'discord' => [
       'webhook_url' => '',         // Pour notifications publiques
       'bot_token' => '',           // Pour DM avec les utilisateurs
       'server_widget_url' => '',   // Widget du serveur Discord (optionnel)
   ],
   ```

## 🌐 Langues disponibles (15+)

Le projet supporte de nombreuses langues :

- 🇫🇷 **Français** (`lang/fr.php`) - Par défaut si sélectionné lors de l'installation
- 🇬🇧 Anglais (`lang/en.php`)
- 🇩🇪 Allemand (`lang/de.php`)
- 🇯🇵 Japonais (`lang/jp.php`)
- Et bien d'autres (cn, du, id, it, kr, pl, pt, ru, sp, tr, ua, vi...)

Pour ajouter une langue : Copiez `lang/en.php`, renommez-le avec le code ISO (ex: `lang/ar.php`), et traduisez les valeurs.

## 🔐 Sécurité par défaut

- Hashage des mots de passe (bcrypt)
- Protection CSRF activée
- Cookies de session sécurisés
- Validation SQL préparée (PDO)
- Limitation des tentatives de connexion
- IP banning (table `ip_bans`)

## 📝 Développement

Pour activer le mode debug :

```php
'debug' => true,  // Dans config.php
```

Cela affichera plus d'informations dans les logs PHP.

## 🤝 Contribuer

Ce projet est open-source ! Pour contribuer :
1. Fork le dépôt
2. Créez une branche (`git checkout -b feature/nouveau-fonctionnement`)
3. Committez vos changements (`git commit -m 'Ajout d\'une fonctionnalité'`)
4. Ouvrez une Pull Request

## 📄 Licence

Voir fichier `LICENSE` pour les détails de la licence.

---

<div align="center">
  <strong>🎮 The Lounge List - La plateforme de classement Geometry Dash</strong><br>
  <small>Inspirée de Pointercrate, construite avec ❤️ et PHP</small><br><br>
  <a href="./INSTALLATION.md"><strong>🚀 Installer maintenant →</strong></a> | 
  <a href="./GUIDE_DEPLOIEMENT.md"><strong>📖 Documentation complète →</strong></a>
</div>

---

**Besoin d'aide ?** Consultez [INSTALLATION.md](./INSTALLATION.md) pour installer en 5 minutes !
