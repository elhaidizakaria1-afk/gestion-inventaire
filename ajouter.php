<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<?php
require 'auth.php';   // 🔒 Guard
require 'icons.php';
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
        <h2 class="page-title">Ajouter un produit</h2>

        <?php
        require 'connexion.php';

        $msg     = '';
        $msgType = '';

        if (isset($_POST['ajouter'])) {
            $ref    = trim($_POST['ref']       ?? '');
            $cat    = trim($_POST['categorie'] ?? '');
            $nom    = trim($_POST['nom']       ?? '');
            $marque = trim($_POST['marque']    ?? '');
            $prix   = trim($_POST['prix']      ?? '');

            if (strlen($ref) < 2) {
                $msg     = "La référence est trop courte (minimum 2 caractères).";
                $msgType = 'error';
            } elseif (strlen($nom) < 2) {
                $msg     = "Le nom est trop court (minimum 2 caractères).";
                $msgType = 'error';
            } elseif (!is_numeric($prix) || floatval($prix) <= 0) {
                $msg     = "Le prix doit être un nombre positif.";
                $msgType = 'error';
            } else {
                try {
                    $req = $bdd->prepare("SELECT COUNT(*) FROM Produits WHERE LOWER(ref) = LOWER(?)");
                    $req->execute([$ref]);

                    if ((int)$req->fetchColumn() > 0) {
                        $msg     = "Ce produit existe déjà (référence : $ref).";
                        $msgType = 'error';
                    } else {
                        $insert = $bdd->prepare(
                            "INSERT INTO Produits (ref, cat, nom, marque, prix) VALUES (?, ?, ?, ?, ?)"
                        );
                        $insert->execute([$ref, $cat, $nom, $marque, floatval($prix)]);

                        if ($insert->rowCount() === 1) {
                            $msg     = "Produit ajouté avec succès !";
                            $msgType = 'success';
                            $_POST   = [];
                        } else {
                            $msg     = "L'insertion a échoué. Vérifiez la structure de la table.";
                            $msgType = 'error';
                        }
                    }
                } catch (PDOException $e) {
                    $msg     = "Erreur SQL : " . htmlspecialchars($e->getMessage());
                    $msgType = 'error';
                }
            }

            $safeMsg = htmlspecialchars($msg, ENT_QUOTES);
            echo "<script>window.addEventListener('DOMContentLoaded',()=>showToast('$safeMsg','$msgType'));</script>";
        }
        ?>

        <div class="card">
            <form action="" method="post" id="form-ajouter" novalidate>
                <div class="form-fields">
                    <div class="field-row">
                        <label class="field-label">Catégorie</label>
                        <select name="categorie" id="categorie">
                            <option value="ordinateur" <?php echo (($_POST['categorie'] ?? '') === 'ordinateur') ? 'selected' : ''; ?>>Ordinateur</option>
                            <option value="telephone"  <?php echo (($_POST['categorie'] ?? '') === 'telephone')  ? 'selected' : ''; ?>>Téléphone</option>
                            <option value="television" <?php echo (($_POST['categorie'] ?? '') === 'television') ? 'selected' : ''; ?>>Télévision</option>
                        </select>
                    </div>
                    <div class="field-row">
                        <label class="field-label" for="ref">Référence</label>
                        <input type="text" name="ref" id="ref" placeholder="Ex: PC-001"
                            value="<?php echo htmlspecialchars($_POST['ref'] ?? ''); ?>">
                    </div>
                    <div class="field-row">
                        <label class="field-label" for="nom">Nom</label>
                        <input type="text" name="nom" id="nom" placeholder="Nom du produit"
                            value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>">
                    </div>
                    <div class="field-row">
                        <label class="field-label" for="marque">Marque</label>
                        <input type="text" name="marque" id="marque" placeholder="Marque du produit"
                            value="<?php echo htmlspecialchars($_POST['marque'] ?? ''); ?>">
                    </div>
                    <div class="field-row">
                        <label class="field-label" for="prix">Prix (DH)</label>
                        <input type="number" name="prix" id="prix" placeholder="0.00" min="0.01" step="0.01"
                            value="<?php echo htmlspecialchars($_POST['prix'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="ajouter" id="btn-ajouter"><?php echo icon_svg('add'); ?> Ajouter le produit</button>
                    <button type="reset">Effacer</button>
                </div>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>

</html>