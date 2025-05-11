<header>
    <link rel="stylesheet" href="assets/css/header.css"/>
    <div class="header-top">
        <div class="site-name">Site de e-commerce</div>
        <form method="GET" action="index.php" class="search-bar">
            <input type="text" name="search" placeholder="Rechercher un article" value="<?= htmlspecialchars($search ?? '') ?>">
            <button type="submit">Rechercher</button>
        </form>
        <div class="account-options">
            <?php if (isset($_SESSION['est_vendeur']) && $_SESSION['est_vendeur'] == 1): ?>
            <a href="add_article.php" class="sell-button">Vendre un article</a>
            <a href="credit_account.php">Créditer mon compte</a>
            <a href="cart.php">Panier</a>
            <a href="index.php">Accueil</a>
            <a href="mes_commandes.php">Histo commandes</a>
            <a href="mes_ventes.php">Mes ventes</a>
            <?php endif; ?>
            <?php if (!isset($_SESSION['id_user'])): ?>
                <a href="login.php">Connexion</a>
                <a href="register.php">Inscription</a>
            <?php else: ?>
                <div class="dropdown">
                    <button class="dropbtn">Bonjour, <?= htmlspecialchars($_SESSION['prenom']) ?> !</button>
                    <p>Solde : <?= number_format($_SESSION['solde'], 2) ?> €</p>
                    <div class="dropdown-content">
                        <a href="logout.php">Déconnexion</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="categories">
        <a href="index.php?category=voiture">Voiture</a>
        <a href="index.php?category=carte">Carte de collection</a>
        <a href="index.php?category=vetement">Vêtement</a>
        <a href="index.php?category=livre">Livre</a>
    </div>
</header>