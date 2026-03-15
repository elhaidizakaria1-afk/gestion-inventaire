<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un produit</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    require 'auth.php';   // 🔒 Guard
    require 'icons.php';
    require 'connexion.php';

    $element = null;
    $msg     = '';
    $msgType = '';

    /**
     * CORRECTION BUG MOBILE :
     * Le problème original : après un POST, si rowCount() >= 0 (toujours vrai),
     * on affichait "succès" mais $element n'était pas rechargé correctement.
     * Sur mobile, le formulaire disparaissait car $element restait null.
     *
     * SOLUTION : on détermine d'abord la référence depuis POST OU GET,
     * puis on charge/recharge le produit SYSTÉMATIQUEMENT avant d'afficher.
     */

    // Déterminer la référence courante (POST prioritaire, sinon GET)
    $refCourante = trim($_POST['ref'] ?? $_GET['ref'] ?? '');

    // ÉTAPE 1 : Traitement POST (modification)
    if (isset($_POST['btn_modifier']) && !empty($refCourante)) {
        $cat    = trim($_POST['cat']    ?? '');
        $nom    = trim($_POST['nom']    ?? '');
        $marque = trim($_POST['marque'] ?? '');
        $prix   = trim($_POST['prix']   ?? '');

        if (strlen($nom) < 2) {
            $msg     = "Le nom est trop court (minimum 2 caractères).";
            $msgType = 'error';
        } elseif (!is_numeric($prix) || floatval($prix) <= 0) {
            $msg     = "Le prix doit être un nombre positif.";
            $msgType = 'error';
        } else {
            try {
                $upd = $bdd->prepare(
                    "UPDATE Produits SET cat = ?, nom = ?, marque = ?, prix = ? WHERE ref = ?"
                );
                $upd->execute([$cat, $nom, $marque, floatval($prix), $refCourante]);
                // rowCount() peut être 0 si les valeurs n'ont pas changé — c'est normal
                $msg     = "Produit modifié avec succès !";
                $msgType = 'success';
            } catch (PDOException $e) {
                $msg     = "Erreur SQL : " . htmlspecialchars($e->getMessage());
                $msgType = 'error';
            }
        }

        $safeMsg = htmlspecialchars($msg, ENT_QUOTES);
        echo "<script>window.addEventListener('DOMContentLoaded',()=>showToast('$safeMsg','$msgType'));</script>";
    }

    // ÉTAPE 2 : Chargement du produit (TOUJOURS, que ce soit GET ou après POST)
    // C'est ici la correction principale : on recharge systématiquement depuis la BDD
    if (!empty($refCourante)) {
        $req = $bdd->prepare("SELECT * FROM Produits WHERE ref = ?");
        $req->execute([$refCourante]);
        $element = $req->fetch();

        if (!$element) {
            // Essai insensible à la casse
            $req = $bdd->prepare("SELECT * FROM Produits WHERE LOWER(ref) = LOWER(?)");
            $req->execute([$refCourante]);
            $element = $req->fetch();
        }
    }

    // Normalisation des clés en minuscules
    if ($element) {
        $element = array_change_key_case($element, CASE_LOWER);
    }
    ?>

    <nav class="nav">
        <div class="nav-brand"><?php echo icon_svg('logo'); ?> <span>STOCK</span> MANAGER</div>
        <ul class="nav-links">
            <li><a href="ajouter.php"><?php echo icon_svg('add'); ?> Ajouter</a></li>
            <li><a href="chercher.php"><?php echo icon_svg('search'); ?> Chercher</a></li>
            <li><a href="lister.php"><?php echo icon_svg('list'); ?> Liste</a></li>
            <li><a href="deconnexion.php" class="nav-logout"><?php echo icon_svg('x'); ?> Déconnexion</a></li>
        </ul>
    </nav>

    <div class="page-wrapper">
        <h2 class="page-title">Modifier un produit</h2>

        <?php if ($element): ?>
            <div class="card">
                <!-- CORRECTION : action="" + method="post" explicite pour mobile -->
                <form action="modifier.php" method="POST" id="form-modifier" novalidate>
                    <!-- Référence cachée — toujours présente pour le POST -->
                    <input type="hidden" name="ref" value="<?php echo htmlspecialchars($element['ref']); ?>">

                    <div class="form-fields">
                        <div class="field-row">
                            <label class="field-label">Référence</label>
                            <div>
                                <code style="color:#6b728f;font-family:'DM Mono',monospace;"><?php echo htmlspecialchars($element['ref']); ?></code>
                            </div>
                        </div>
                        <div class="field-row">
                            <label class="field-label" for="cat">Catégorie</label>
                            <select name="cat" id="cat">
                                <option value="ordinateur" <?php echo ($element['cat'] === 'ordinateur') ? 'selected' : ''; ?>>Ordinateur</option>
                                <option value="telephone" <?php echo ($element['cat'] === 'telephone')  ? 'selected' : ''; ?>>Téléphone</option>
                                <option value="television" <?php echo ($element['cat'] === 'television') ? 'selected' : ''; ?>>Télévision</option>
                            </select>
                        </div>
                        <div class="field-row">
                            <label class="field-label" for="nom">Nom</label>
                            <input type="text" name="nom" id="nom"
                                value="<?php echo htmlspecialchars($element['nom']); ?>"
                                placeholder="Nom du produit">
                        </div>
                        <div class="field-row">
                            <label class="field-label" for="marque">Marque</label>
                            <input type="text" name="marque" id="marque"
                                value="<?php echo htmlspecialchars($element['marque']); ?>"
                                placeholder="Marque">
                        </div>
                        <div class="field-row">
                            <label class="field-label" for="prix">Prix (DH)</label>
                            <input type="number" name="prix" id="prix"
                                value="<?php echo htmlspecialchars($element['prix']); ?>"
                                min="0.01" step="0.01">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="btn_modifier"><?php echo icon_svg('check'); ?> Enregistrer</button>
                        <button type="button" onclick="window.location.href='chercher.php'">Annuler</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="icon"><?php echo icon_svg('question'); ?></div>
                <p>Produit introuvable. <a href="chercher.php" style="color:#00b37a;">Retour à la recherche</a>.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="script.js"></script>
</body>

</html>