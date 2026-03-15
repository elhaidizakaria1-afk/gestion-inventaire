<?php
/**
 * gestionProduits.php — Point d'entrée après le formulaire de connexion.
 * Gère la session et redirige vers la bonne page.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']   ?? '');
    $pass  = trim($_POST['password'] ?? '');

    $adminLogin = getenv('ADMIN_LOGIN') ?: 'admin';
    $adminPass  = getenv('ADMIN_PASSWORD') ?: '0807';

    if ((getenv('RENDER') || getenv('RENDER_SERVICE_ID')) && (!getenv('ADMIN_LOGIN') || !getenv('ADMIN_PASSWORD'))) {
        $_SESSION['login_error'] = 'Variables ADMIN_LOGIN / ADMIN_PASSWORD manquantes sur Render.';
        header('Location: index.php');
        exit;
    }

    if ($login === $adminLogin && $pass === $adminPass) {
        // Régénération de l'ID de session pour éviter la fixation
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user']      = $login;
        header('Location: lister.php');
        exit;
    } else {
        // Accès refusé — stocke le message d'erreur en session
        $_SESSION['login_error'] = 'Login ou mot de passe incorrect.';
        header('Location: index.php');
        exit;
    }
}

// Accès direct via GET → retour à l'accueil
header('Location: index.php');
exit;