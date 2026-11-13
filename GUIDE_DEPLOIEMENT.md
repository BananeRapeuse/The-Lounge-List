# 📖 Guide de Déploiement - The Lounge List

Ce guide vous expliquera comment installer et configurer **The Lounge List** sur votre homelab ou hébergement traditionnel.

---

## 🎯 Table des Matières

1. [Prérequis](#-prérequis)
2. [Installation sur Homelab (Docker)](#-installation-sur-homelab-docker)
3. [Installation sur Homelab (Natif)](#-installation-sur-homelab-natif)
4. [Configuration de la Base de Données](#-configuration-de-la-base-de-données)
5. [Finalisation de l'Installation](#-finalisation-de-linstallation)
6. [Vérification du Fonctionnement](#-vérification-du-fonctionnement)
7. [Dépannage](#-dépannage)

---

## 📋 Prérequis

### Système d'exploitation
- Windows, Linux (Ubuntu/Debian), ou macOS

### Logiciels requis
- **PHP 8.1+** avec extensions : `pdo_mysql`, `mbstring`, `openssl`, `curl`
- **MySQL/MariaDB 10.4+**
- Un serveur web (**Apache**, **Nginx**, ou PHP-FPM)

### Pour Docker (recommandé)
```bash
docker --version  # Doit être >= 20.10
docker-compose --version  # Optionnel mais recommandé
```

---

## 🐳 Installation sur Homelab (Docker) - Méthode Recommandée

### Étape 1 : Créer le dossier de projet

```bash
# Sur votre homelab, créez un dossier pour le projet
mkdir -p /var/docker/the-lounge-list
cd /var/docker/the-lounge-list
```

### Étape 2 : Télécharger le code source

```bash
# Clonez ce dépôt (remplacez par votre URL GitHub si vous en avez une)
git clone https://github.com/votre-username/The-Lounge-List.git .

# OU téléchargez via wget/curl depuis la page GitHub
```

### Étape 3 : Configurer les variables d'environnement

```bash
# Copier le fichier .env.example en .env
cp .env.example .env

# Éditer le fichier .env avec vos valeurs réelles
nano .env  # ou vim, ou votre éditeur préféré
```

**Valeurs importantes à modifier dans `.env` :**

```bash
DB_HOST=db_votre_nom  # Nom du conteneur MySQL (ex: mysql)
DB_NAME=the_lounge_list
DB_USER=lounge_admin
DB_PASSWORD=votre_mot_de_passe_sécurisé_123

EMAIL_ENABLED=true      # Activer si vous voulez la vérification par e-mail
SMTP_HOST=smtp.votre-fournisseur.com  # Ex: smtp.gmail.com, mail.local
```

### Étape 4 : Créer le fichier docker-compose.yml

Créez un fichier `docker-compose.yml` dans le même dossier :

```yaml
version: '3.8'

services:
  app:
    image: php:8.2-apache
    container_name: the-lounge-list-app
    ports:
      - "8080:80"  # Port externe (modifiez selon vos besoins)
    volumes:
      - ./The-Lounge-List:/var/www/html/the-lounge-list:ro
    depends_on:
      - db
  
  db:
    image: mariadb:10.9
    container_name: the-lounge-list-db
    environment:
      MYSQL_ROOT_PASSWORD: root_password_changer_avant_production!
      MYSQL_DATABASE: the_lounge_list
      MYSQL_USER: lounge_admin
      MYSQL_PASSWORD: secure_password_123!  # Changez ceci !
    volumes:
      - mysql_data:/var/lib/mysql
    ports:
      - "3306:3306"  # Optionnel pour accès direct
  
  nginx-proxy (optionnel):
    image: nginx:alpine
    container_name: the-lounge-list-nginx
    ports:
      - "80:80"
    volumes:
      - ./The-Lounge-List:/var/www/html/the-lounge-list:ro
      - ./nginx.conf:/etc/nginx/conf.d/default.conf:ro

volumes:
  mysql_data:
```

### Étape 5 : Lancer les conteneurs

```bash
# Démarrer tous les services Docker
docker-compose up -d

# Vérifier que tout fonctionne
docker-compose ps

# Voir les logs en temps réel (optionnel)
docker-compose logs -f
```

### Étape 6 : Accéder à l'interface d'installation

Ouvrez votre navigateur et allez sur :

```
http://votre-homelab.local:8080/setup.php
# OU si vous avez configuré le proxy Nginx :
http://votre-ip-local:80/setup.php
```

---

## 💻 Installation sur Homelab (Natif) - Sans Docker

### Étape 1 : Installer PHP 8.2 avec extensions

#### Sur Ubuntu/Debian :
```bash
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-common \
    php8.2-mysql php8.2-curl php8.2-xml php8.2-mbstring \
    libapache2-mod-php8.2  # Pour Apache

# Vérifier l'installation
php -v
```

#### Sur CentOS/RHEL :
```bash
sudo yum install php php-mysqlnd php-pdo php-curl php-xml php-mbstring
```

### Étape 2 : Installer MySQL/MariaDB

#### Option A : Utiliser Docker pour MySQL (recommandé)
```bash
docker run -d \
  --name the-lounge-list-db \
  --publish 3306:3306 \
  --env MYSQL_ROOT_PASSWORD=votre_mot_de_passe_sécurisé! \
  --env MYSQL_DATABASE=the_lounge_list \
  --env MYSQL_USER=lounge_admin \
  --env MYSQL_PASSWORD=mot_de_passe_utilisateur_123! \
  mariadb:10.9
```

#### Option B : Installer MariaDB natif (Ubuntu)
```bash
sudo apt install mariadb-server -y

# Créer la base de données et l'utilisateur
mysql -u root -p << EOF
CREATE DATABASE the_lounge_list CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER 'lounge_admin'@'localhost' IDENTIFIED BY 'secure_password_123!';
GRANT ALL PRIVILEGES ON the_lounge_list.* TO 'lounge_admin'@'localhost';
FLUSH PRIVILEGES;
EOF

exit
```

### Étape 3 : Configurer Apache/Nginx (si nécessaire)

#### Pour Apache :
```bash
# Créer un site virtuel /votre-site/the-lounge-list/
sudo nano /etc/apache2/sites-available/the-lounge.list

<VirtualHost *:80>
    ServerName the-lounge-list.local
    DocumentRoot /var/www/html/the-lounge-list
    
    <Directory /var/www/html/the-lounge-list>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/the-lounge-error.log
    CustomLog ${APACHE_LOG_DIR}/the-lounge-access.log combined
</VirtualHost>

# Activer le site virtuel et PHP
sudo a2ensite the-lounge.list
sudo a2dissite 000-default.conf
sudo systemctl restart apache2
```

### Étape 4 : Copier le code source

```bash
# Créer le dossier web root (modifiez selon votre setup)
mkdir -p /var/www/html/the-lounge-list

# Copier les fichiers du projet
cp -r The-Lounge-List/* /var/www/html/the-lounge-list/

# Permissions correctes
sudo chown -R www-data:www-data /var/www/html/the-lounge-list
sudo chmod -R 755 /var/www/html/the-lounge-list
```

### Étape 5 : Configurer le fichier .env (optionnel mais recommandé)

Créez un fichier `.env` dans le dossier du projet avec vos valeurs :

```bash
nano /var/www/html/the-lounge-list/.env
```

Contenu minimal pour commencer :
```bash
APP_NAME="The Lounge List"
DB_HOST=localhost
DB_PORT=3306
DB_NAME=the_lounge_list
DB_USER=lounge_admin
DB_PASSWORD=votre_mot_de_passe_mysql

EMAIL_ENABLED=false  # Commencez par false, configurez SMTP plus tard
RECAPTCHA_SITE_KEY=""  # Vide = désactivé pour commencer
```

---

## 🗄️ Configuration de la Base de Données

### Vérifier que MySQL/MariaDB fonctionne

```bash
# Tester la connexion (remplacez les valeurs)
mysql -h localhost -u lounge_admin -p the_lounge_list -e "SELECT VERSION();"
```

### Importer le schéma de base de données (si nécessaire)

Le projet contient déjà `db/schema.sql` qui sera exécuté automatiquement par setup.php.

Mais si vous voulez l'importer manuellement :

```bash
mysql -u lounge_admin -p the_lounge_list < db/schema.sql
```

---

## ✅ Finalisation de l'Installation

### Étape 1 : Accéder à la page d'installation

Ouvrez votre navigateur et allez sur :

```
http://votre-domaine.com/the-lounge-list/setup.php
# OU pour homelab local :
http://votre-ip-local:8080/setup.php
```

### Étape 2 : Remplir le formulaire d'installation

Vous verrez un formulaire avec plusieurs sections :

#### Section 1 : Liste (List)
- **Name** : "The Lounge List" (défaut, modifiable)
- **Tagline** : Votre slogan personnalisé
- **Base URL** : `/the-lounge-list` (chemin de l'application)
- **Public URL** : Laissez vide pour commencer

#### Section 2 : Base de données (Database)
- **Host** : `localhost` ou l'adresse de votre serveur MySQL
- **Port** : `3306` (défaut)
- **Name** : Nom de la base de données créée
- **User** : Utilisateur MySQL avec permissions
- **Password** : Mot de passe MySQL

#### Section 3 : Discord (Optionnel - laisser vide pour commencer)
Laissez tous les champs vides si vous ne voulez pas d'intégration Discord maintenant.

#### Section 4 : Propriétaire (Owner)
- **Username** : Nom d'utilisateur admin (ex: `admin`, `owner`)
- **Email** : Votre e-mail (optionnel mais recommandé pour la récupération de mot de passe)
- **Password** : Mot de passe fort (min. 8 caractères)
- **Confirm Password** : Répétez le mot de passe

#### Section 5 : Sécurité (Optionnel - reCAPTCHA)
Laissez les champs vides si vous ne voulez pas activer reCAPTCHA maintenant.

#### Section 6 : E-mail (Optionnel)
Désactivez pour commencer (`email_enabled` vide ou non coché).

### Étape 3 : Cliquer sur "Install"

Cliquez sur le bouton **Install** en bas du formulaire.

Le script va :
1. Créer/configurer `config.php`
2. Importer le schéma de base de données (`db/schema.sql`)
3. Créer votre compte administrateur
4. Supprimer `setup.php` (sécurité)

### Étape 4 : Redirection vers la page principale

Vous devriez être redirigé vers la page d'accueil du site ! 🎉

---

## 🔍 Vérification du Fonctionnement

### Tests rapides à effectuer :

1. **Page d'accueil**
   ```
   http://votre-domaine.com/the-lounge-list/
   ```
   - Le logo/nom s'affiche-t-il ? ✅
   - La liste des démonstrations est-elle visible ? ✅

2. **Connexion utilisateur** (page `/login`)
   - Le formulaire de connexion fonctionne-t-il ? ✅
   - Les messages d'erreur sont-ils clairs ? ✅

3. **Soumission d'enregistrement** (page `/submit`)
   - Le formulaire s'affiche-t-il correctement ? ✅

4. **Panneau admin** (page `/admin` après connexion)
   - Pouvez-vous accéder au panneau admin ? ✅
   - Les tableaux de données sont-ils visibles ? ✅

### Vérifier les logs PHP (si problème)

```bash
# Pour Apache
sudo tail -f /var/log/apache2/error.log

# Pour Nginx + PHP-FPM
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/php-fpm/error.log
```

---

## 🔧 Dépannage

### Problème : "Erreur de connexion à la base de données"

**Solutions :**
1. Vérifiez que MySQL/MariaDB est en cours d'exécution : `docker-compose ps` ou `systemctl status mariadb`
2. Vérifiez les credentials dans `config.php` (host, user, password)
3. Testez la connexion manuellement : `mysql -h localhost -u utilisateur -p`

### Problème : "Erreur 500 Internal Server Error"

**Solutions :**
1. Vérifiez que PHP 8.1+ est installé (`php -v`)
2. Vérifiez les logs d'erreur Apache/Nginx
3. Assurez-vous que toutes les extensions PHP requises sont installées :
   ```bash
   php -m | grep -E "pdo_mysql|mbstring|openssl|curl"
   ```

### Problème : "Erreur de configuration reCAPTCHA"

**Solutions :**
1. Videz `RECAPTCHA_SITE_KEY` et `RECAPTCHA_SECRET_KEY` dans `.env` ou `config.php`
2. Ou obtenez des clés valides sur [Google reCAPTCHA](https://www.google.com/recaptcha/admin)

### Problème : "E-mails non envoyés"

**Solutions :**
1. Vérifiez que le serveur SMTP est accessible (`telnet smtp.gmail.com 587`)
2. Pour Gmail, utilisez un "App Password" si vous avez 2FA activé
3. Désactivez temporairement `EMAIL_ENABLED` pour continuer sans vérification

### Problème : "Page blanche ou CSS manquant"

**Solutions :**
1. Videz le cache du navigateur (Ctrl+F5)
2. Vérifiez que les fichiers CSS/JS sont présents dans `/assets/`
3. Vérifiez les permissions des fichiers (`chmod 644 assets/*`)

---

## 🎨 Personnalisation Rapide

### Changer le nom et le slogan du site

Éditez `config.php` :
```php
'app' => [
    'name' => 'The Lounge List',      // Votre nom personnalisé
    'tagline' => 'Votre slogan ici...', // Votre description
],
```

### Ajouter votre logo

1. Placez votre fichier `logo.png` dans le dossier racine du projet
2. Dans `config.php`, ajustez :
   ```php
   'logo_height' => 50,      // Hauteur en pixels (défaut: 22)
   'logo_max_width' => 180,  // Largeur max (défaut: 130)
   ```

### Changer la langue par défaut

Dans `config.php` :
```php
'default_language' => 'fr',  // Code langue (voir lang/*.php pour les options)
```

---

## 📚 Ressources Supplémentaires

- **Documentation officielle** : Voir le dépôt GitHub original
- **Forum de discussion** : [Lien vers forum si applicable]
- **Communauté Discord** : [Lien vers serveur Discord]

---

## 🔐 Sécurité - Checklist Post-Installation

Après l'installation, vérifiez ces points :

- [ ] `config.php` n'est pas en version publique (gitignore) ✅
- [ ] Mot de passe MySQL est fort et unique ✅
- [ ] Mot de passe admin est changé depuis les valeurs par défaut ✅
- [ ] reCAPTCHA est configuré si nécessaire pour la production ✅
- [ ] Les logs d'erreur ne sont pas exposés en production (`debug => false`) ✅

---

## 🎉 Félicitations !

Votre **The Lounge List** est maintenant opérationnel ! 

Si vous rencontrez des problèmes, consultez les logs ou contactez la communauté.

Bon amusement avec Geometry Dash ! 🎮✨
