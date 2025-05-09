<?php
session_start();
include("connex.inc.php");
$idcom = connex("projetweb", "myparam");

// Traitement recherche et tri par catégorie
$search = '';
$category = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = mysqli_real_escape_string($idcom, trim($_GET['search']));
}
if (isset($_GET['category']) && !empty(trim($_GET['category']))) {
    $category = mysqli_real_escape_string($idcom, trim($_GET['category']));
}

$requete = "SELECT * FROM articles WHERE 1";
if ($search) {
    $requete .= " AND (nom LIKE '%$search%' OR description LIKE '%$search%')";
}
if ($category) {
    $requete .= " AND categorie = '$category'";
}
$requete .= " ORDER BY date_ajout DESC";

// Exécution requête
$resultat = mysqli_query($idcom, $requete);
$articles = mysqli_fetch_all($resultat, MYSQLI_ASSOC);
mysqli_free_result($resultat);
mysqli_close($idcom);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/stylelogin.css">
    <title>Accueil - E-commerce</title>
</head>
<body>
    <header>
        <h1>Bienvenue sur notre site de e-commerce</h1>
        <nav>
            <?php if (!isset($_SESSION['id_user'])): ?>
                <a href="login.php">Connexion</a>
                <a href="register.php">Inscription</a>
            <?php else: ?>
                <a href="logout.php">Déconnexion</a>
                <p>Bonjour, <?= htmlspecialchars($_SESSION['prenom']) ?> !</p>
            <?php endif; ?>
            <?php if (isset($_SESSION['est_vendeur']) && $_SESSION['est_vendeur'] == 1): ?>
                <a href="add_article.php">Vendre un article</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <h2>Articles disponibles</h2>
        <form method="GET" action="index.php">
            <input type="text" name="search" placeholder="Rechercher un article" value="<?= htmlspecialchars($search) ?>">
            <select name="category">
                <option value="">Toutes les catégories</option>
                <option value="voiture" <?= $category == 'voiture' ? 'selected' : '' ?>>Voiture</option>
                <option value="carte" <?= $category == 'carte' ? 'selected' : '' ?>>Carte de collection</option>
                <option value="vetement" <?= $category == 'vetement' ? 'selected' : '' ?>>Vêtement</option>
                <option value="livre" <?= $category == 'livre' ? 'selected' : '' ?>>Livre</option>
            </select>
            <button type="submit">Rechercher</button>
        </form>

        <div class="articles">
            <?php if ($articles): ?>
                <?php foreach ($articles as $article): ?>
                    <div class="article-card">
                        <h3><?= htmlspecialchars($article['nom']) ?></h3>
                        <p><?= htmlspecialchars($article['description']) ?></p>
                        <p><strong>Prix :</strong> <?= htmlspecialchars($article['prix']) ?> €</p>
                        <?php if (!empty($article['categorie'])): ?>
                            <p><strong>Catégorie :</strong> <?= htmlspecialchars($article['categorie']) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($article['caracteristiques'])):
                            $caracs = json_decode($article['caracteristiques'], true);
                            if (is_array($caracs)): ?>
                                <ul>
                                    <?php foreach ($caracs as $cle => $val): ?>
                                        <li><strong><?= htmlspecialchars($cle) ?>:</strong> <?= htmlspecialchars($val) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif;
                        endif; ?>

                        <a href="article.php?id=<?= htmlspecialchars($article['id']) ?>">Voir l'article</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun article disponible pour le moment.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 - Projet E-commerce</p>
    </footer>

    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
        }
        nav a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
        }
        nav a:hover {
            text-decoration: underline;
        }
        main {
            padding: 20px;
        }
        .articles {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .article-card {
            border: 1px solid #ccc;
            padding: 10px;
            width: 250px;
            background-color: #f9f9f9;
        }
        .article-card ul {
            padding-left: 20px;
            list-style: square;
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
            margin-top: 50px;
        }
    </style>
</body>
</html>
