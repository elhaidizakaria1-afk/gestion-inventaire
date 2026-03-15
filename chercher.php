<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chercher un produit</title>
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
        <h2 class="page-title">Rechercher un produit</h2>

        <?php
        if (isset($_GET['deleted']) && $_GET['deleted'] === '1') {
            echo '<script>window.addEventListener("DOMContentLoaded",()=>showToast("Produit supprimé avec succès !","success"));</script>';
        }
        if (isset($_GET['error'])) {
            $errMsg = htmlspecialchars(urldecode($_GET['error']), ENT_QUOTES);
            echo "<script>window.addEventListener('DOMContentLoaded',()=>showToast('$errMsg','error'));</script>";
        }
        ?>

        <div class="card">
            <form action="" method="GET">
                <div class="search-wrapper">
                    <span class="search-icon"><?php echo icon_svg('search'); ?></span>
                    <input type="text" name="Ref" placeholder="Référence, nom ou catégorie…"
                        value="<?php echo isset($_GET['Ref']) ? htmlspecialchars($_GET['Ref']) : ''; ?>">
                </div>
                <div class="form-actions">
                    <button type="submit" name="chercher"><?php echo icon_svg('search'); ?> Rechercher</button>
                    <button type="button" onclick="window.location.href='chercher.php'">Annuler</button>
                </div>
            </form>
        </div>

        <?php
        include 'connexion.php';
        if (isset($_GET['chercher'])) {
            $mc  = trim($_GET['Ref'] ?? '');
            $req = $bdd->prepare(
                "SELECT * FROM Produits WHERE nom LIKE ? OR cat LIKE ? OR ref LIKE ?"
            );
            $req->execute(["%$mc%", "%$mc%", "%$mc%"]);
            $results = $req->fetchAll();
            $results = array_map(fn($row) => array_change_key_case($row, CASE_LOWER), $results);
        ?>

            <?php if (count($results) === 0): ?>
                <div class="empty-state">
                    <div class="icon"><?php echo icon_svg('search'); ?></div>
                    <p>Aucun produit trouvé pour « <?php echo htmlspecialchars($mc); ?> ».</p>
                </div>
            <?php else: ?>
                <div class="table-scroll"><table>
                    <thead>
                        <tr>
                            <th>Réf.</th>
                            <th>Catégorie</th>
                            <th>Nom</th>
                            <th>Prix</th>
                            <th>Marque</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $data): ?>
                            <tr>
                                <td><code style="color:#6b728f;font-size:.8rem;"><?php echo htmlspecialchars($data['ref']); ?></code></td>
                                <td><?php echo htmlspecialchars($data['cat']); ?></td>
                                <td><?php echo htmlspecialchars($data['nom']); ?></td>
                                <td><strong style="color:#00b37a;"><?php echo htmlspecialchars($data['prix']); ?> DH</strong></td>
                                <td><?php echo htmlspecialchars($data['marque']); ?></td>
                                <td class="actions-cell">
                                    <a href="modifier.php?ref=<?php echo urlencode($data['ref']); ?>"><?php echo icon_svg('edit'); ?> Modifier</a>
                                    <a href="supprimer.php?ref=<?php echo urlencode($data['ref']); ?>"><?php echo icon_svg('trash'); ?> Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table></div>
            <?php endif; ?>
        <?php } ?>
    </div>

    <script src="script.js"></script>
</body>

</html>