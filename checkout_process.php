<?php
session_start();
include("connex.inc.php");
$idcom = connex("projetweb", "myparam");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

try {
    $user_id = $_SESSION['id_user'];
    
    // 1. Récupérer le panier et calculer le total
    $query = "SELECT a.prix, a.vendeur_id, p.quantite, a.id AS article_id 
             FROM panier p 
             JOIN articles a ON p.article_id = a.id 
             WHERE p.acheteur_id = ?";
    $stmt = mysqli_prepare($idcom, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $articles = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

    if (empty($articles)) {
        throw new Exception("Votre panier est vide");
    }

    // 2. Calculer le total
    $total = 0;
    foreach ($articles as $article) {
        $total += $article['prix'] * $article['quantite'];
    }

    // 3. Vérifier le solde
    if ($_SESSION['solde'] < $total) {
        throw new Exception("Solde insuffisant. Veuillez créditer votre compte.");
    }

    // Début de la transaction SQL
    mysqli_begin_transaction($idcom);

    // 4. Débiter l'acheteur
    $update_acheteur = "UPDATE utilisateur SET solde = solde - ? WHERE id = ?";
    $stmt = mysqli_prepare($idcom, $update_acheteur);
    mysqli_stmt_bind_param($stmt, "di", $total, $user_id);
    mysqli_stmt_execute($stmt);

    // 5. Créditer les vendeurs et enregistrer les transactions
    foreach ($articles as $article) {
        $montant = $article['prix'] * $article['quantite'];
        $vendeur_id = $article['vendeur_id'];
        
       // Créditer le vendeur (déjà correct)
$update_vendeur = "UPDATE utilisateur SET solde = solde + ? WHERE id = ?";
$stmt_vendeur = mysqli_prepare($idcom, $update_vendeur);
mysqli_stmt_bind_param($stmt_vendeur, "di", $montant, $vendeur_id);
mysqli_stmt_execute($stmt_vendeur);

// 1. Enregistrer la transaction
$insert_transaction = "INSERT INTO transactions 
    (acheteur_id, vendeur_id, article_id, montant, statut) 
    VALUES (?, ?, ?, ?, 'payé')";
$stmt_transaction = mysqli_prepare($idcom, $insert_transaction);
mysqli_stmt_bind_param($stmt_transaction, "iiid", 
    $user_id, 
    $vendeur_id,
    $article['article_id'],
    $montant
);
mysqli_stmt_execute($stmt_transaction); // <-- EXÉCUTION ICI

// 2. Notification après enregistrement de la transaction
$message_vendeur = "Votre article " . $article['nom'] . " a été acheté par " . $_SESSION['prenom'];
$insert_notification = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
$stmt_notif = mysqli_prepare($idcom, $insert_notification);
mysqli_stmt_bind_param($stmt_notif, "is", $article['vendeur_id'], $message_vendeur);
mysqli_stmt_execute($stmt_notif);
    }

    // 6. Vider le panier
    $delete_panier = "DELETE FROM panier WHERE acheteur_id = ?";
    $stmt = mysqli_prepare($idcom, $delete_panier);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    // Valider la transaction
    mysqli_commit($idcom);

    // Mettre à jour le solde en session
    $_SESSION['solde'] -= $total;
    $_SESSION['checkout_success'] = "Paiement effectué avec succès !";
    header("Location: mes_commandes.php");

} catch (Exception $e) {
    // Annuler en cas d'erreur
    mysqli_rollback($idcom);
    $_SESSION['checkout_error'] = $e->getMessage();
    header("Location: checkout.php");
} finally {
    mysqli_close($idcom);
    exit;
}