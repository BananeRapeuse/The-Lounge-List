# 🚀 Installation Rapide - The Lounge List

Suivez ces étapes pour installer **The Lounge List** en 5 minutes !

---

## ⚡ Méthode la plus simple : Docker (Recommandé)

### Étape 1 : Préparer votre homelab

```bash
# Installer Docker et Docker Compose (si ce n'est pas déjà fait)
# Ubuntu/Debian:
sudo apt install docker.io docker-compose -y

# Windows avec WSL2:
wsl --install
# Puis installez Docker pour WSL2 depuis https://docs.docker.com/desktop/install/windows-install/
```

### Étape 2 : Créer le projet

```bash
# Créez un dossier pour votre homelab (si besoin)
mkdir -p /var/docker/the-lounge-list
cd /var/docker/the-lounge-list

# Clonez ce dépôt (remplacez par votre URL GitHub)
git clone https://github.com/votre-username/The-Lounge-List.git .

# OU copiez manuellement le dossier The-Lounge-List dans ce répertoire
```

### Étape 3 : Configurer les variables d'environnement

```bash
# Copier le fichier modèle en .env
cp .env.example .env

# Éditer avec vos valeurs (nano, vim, ou éditeur préféré)
nano .env
```

**Valeurs à modifier dans `.env` :**

```bash
DB_HOST=db_votre_nom          # Ex: mysql (nom du conteneur MySQL)
DB_PORT=3306                  # Port MySQL (défaut)
DB_NAME=the_lounge_list       # Nom de la base de données
DB_USER=lounge_admin          # Utilisateur MySQL
DB_PASSWORD=votre_mot_de_passe_mysql  # Mot de passe fort !

# Pour commencer sans e-mail et reCAPTCHA :
EMAIL_ENABLED=false           # Désactiver pour commencer
RECAPTCHA_SITE_KEY=""         # Vide = désactivé (peut être activé plus tard)
```

### Étape 4 : Créer docker-compose.yml

Créez un fichier `docker-compose.yml` dans le même dossier :

```yaml
version: '3.8'

services:
  app:
    image: php:8.2-apache
    container_name: the-lounge-list-app
    ports:
      - "8080:80"              # Port externe (modifiez selon vos besoins)
    volumes:
      - ./The-Lounge-List:/var/www/html/the-lounge-list:ro
    depends_on:
      - db
  
  db:
    image: mariadb:10.9
    container_name: the-lounge-list-db
    environment:
      MYSQL_ROOT_PASSWORD:votre_mot_de_passe_root!  # Changez ceci !
      MYSQL_DATABASE:the_lounge_list
      MYSQL_USER:lounge_admin
      MYSQL_PASSWORD:mot_de_passe_utilisateur_123!  # Changez ceci !
    volumes:
      - mysql_data:/var/lib/mysql

volumes:
  mysql_data:
```

### Étape 5 : Lancer !

```bash
# Démarrer les conteneurs Docker
docker-compose up -d

# Vérifier que tout fonctionne
docker-compose ps

# Voir les logs (optionnel)
docker-compose logs -f
```

### Étape 6 : Accéder à l'interface d'installation

Ouvrez votre navigateur et allez sur :

```
http://votre-ip-local:8080/setup.php
```

**Remplissez le formulaire :**

1. **Nom de la liste** : "The Lounge List" (défaut)
2. **Tagline** : Votre slogan personnalisé
3. **Base URL** : `/the-lounge-list` (défaut)
4. **Fuseau horaire** : `Europe/Paris` ou autre selon votre région
5. **Langue par défaut** : Choisissez "fr" pour français

6. **Base de données** : Remplissez avec les valeurs de `.env`
7. **Compte propriétaire** : Créez un compte admin (nom, e-mail optionnel, mot de passe)

8. **Discord** et **reCAPTCHA** : Laissez vides pour commencer !

Cliquez sur **"Installer"** 🎉

---

## 📝 Méthode manuelle (Sans Docker)

### Étape 1 : Installer PHP 8.2 + MySQL/MariaDB

```bash
# Ubuntu/Debian:
sudo apt update
sudo apt install php8.2 php8.2-mysql php8.2-curl php8.2-xml \
    php8.2-mbstring libapache2-mod-php8.2 mariadb-server -y

# CentOS/RHEL:
sudo yum install php php-mysqlnd php-pdo php-curl \
    php-xml php-mbstring mariadb-server -y

# Vérifier l'installation
php -v  # Doit montrer PHP 8.1+
mysql --version  # Doit montrer MySQL/MariaDB 10.4+
```

### Étape 2 : Configurer la base de données

```bash
# Créer la base de données et l'utilisateur
mysql_secure_installation  # Suivez les instructions interactives

# Ou en ligne de commande:
mysql -u root -p << EOF
CREATE DATABASE the_lounge_list CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER 'lounge_admin'@'localhost' IDENTIFIED BY 'votre_mot_de_passe!';
GRANT ALL PRIVILEGES ON the_lounge_list.* TO 'lounge_admin'@'localhost';
FLUSH PRIVILEGES;
EOF

exit
```

### Étape 3 : Copier le code source

```bash
# Créer le dossier web root (modifiez selon votre setup Apache/Nginx)
sudo mkdir -p /var/www/html/the-lounge-list

# Copier les fichiers du projet
cp -r The-Lounge-List/* /var/www/html/the-lounge-list/

# Permissions correctes
sudo chown -R www-data:www-data /var/www/html/the-lounge-list
sudo chmod -R 755 /var/www/html/the-lounge-list
```

### Étape 4 : Configurer Apache/Nginx (si nécessaire)

**Pour Apache :**
```bash
# Créer un site virtuel
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

# Activer et redémarrer Apache
sudo a2ensite the-lounge.list
sudo a2dissite 000-default.conf
sudo systemctl restart apache2
```

### Étape 5 : Accéder à l'installation

Ouvrez votre navigateur :

```
http://votre-domaine.com/the-lounge-list/setup.php
# OU pour local:
http://votre-ip-local:80/the-lounge-list/setup.php
```

Suivez les mêmes étapes que la méthode Docker !

---

## ✅ Vérification post-installation

Après l'installation, vérifiez :

- [ ] La page d'accueil s'affiche correctement
- [ ] Vous pouvez vous connecter avec le compte admin créé
- [ ] Le panneau admin est accessible (`/the-lounge-list/admin`)
- [ ] Les démons sont visibles dans la liste principale

---

## 🔧 Configuration optionnelle (à faire plus tard)

### Activer reCAPTCHA (anti-spam)

1. Allez sur https://www.google.com/recaptcha/admin
2. Créez un nouveau site avec votre domaine
3. Collez les clés dans `.env` ou `config.php` :
   ```bash
   RECAPTCHA_SITE_KEY=votre_clé_site_ici
   RECAPTCHA_SECRET_KEY=votre_clé_secrète_ici
   ```

### Activer l'e-mail (vérification des comptes)

Dans `.env` :
```bash
EMAIL_ENABLED=true
SMTP_HOST=smtp.gmail.com      # ou smtp.office365.com, mail.local, etc.
SMTP_PORT=587                 # TLS ou 465 pour SSL
SMTP_ENCRYPTION=tls          # tls | ssl | none
SMTP_USERNAME=votre@email.com
SMTP_PASSWORD=mot_de_passe_smtp  # App Password si Gmail avec 2FA
```

### Configuration Discord (notifications)

Dans `.env` :
```bash
DISCORD_WEBHOOK_URL=URL_DU_WEBHOOK_ICI
DISCORD_BOT_TOKEN=votre_token_bot_ici
# Ou widget du serveur:
DISCORD_SERVER_WIDGET_URL=https://discord.com/widget?id=VOTRE_ID&theme=dark
```

---

## 🆘 Dépannage Rapide

### Problème : "Erreur de connexion à la base de données"

**Solution :** Vérifiez que MySQL/MariaDB est en cours d'exécution :
```bash
docker-compose ps  # Pour Docker
systemctl status mariadb  # Pour installation native
```

### Problème : "Erreur 500 Internal Server Error"

**Solution :** Vérifiez les logs PHP et Apache/Nginx :
```bash
# Docker:
docker-compose logs app

# Natif (Apache):
sudo tail -f /var/log/apache2/error.log

# Natif (Nginx + PHP-FPM):
sudo tail -f /var/log/nginx/error.log
```

### Problème : "Erreur reCAPTCHA"

**Solution :** Videz les champs reCAPTCHA dans `.env` ou `config.php` pour désactiver temporairement.

---

## 📚 Documentation Complète

Pour plus de détails, consultez :
- [GUIDE_DEPLOIEMENT.md](./GUIDE_DEPLOIEMENT.md) - Guide complet étape par étape
- [README.md](./README.md) - Fonctionnalités et configuration générale
- `.env.example` - Modèle de variables d'environnement avec commentaires

---

## 🎉 Félicitations !

Votre **The Lounge List** est maintenant opérationnel ! 

Si vous rencontrez des problèmes, consultez les logs ou contactez la communauté.

Bon amusement avec Geometry Dash ! 🎮✨
