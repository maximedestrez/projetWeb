<?php
session_start();
include("connex.inc.php");
$idcom = connex("projetweb", "myparam");

// Vérifier connexion
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// Récupérer le panier
$query = "SELECT a.*, p.quantite 
         FROM panier p
         JOIN articles a ON p.article_id = a.id
         WHERE p.acheteur_id = ?";
$stmt = mysqli_prepare($idcom, $query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['id_user']);
mysqli_stmt_execute($stmt);
$articles = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

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
    <title>Confirmation de paiement</title>
    <link rel="stylesheet" href="assets/css/stylelogin.css">
    <style>
        .checkout-info { 
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>

    <div class="checkout-container">
        <h1>Confirmer le paiement</h1>
        
        <?php if(isset($_SESSION['checkout_error'])): ?>
            <div class="error"><?= $_SESSION['checkout_error'] ?></div>
            <?php unset($_SESSION['checkout_error']); ?>
        <?php endif; ?>

        <div class="checkout-info">
            <p>Total à débiter : <strong><?= number_format($total, 2) ?> €</strong></p>
            <p>Solde disponible : <strong><?= number_format($_SESSION['solde'], 2) ?> €</strong></p>
        </div>

        <form action="checkout_process.php" method="post">
            <button type="submit" class="confirm-button">
                Confirmer le paiement avec mon solde
            </button>
            <a href="cart.php" class="cancel-button">Annuler</a>
        </form>
    </div>

    <?php include('footer.php'); ?>
</body>
</html>