<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des produits</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    require 'auth.php';   // 🔒 Guard — redirige si non connecté
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
        <h2 class="page-title">Liste des produits</h2>

        <?php
        require 'connexion.php';

        $colonnesAutorisees = ['cat', 'nom', 'marque', 'prix'];
        $triChoisi = '';
        $sql = "SELECT * FROM Produits";

        if (isset($_POST['trier']) && isset($_POST['tri'])) {
            $triChoisi = strtolower(trim($_POST['tri']));
            if (in_array($triChoisi, $colonnesAutorisees)) {
                $sql .= " ORDER BY " . $triChoisi;
            }
        }

        try {
            $req      = $bdd->query($sql);
            $produits = $req->fetchAll();
            $produits = array_map(fn($row) => array_change_key_case($row, CASE_LOWER), $produits);
        } catch (PDOException $e) {
            $produits = [];
            echo '<script>window.addEventListener("DOMContentLoaded",()=>showToast("Erreur SQL : ' . addslashes(htmlspecialchars($e->getMessage())) . '","error"));</script>';
        }
        ?>

        <form method="POST" class="sort-bar">
            Trier par :
            <label><input type="radio" name="tri" value="cat" <?php echo $triChoisi === 'cat'    ? 'checked' : ''; ?>> Catégorie</label>
            <label><input type="radio" name="tri" value="prix" <?php echo $triChoisi === 'prix'   ? 'checked' : ''; ?>> Prix</label>
            <label><input type="radio" name="tri" value="nom" <?php echo $triChoisi === 'nom'    ? 'checked' : ''; ?>> Nom</label>
            <label><input type="radio" name="tri" value="marque" <?php echo $triChoisi === 'marque' ? 'checked' : ''; ?>> Marque</label>
            <button type="submit" name="trier" style="padding:.45rem 1rem;font-size:.8rem;">Trier ↑</button>
        </form>

        <?php if (count($produits) === 0): ?>
            <div class="empty-state">
                <div class="icon"><?php echo icon_svg('box'); ?></div>
                <p>Aucun produit dans la base de données.</p>
            </div>
        <?php else: ?>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Réf.</th>
                            <th>Catégorie</th>
                            <th>Nom</th>
                            <th>Marque</th>
                            <th>Prix</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produits as $res): ?>
                            <tr>
                                <td><code style="color:#6b728f;font-size:.8rem;"><?php echo htmlspecialchars($res['ref']); ?></code></td>
                                <td><?php echo htmlspecialchars($res['cat']); ?></td>
                                <td><?php echo htmlspecialchars($res['nom']); ?></td>
                                <td><?php echo htmlspecialchars($res['marque']); ?></td>
                                <td><strong style="color:#00b37a;"><?php echo htmlspecialchars($res['prix']); ?> DH</strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>

    <script src="script.js"></script>
</body>

</html>