# The Lounge List 🎮

**Plateforme de classement compétitif pour Geometry Dash** - Inspirée de Pointercrate, construite avec PHP + MySQL.

## ✨ Fonctionnalités

- **Listes principales** : Main List / Extended List / Legacy List
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

### Option 1 : Homelab (Recommandé) 💻

```bash
# 1. Installer PHP 8.1+ et MySQL/MariaDB sur votre homelab
#    - Docker : docker run -d --name the-lounge-list \
#      -p 80:80 php:8.1-apache
#    - Ou natif selon votre distribution

# 2. Créer une base de données MySQL/MariaDB
#    CREATE DATABASE the_lounge_list CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

# 3. Copier ce projet dans le dossier web root
#    cp -r /chemin/vers/The-Lounge-List/* /var/www/html/the-lounge-list/

# 4. Accéder à l'interface d'installation
#    http://votre-homelab.local/setup.php
```

### Option 2 : Hosting traditionnel

1. Téléchargez ce projet sur votre hébergement
2. Créez une base de données MySQL dans votre panneau d'hébergement
3. Accédez à `https://votre-domaine.com/the-lounge-list/setup.php`
4. Suivez le formulaire d'installation

## 🔧 Configuration

### Variables importantes (config.php)

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

### reCAPTCHA - Désactivation facile 🔒

Pour désactiver reCAPTCHA sans modifier le code :

1. Ouvrez `config.php` (généré par setup.php)
2. Modifiez ces lignes :
   ```php
   'security' => [
       'captcha_enabled' => false,  // Désactive tout reCAPTCHA
       'setup_captcha_enabled' => false,
       // ... autres options
   ],
   ```

### E-mail/Vérification - Configuration SMTP 📧

Pour activer la vérification par e-mail :

1. Configurez votre serveur SMTP dans `config.php` :
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

## 🌐 Langues disponibles

Le projet supporte 15+ langues :

- 🇫🇷 Français (`lang/fr.php`)
- 🇬🇧 Anglais (`lang/en.php`) - Par défaut
- 🇩🇪 Allemand (`lang/de.php`)
- 🇯🇵 Japonais (`lang/jp.php`)
- Et bien d'autres...

Pour ajouter une langue : Copiez `lang/en.php`, renommez-le, et traduisez les valeurs.

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

**Besoin d'aide ?** Consultez le fichier [GUIDE_DEPLOIEMENT.md](./GUIDE_DEPLOIEMENT.md) pour des instructions détaillées étape par étape !
