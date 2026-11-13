<?php
/**
 * Script utilitaire pour générer config.php à partir de .env.example ou variables d'environnement.
 *
 * Usage : php generate_config.php [nom_app] [chemin_base_url] [langue_defaut]
 */

declare(strict_types=1);

// Lecture des variables d'environnement (fichier .env ou système)
function loadEnv(string $path): void {
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorer les commentaires et lignes vides
        if (strpos(trim($line), '#') === 0 || trim($line) === '') {
            continue;
        }

        // Parser KEY=VALUE ou KEY="VALUE"
        if (preg_match('/^([A-Z_]+)=(.*)$/i', trim($line), $matches)) {
            $key = $matches[1];
            $value = str_replace(['"', "'"], '', $matches[2]); // Enlever les guillemets
            putenv("$key=$value");
        }
    }
}

// Charger .env si existe
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    loadEnv($envFile);
}

// Valeurs par défaut avec override par variables d'environnement
$config = [
    'app' => [
        'name' => getenv('APP_NAME') ?: 'The Lounge List',
        'ratedplus_enabled' => true,
        'brand_mode' => 1, // 1 = texte, 2 = logo
        'logo_path' => 'logo.png',
        'logo_height' => 22,
        'logo_max_width' => 130,
        'tagline' => getenv('APP_TAGLINE') ?: 'A competitive Geometry Dash ranking platform.',
        'author' => '', // À personnaliser
        'base_url' => getenv('BASE_URL') ?: '/the-lounge-list',
        'public_url' => getenv('PUBLIC_URL') ?: '',
        'timezone' => getenv('APP_TIMEZONE') ?: 'UTC',
        'default_language' => getenv('DEFAULT_LANGUAGE') ?: 'en',
        'debug' => filter_var(getenv('DEBUG_MODE'), FILTER_VALIDATE_BOOLEAN) ?? false,
        'updated' => 0,
        'installation_complete' => false,
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => (int)(getenv('DB_PORT') ?: '3306'),
        'database' => getenv('DB_NAME') ?: '', // Sera rempli par setup.php
        'username' => getenv('DB_USER') ?: '', // Sera rempli par setup.php
        'password' => getenv('DB_PASSWORD') ?: '', // Sera rempli par setup.php
        'charset' => 'utf8mb4',
    ],
    'discord' => [
        'webhook_url' => getenv('DISCORD_WEBHOOK_URL') ?: '',
        'bot_token' => getenv('DISCORD_BOT_TOKEN') ?: '',
        'bot_api_base_url' => 'https://discord.com/api/v10',
        'server_widget_url' => getenv('DISCORD_SERVER_WIDGET_URL') ?: '',
        'server_id' => preg_replace('/\D+/', '', getenv('DISCORD_SERVER_ID')) ?: '',
        'server_theme' => getenv('DISCORD_SERVER_THEME') ?: 'dark',
    ],
    'email' => [
        'enabled' => filter_var(getenv('EMAIL_ENABLED'), FILTER_VALIDATE_BOOLEAN) ?? false,
        'smtp_host' => getenv('SMTP_HOST') ?: '',
        'smtp_port' => (int)(getenv('SMTP_PORT') ?: '587'),
        'smtp_encryption' => getenv('SMTP_ENCRYPTION') ?: 'tls',
        'smtp_username' => getenv('SMTP_USERNAME') ?: '',
        'smtp_password' => getenv('SMTP_PASSWORD') ?: '',
        'from_address' => getenv('FROM_ADDRESS') ?: '', // Sera rempli par setup.php
        'from_name' => getenv('FROM_NAME') ?: 'The Lounge List',
    ],
    'updates' => [
        'github_repository' => getenv('GITHUB_REPOSITORY') ?: 'https://github.com/kacygd/pointercrate-php',
        'ref' => getenv('UPDATE_REF') ?: 'main',
    ],
    'security' => [
        'trust_proxy' => filter_var(getenv('TRUST_PROXY'), FILTER_VALIDATE_BOOLEAN) ?? false,
        'captcha_enabled' => getenv('RECAPTCHA_SITE_KEY') !== '' && getenv('RECAPTCHA_SECRET_KEY') !== '',
        'setup_captcha_enabled' => getenv('RECAPTCHA_SITE_KEY') !== '' && getenv('RECAPTCHA_SECRET_KEY') !== '',
        'captcha_login_enabled' => true,
        'captcha_register_enabled' => true,
        'captcha_submit_enabled' => true,
        'captcha_driver' => 'google_recaptcha_v2',
        'recaptcha_site_key' => getenv('RECAPTCHA_SITE_KEY') ?: '',
        'recaptcha_secret_key' => getenv('RECAPTCHA_SECRET_KEY') ?: '',
        'recaptcha_verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
        'csrf_enabled' => true,
        'session_cookie_httponly' => true,
        'session_cookie_samesite' => 'Lax',
        'session_cookie_secure' => filter_var(getenv('SESSION_COOKIE_SECURE'), FILTER_VALIDATE_BOOLEAN) ?? false,
    ],
];

// Afficher le contenu généré (pour vérification)
echo "Configuration générée pour : {$config['app']['name']}\n\n";
echo "=== APPLICATION ===\n";
print_r($config['app']);
echo "\n=== BASE DE DONNÉES ===\n";
print_r($config['db']);
echo "\n=== E-MAIL ===\n";
print_r($config['email']);
echo "\n=== DISCORD ===\n";
print_r($config['discord']);

// Générer config.php si demandé ou si variables DB sont remplies
if (getenv('DB_NAME') !== '' && getenv('DB_USER') !== '') {
    $config['db']['database'] = getenv('DB_NAME');
    $config['db']['username'] = getenv('DB_USER');
    $config['db']['password'] = getenv('DB_PASSWORD');

    // Sauvegarder config.php
    $outputPath = __DIR__ . '/config.php';
    if (file_put_contents($outputPath, "<?php\ndeclare(strict_types=1;\n\nreturn " . var_export($config, true) . ";\n") !== false) {
        echo "\n✅ config.php généré avec succès à : $outputPath\n";

        // Supprimer setup.php pour éviter la confusion (optionnel)
        if (file_exists(__DIR__ . '/setup.php')) {
            echo "⚠️  setup.php existe toujours. Pour installer, accédez à http://votre-site/setup.php\n";
        } else {
            echo "✅ setup.php a été supprimé (déjà présent ou n'existe pas)\n";
        }
    } else {
        echo "\n❌ Échec de l'écriture de config.php\n";
    }
} else {
    echo "\n⚠️  Variables DB non complètes. Pour installer, accédez à http://votre-site/setup.php\n";
}
