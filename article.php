<?php
session_start();
include("connex.inc.php");
$idcom = connex("projetweb", "myparam");

// Vérification de l'ID de l'article
if (!isset($_GET['id']) || empty(trim($_GET['id']))) {
    echo "<p>Article non spécifié.</p>";
    exit;
}

$id_article = mysqli_real_escape_string($idcom, trim($_GET['id']));

// Récupération des informations de l'article
$requete = "SELECT * FROM articles WHERE id = '$id_article'";
$resultat = mysqli_query($idcom, $requete);
$article = mysqli_fetch_assoc($resultat);
mysqli_free_result($resultat);
mysqli_close($idcom);

if (!$article) {
    echo "<p>Article introuvable.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['nom']) ?> - Détails</title>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        main {
            padding: 20px 15%;
        }
        .article-details {
            border: 1px solid #ccc;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .article-details ul {
            padding-left: 20px;
            list-style: square;
        }
    </style>
</head>
<body>
<?php
include("header.php");
?>
<link rel="stylesheet" href="assets/css/index.css">

<main>
    <div class="article-details">
        <h2><?= htmlspecialchars($article['nom']) ?></h2>
        <p><strong>Description :</strong> <?= htmlspecialchars($article['description']) ?></p>
        <p><strong>Prix :</strong> <?= htmlspecialchars($article['prix']) ?> €</p>
        <?php if (!empty($article['categorie'])): ?>
            <p><strong>Catégorie :</strong> <?= htmlspecialchars($article['categorie']) ?></p>
        <?php endif; ?>

        <h3>Caractéristiques :</h3>
        <ul>
            <?php switch ($article['categorie']) {
                case 'voiture':
                    if (!empty($article['kilometrage'])) {
                        echo '<li><strong>Kilométrage :</strong> ' . htmlspecialchars($article['kilometrage']) . '</li>';
                    }
                    if (!empty($article['photos'])) {
                        echo '<li><strong>Photo :</strong> <img src="' . htmlspecialchars($article['photos']) . '" alt="Photo de la voiture" style="max-width: 100%;"></li>';
                    }
                    break;
                case 'vetement':
                    if (!empty($article['taille'])) {
                        echo '<li><strong>Taille :</strong> ' . htmlspecialchars($article['taille']) . '</li>';
                    }
                    if (!empty($article['photos'])) {
                        echo '<li><strong>Photo :</strong> <img src="' . htmlspecialchars($article['photos']) . '" alt="Photo du vêtement" style="max-width: 100%;"></li>';
                    }
                    break;
                case 'carte':
                    if (!empty($article['etat'])) {
                        echo '<li><strong>État :</strong> ' . htmlspecialchars($article['etat']) . '</li>';
                    }
                    if (!empty($article['photos'])) {
                        echo '<li><strong>Photo :</strong> <img src="' . htmlspecialchars($article['photos']) . '" alt="Photo de la carte" style="max-width: 100%;"></li>';
                    }
                    break;
                case 'livre':
                    if (!empty($article['etat'])) {
                        echo '<li><strong>État :</strong> ' . htmlspecialchars($article['etat']) . '</li>';
                    }
                    if (!empty($article['auteur'])) {
                        echo '<li><strong>Auteur :</strong> ' . htmlspecialchars($article['auteur']) . '</li>';
                    }
                    break;
                default:
                    echo '<li>Aucune caractéristique spécifique disponible pour cette catégorie.</li>';
                    break;
            } ?>
        </ul>
        <form method="post" action="cart.php">
    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
    <button type="submit" name="ajouter_panier">Ajouter au panier</button>
</form>
    </div>
</main>

<?php
include("footer.php");
?>
</body>
</html>