<?php
declare(strict_types=1);

$root = dirname(__DIR__);

// 1) Charger .env (sans dépendance externe)
$envFile = $root . '/.env';
if (is_readable($envFile)) {
  foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
    [$name, $value] = array_map('trim', explode('=', $line, 2));
    $value = trim($value, "\"'");
    if (!getenv($name)) {
      putenv("$name=$value");
      $_ENV[$name] = $value;
    }
  }
}

function env(string $key, $default = null) {
  $value = getenv($key);
  return ($value === false || $value === '') ? $default : $value;
}

define('WP_ENVIRONMENT_TYPE', env('APP_ENV', 'production'));

define('DB_NAME', env('DB_NAME'));
define('DB_USER', env('DB_USER'));
define('DB_PASSWORD', env('DB_PASSWORD'));
define('DB_HOST', env('DB_HOST', '127.0.0.1'));
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');
$table_prefix = env('DB_PREFIX', 'wp_');

define('WP_HOME', env('WP_HOME'));
define('WP_SITEURL', env('WP_SITEURL', rtrim(env('WP_HOME'), '/') . '/wordpress'));

// Important : déplacer wp-content
define('WP_CONTENT_DIR', dirname(__FILE__) . '/app');
define('WP_CONTENT_URL', rtrim(env('WP_HOME'), '/') . '/app');

define('WP_DEBUG', filter_var(env('WP_DEBUG', false), FILTER_VALIDATE_BOOLEAN));
if (WP_DEBUG) {
  define('WP_DEBUG_LOG', true);
  define('WP_DEBUG_DISPLAY', true);
}

// Salts (à remplir)
define('AUTH_KEY', env('AUTH_KEY', 'change-me'));
define('SECURE_AUTH_KEY', env('SECURE_AUTH_KEY', 'change-me'));
define('LOGGED_IN_KEY', env('LOGGED_IN_KEY', 'change-me'));
define('NONCE_KEY', env('NONCE_KEY', 'change-me'));
define('AUTH_SALT', env('AUTH_SALT', 'change-me'));
define('SECURE_AUTH_SALT', env('SECURE_AUTH_SALT', 'change-me'));
define('LOGGED_IN_SALT', env('LOGGED_IN_SALT', 'change-me'));
define('NONCE_SALT', env('NONCE_SALT', 'change-me'));

// Langue du site
define('WPLANG', 'fr_FR');

// Forcer la méthode directe pour les mises à jour (pas de FTP)
define('FS_METHOD', 'direct');

define('ABSPATH', __DIR__ . '/wordpress/');
require_once ABSPATH . 'wp-settings.php';
