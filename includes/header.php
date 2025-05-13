<?php 
if (basename(path: $_SERVER['PHP_SELF']) === 'index.php') {
    $base_url = '';
    $base_url_pages = 'pages/';
} else {
    $base_url = '../';
    $base_url_pages = '';
}
?>

<header>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/header.css"/>
    <div class="header-top">
        <div class="site-name">
            <a href="<?= $base_url ?>index.php" class="site-name-link">Site de E-commerce</a>
        </div>
        <form method="GET" action="<?= $base_url ?>index.php" class="search-bar">
            <input type="text" name="search" placeholder="Rechercher un article" value="<?= htmlspecialchars($search ?? '') ?>">
            <button type="submit">Rechercher</button>
        </form>
        <div class="account-options">
            <?php if (isset($_SESSION['est_vendeur']) && $_SESSION['est_vendeur'] == 1): ?>
            <a href="<?= $base_url_pages ?>add_article.php" class="btn btn-secondary " type="button">Vendre un article</a>
            <a href="<?= $base_url_pages ?>cart.php" class="btn btn-secondary " type="button">Panier</a>
            <?php endif; ?>
            <?php if (!isset($_SESSION['id_user'])): ?>
                <a href="<?= $base_url ?>auth/login.php" class="btn btn-secondary " type="button">Connexion</a>
                <a href="<?= $base_url ?>auth/register.php" class="btn btn-secondary " type="button">Inscription</a>
            <?php else: ?>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        Bonjour, <?= htmlspecialchars($_SESSION['prenom']) ?> !
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#">Solde : <?= number_format($_SESSION['solde'], 2) ?> €</a>
                        <a class="dropdown-item" href="<?= $base_url_pages ?>credit_account.php">Créditer mon compte</a>
                        <a class="dropdown-item" href="<?= $base_url_pages ?>mes_commandes.php">Mes commandes</a>
                        <?php if ($_SESSION['est_vendeur'] == 1): ?>
                            <a class="dropdown-item" href="<?= $base_url_pages ?>mes_ventes.php">Mes ventes</a>
                        <?php endif; ?>
                        <a class="dropdown-item" href="<?= $base_url ?>auth/logout.php">Déconnexion</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="categories">
        <a href="<?= $base_url ?>index.php?category=voiture">Voiture</a>
        <a href="<?= $base_url ?>index.php?category=carte">Carte de collection</a>
        <a href="<?= $base_url ?>index.php?category=vetement">Vêtement</a>
        <a href="<?= $base_url ?>index.php?category=livre">Livre</a>
    </div>
</header>