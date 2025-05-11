<?php
session_start();
include("connex.inc.php");
$idcom = connex("projetweb", "myparam");

// Vérification connexion utilisateur
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// Traitement ajout au panier
if (isset($_POST['ajouter_panier'])) {
    if (!isset($_POST['article_id']) || empty($_POST['article_id'])) {
        $_SESSION['panier_error'] = "Article non spécifié";
        header("Location: index.php");
        exit;
    }

    $article_id = mysqli_real_escape_string($idcom, $_POST['article_id']);

    // Vérification existence article
    $check_article = "SELECT id, prix, vendeur_id FROM articles WHERE id = ?";
    $stmt = mysqli_prepare($idcom, $check_article);
    mysqli_stmt_bind_param($stmt, "i", $article_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        $_SESSION['panier_error'] = "Article introuvable";
        header("Location: index.php");
        exit;
    }
    
    $article = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    // Insertion dans le panier
    $insert = "INSERT INTO panier (acheteur_id, article_id, prix_unitaire, vendeur_id) 
              VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($idcom, $insert);
    mysqli_stmt_bind_param($stmt, "iidi", 
        $_SESSION['id_user'],
        $article_id,
        $article['prix'],
        $article['vendeur_id']
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        $_SESSION['panier_error'] = "Erreur d'ajout au panier : " . mysqli_error($idcom);
    }
    mysqli_stmt_close($stmt);
}

// Récupération du panier
$query = "SELECT p.id AS panier_id, a.*, p.quantite 
         FROM panier p
         JOIN articles a ON p.article_id = a.id
         WHERE p.acheteur_id = ?";
$stmt = mysqli_prepare($idcom, $query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['id_user']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$articles = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Calcul du total
$total = 0;
foreach ($articles as $article) {
    $total += $article['prix'] * $article['quantite'];
}

mysqli_close($idcom);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mon Panier</title>
    <link rel="stylesheet" href="assets/css/stylelogin.css">
    <style>
        .panier-container { max-width: 800px; margin: 20px auto; }
        .article-panier { 
            border: 1px solid #ddd; 
            padding: 15px; 
            margin-bottom: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .total { 
            font-size: 1.2em; 
            font-weight: bold; 
            text-align: right;
            margin-top: 20px;
        }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <?php include('header.php'); ?>

    <div class="panier-container">
        <h1>Votre Panier</h1>
        
        <?php if(isset($_SESSION['panier_error'])): ?>
            <div class="error"><?= $_SESSION['panier_error'] ?></div>
            <?php unset($_SESSION['panier_error']); ?>
        <?php endif; ?>

        <?php if(empty($articles)): ?>
            <p>Votre panier est vide</p>
        <?php else: ?>
            <?php foreach($articles as $article): ?>
                <div class="article-panier">
                    <div>
                        <h3><?= htmlspecialchars($article['nom']) ?></h3>
                        <p>Prix unitaire : <?= number_format($article['prix'], 2) ?> €</p>
                        <p>Quantité : <?= $article['quantite'] ?></p>
                    </div>
                    <div>
                        <form action="update_quantity.php" method="post">
                            <input type="hidden" name="panier_id" value="<?= $article['panier_id'] ?>">
                            <input type="number" name="quantite" value="<?= $article['quantite'] ?>" min="1" style="width: 60px;">
                            <button type="submit">Modifier</button>
                        </form>
                        <a href="remove_from_cart.php?id=<?= $article['panier_id'] ?>" 
                           style="color: red; margin-left: 10px;">
                            Supprimer
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="total">
                Total du panier : <?= number_format($total, 2) ?> €
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="checkout.php" 
                   style="padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none;">
                    Passer la commande
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php include('footer.php'); ?>
</body>
</html>