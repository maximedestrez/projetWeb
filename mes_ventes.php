<?php
session_start();
include("connex.inc.php");

// Vérifier si l'utilisateur est connecté ET vendeur
if (!isset($_SESSION['id_user']) || $_SESSION['est_vendeur'] != 1) {
    header("Location: login.php");
    exit;
}

$idcom = connex("projetweb", "myparam");
$vendeur_id = $_SESSION['id_user'];

try {
    // Récupérer les ventes avec détails des acheteurs
    $query = "SELECT 
                t.id AS transaction_id,
                a.nom AS article_nom,
                u.prenom AS acheteur_prenom,
                u.email AS acheteur_email,
                t.montant,
                t.statut,
                t.date_transaction,
                a.photos
              FROM transactions t
              INNER JOIN articles a ON t.article_id = a.id
              INNER JOIN utilisateur u ON t.acheteur_id = u.id
              WHERE t.vendeur_id = ?
              ORDER BY t.date_transaction DESC";

    $stmt = mysqli_prepare($idcom, $query);
    mysqli_stmt_bind_param($stmt, "i", $vendeur_id);
    mysqli_stmt_execute($stmt);
    $ventes = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

} catch (mysqli_sql_exception $e) {
    $error = "Erreur lors du chargement des ventes";
} finally {
    mysqli_close($idcom);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mes ventes</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <style>
        .ventes-container { max-width: 1200px; margin: 20px auto; }
        .vente-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .statut-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
            display: inline-block;
        }
        .statut-payé { background: #d4edda; color: #155724; }
        .statut-livré { background: #fff3cd; color: #856404; }
        .statut-confirmé { background: #d1ecf1; color: #0c5460; }
        .article-image {
            max-width: 150px;
            border-radius: 4px;
            margin-right: 20px;
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>

    <div class="ventes-container">
        <h1>Mes ventes</h1>
        
        <?php if(isset($error)): ?>
            <div class="error-message"><?= $error ?></div>
        
        <?php elseif(empty($ventes)): ?>
            <div class="empty-state">
                <p>Vous n'avez aucune vente pour le moment</p>
                <a href="add_article.php" class="btn-primary">Ajouter un article</a>
            </div>
        
        <?php else: ?>
            <div class="ventes-list">
                <?php foreach($ventes as $vente): ?>
                    <div class="vente-card">
                        <div class="vente-header">
                            <?php if(!empty($vente['photos'])): ?>
                                <img src="uploads/<?= htmlspecialchars($vente['photos']) ?>" 
                                     class="article-image" 
                                     alt="<?= htmlspecialchars($vente['article_nom']) ?>">
                            <?php endif; ?>
                            
                            <div>
                                <h3><?= htmlspecialchars($vente['article_nom']) ?></h3>
                                <div class="vente-meta">
                                    <span class="statut-badge statut-<?= $vente['statut'] ?>">
                                        <?= ucfirst($vente['statut']) ?>
                                    </span>
                                    <span class="vente-date">
                                        <?= date('d/m/Y H:i', strtotime($vente['date_transaction'])) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="vente-details">
                            <p><strong>Acheteur :</strong> 
                                <?= htmlspecialchars($vente['acheteur_prenom']) ?> 
                                (<?= htmlspecialchars($vente['acheteur_email']) ?>)
                            </p>
                            <p><strong>Montant :</strong> 
                                <?= number_format($vente['montant'], 2) ?> €
                            </p>
                        </div>

                        <?php if($vente['statut'] === 'payé'): ?>
                            <form action="update_vente_status.php" method="post">
    <input type="hidden" name="transaction_id" value="<?= $vente['transaction_id'] ?>">
    
    <select name="statut" class="status-select">
        <option value="payé" <?= $vente['statut'] === 'payé' ? 'selected' : '' ?>>Payé</option>
        <option value="livré" <?= $vente['statut'] === 'livré' ? 'selected' : '' ?>>Livré</option>
        <option value="confirmé" <?= $vente['statut'] === 'confirmé' ? 'selected' : '' ?>>Confirmé</option>
    </select>
    
    <button type="submit" class="btn-update">Mettre à jour</button>
</form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include('footer.php'); ?>
</body>
</html>