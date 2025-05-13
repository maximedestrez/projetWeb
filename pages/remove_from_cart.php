<?php
session_start();
include("../includes/connex.inc.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

// Vérifier si l'ID de l'article à supprimer est présent
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['cart_error'] = "Requête invalide";
    header("Location: cart.php");
    exit;
}

$idcom = connex("projetweb", "myparam");
$panier_id = mysqli_real_escape_string($idcom, $_GET['id']);
$user_id = $_SESSION['id_user'];

try {
    // Vérifier que l'article appartient bien à l'utilisateur
    $check_query = "SELECT id FROM panier WHERE id = ? AND acheteur_id = ?";
    $stmt = mysqli_prepare($idcom, $check_query);
    mysqli_stmt_bind_param($stmt, "ii", $panier_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 0) {
        $_SESSION['cart_error'] = "Article introuvable dans votre panier";
        header("Location: cart.php");
        exit;
    }

    // Suppression de l'article
    $delete_query = "DELETE FROM panier WHERE id = ?";
    $stmt = mysqli_prepare($idcom, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $panier_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['cart_success'] = "Article retiré du panier";
    } else {
        throw new Exception("Erreur de suppression");
    }

} catch (Exception $e) {
    $_SESSION['cart_error'] = "Erreur : " . $e->getMessage();
} finally {
    mysqli_close($idcom);
    header("Location: cart.php");
    exit;
}
?>