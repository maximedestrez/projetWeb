<?php
session_start();
include("connex.inc.php");

// Vérifier la connexion
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$idcom = connex("projetweb", "myparam");
$user_id = $_SESSION['id_user'];

try {
    // Récupérer l'historique des commandes avec détails
    $query = "SELECT 
                t.id AS transaction_id,
                a.nom AS article_nom,
                u.prenom AS vendeur_prenom,
                t.montant,
                t.statut,
                t.date_transaction
              FROM transactions t
              JOIN articles a ON t.article_id = a.id
              JOIN utilisateur u ON t.vendeur_id = u.id
              WHERE t.acheteur_id = ?
              ORDER BY t.date_transaction DESC";
              
    $stmt = mysqli_prepare($idcom, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $commandes = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

} catch (mysqli_sql_exception $e) {
    $error = "Erreur lors de la récupération des commandes";
} finally {
    mysqli_close($idcom);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mes Commandes</title>
    <link rel="stylesheet" href="assets/css/stylelogin.css">
    <style>
        .commandes-container { max-width: 1000px; margin: 20px auto; }
        .commande-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            background: #fff;
        }
        .statut {
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
        }
        .statut.payé { background: #d4edda; color: #155724; }
        .statut.livré { background: #fff3cd; color: #856404; }
        .statut.confirmé { background: #cce5ff; color: #004085; }
    </style>
</head>
<body>
    <?php include('header.php'); ?>

    <div class="commandes-container">
        <h1>Historique de mes commandes</h1>
        
        <?php if(isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php elseif(empty($commandes)): ?>
            <p>Vous n'avez aucune commande pour le moment.</p>
        <?php else: ?>
            <?php foreach($commandes as $commande): ?>
                <div class="commande-card">
                    <div class="commande-header">
                        <h3><?= htmlspecialchars($commande['article_nom']) ?></h3>
                        <span class="statut <?= $commande['statut'] ?>">
                            <?= ucfirst($commande['statut']) ?>
                        </span>
                    </div>
                    
                    <div class="commande-details">
                        <p>Vendeur : <?= htmlspecialchars($commande['vendeur_prenom']) ?></p>
                        <p>Montant : <?= number_format($commande['montant'], 2) ?> €</p>
                        <p>Date : <?= date('d/m/Y H:i', strtotime($commande['date_transaction'])) ?></p>
                    </div>

                    <?php if($commande['statut'] === 'livré'): ?>
                        <form action="confirm_receipt.php" method="post">
                            <input type="hidden" name="transaction_id" 
                                   value="<?= $commande['transaction_id'] ?>">
                            <button type="submit" class="confirm-button">
                                Confirmer la réception
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php include('footer.php'); ?>
</body>
</html>