<?php
require 'auth.php';   // 🔒 Guard
require 'connexion.php';

if (isset($_GET['ref'])) {
    $ref = trim($_GET['ref']);

    try {
        $req = $bdd->prepare("DELETE FROM Produits WHERE ref = ?");
        $req->execute([$ref]);
        header('Location: chercher.php?deleted=1');
    } catch (PDOException $e) {
        $msg = urlencode('Erreur suppression : ' . $e->getMessage());
        header("Location: chercher.php?error=$msg");
    }
} else {
    header('Location: chercher.php');
}
exit;