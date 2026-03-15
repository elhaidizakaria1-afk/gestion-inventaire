<?php
/**
 * auth.php — Guard de session
 * À inclure en tête de CHAQUE page protégée.
 * Redirige vers index.php si l'utilisateur n'est pas connecté.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Empêche tout accès direct à une page protégée
    header('Location: index.php');
    exit;
}