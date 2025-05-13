<?php
session_start();
include("includes/connex.inc.php");
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

$requete = "SELECT * FROM articles WHERE vendu = 0";
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

<?php
include("includes/header.php");
?>
<link rel="stylesheet" href="assets/css/index.css">
<main>
    <h2>Articles disponibles</h2>
    <div class="articles">
        <?php if ($articles): ?>
            <?php foreach ($articles as $article): ?>
                <a href="pages/article.php?id=<?= htmlspecialchars($article['id']) ?>" class="article-card">
                    <h3><?= htmlspecialchars($article['nom']) ?></h3>
                    <p><?= htmlspecialchars($article['description']) ?></p>
                    <p><strong>Prix :</strong> <?= htmlspecialchars($article['prix']) ?> €</p>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun article disponible pour le moment.</p>
        <?php endif; ?>
    </div>
</main>

<?php
include("includes/footer.php");
?>
