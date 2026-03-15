<?php
/**
 * connexion.php
 * Connexion PDO MySQL via variables d'environnement.
 *
 * Variables supportées :
 * - DB_HOST
 * - DB_PORT (optionnel, défaut: 3306)
 * - DB_NAME
 * - DB_USER
 * - DB_PASS
 * - DB_CHARSET (optionnel, défaut: utf8mb4)
 *
 * Pour simplifier le premier déploiement, la table `Produits`
 * est créée automatiquement si elle n'existe pas.
 */

function app_env(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    if ($value === false || $value === '') {
        return $default;
    }
    return $value;
}

$host    = app_env('DB_HOST');
$port    = app_env('DB_PORT', '3306');
$dbname  = app_env('DB_NAME');
$username = app_env('DB_USER');
$password = app_env('DB_PASS');
$charset = app_env('DB_CHARSET', 'utf8mb4');

$missing = [];
foreach (['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'] as $requiredKey) {
    if (app_env($requiredKey) === null) {
        $missing[] = $requiredKey;
    }
}

if (!empty($missing)) {
    die('<div style="background:#1e2230;color:#ff4d6d;font-family:monospace;padding:2rem;border-radius:8px;margin:2rem;line-height:1.6;">'
        . '<strong>Configuration BDD manquante</strong><br>'
        . 'Ajoutez ces variables d\'environnement avant de lancer l\'application :<br>'
        . htmlspecialchars(implode(', ', $missing), ENT_QUOTES)
        . '<br><br><span style="color:#c6d0f5;">Exemple Render :</span><br>'
        . 'DB_HOST=mysql<br>DB_PORT=3306<br>DB_NAME=gestion_inventaire<br>DB_USER=stock_user<br>DB_PASS=mot_de_passe'
        . '</div>');
}

try {
    $bdd = new PDO(
        "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

    $bdd->exec(
        "CREATE TABLE IF NOT EXISTS Produits (
            ref VARCHAR(100) PRIMARY KEY,
            cat VARCHAR(100) NOT NULL,
            nom VARCHAR(255) NOT NULL,
            marque VARCHAR(255) NOT NULL,
            prix DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
} catch (PDOException $e) {
    die('<div style="background:#1e2230;color:#ff4d6d;font-family:monospace;padding:2rem;border-radius:8px;margin:2rem;line-height:1.6;">'
        . '<strong>Erreur de connexion BDD :</strong><br>'
        . htmlspecialchars($e->getMessage(), ENT_QUOTES)
        . '</div>');
}
