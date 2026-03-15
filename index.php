<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Manager — Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    // Démarrage session propre
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // Si déjà connecté → rediriger directement
    if (!empty($_SESSION['admin_logged_in'])) {
        header('Location: lister.php');
        exit;
    }
    require 'icons.php';
    ?>

    <div class="login-container">
        <div class="login-box">
            <div class="login-logo"><?php echo icon_svg('logo'); ?> Gestion Inventaire</div>
            <p class="login-subtitle">BACK OFFICE — ACCÈS SÉCURISÉ</p>

            <?php if (!empty($_SESSION['login_error'])): ?>
                <div class="alert alert-error" style="margin-bottom:1.2rem;">
                    <?php echo icon_svg('x'); ?> <?php echo htmlspecialchars($_SESSION['login_error']); ?>
                </div>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>

            <form action="gestionProduits.php" method="POST">
                <div class="form-group">
                    <label class="form-label" for="login">Login</label>
                    <input type="text" name="login" id="login" placeholder="Entrer votre login" autocomplete="username">
                </div>
                <div class="form-group">
                    <label class="form-label" for="pass">Mot de passe</label>
                    <!-- CORRECTION : wrapper relatif avec padding-right explicite pour l'icône -->
                    <div class="password-wrapper">
                        <input type="password" name="password" id="pass" placeholder="••••••••" autocomplete="current-password">
                        <button type="button" id="toggle-pass" onclick="togglePassword()" aria-label="Afficher/masquer le mot de passe">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>
                <div style="margin-top:1.5rem">
                    <button type="submit" id="btn-login" style="width:100%;justify-content:center;">
                        Connexion <?php echo icon_svg('arrow-right'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        function togglePassword() {
            const input = document.getElementById('pass');
            const btn = document.getElementById('toggle-pass');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.classList.toggle('active', isHidden);
            btn.innerHTML = isHidden ?
                `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>` :
                `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                    </svg>`;
        }

        // Loader sur le bouton de connexion
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.getElementById('btn-login');
            btn.textContent = 'Chargement...';
            btn.disabled = false; // Ne jamais désactiver définitivement
        });
    </script>
</body>

</html>