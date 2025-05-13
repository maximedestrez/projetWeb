<?php
session_start();
include("../includes/connex.inc.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['transaction_id'])) {
    $idcom = connex("projetweb", "myparam");
    $transaction_id = mysqli_real_escape_string($idcom, $_POST['transaction_id']);
    $acheteur_id = $_SESSION['id_user'];

    try {
        // 1. Vérifier que la transaction appartient à l'acheteur
        $check_query = "SELECT vendeur_id, statut 
                       FROM transactions 
                       WHERE id = ? 
                       AND acheteur_id = ?";
        $stmt = mysqli_prepare($idcom, $check_query);
        mysqli_stmt_bind_param($stmt, "ii", $transaction_id, $acheteur_id);
        mysqli_stmt_execute($stmt);
        $transaction = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$transaction) {
            throw new Exception("Transaction introuvable ou non autorisée");
        }

        // 2. Vérifier que le statut est "livré"
        if ($transaction['statut'] !== 'livré') {
            throw new Exception("Vous ne pouvez confirmer que les commandes livrées");
        }

        // 3. Mettre à jour le statut
        $update_query = "UPDATE transactions 
                        SET statut = 'confirmé' 
                        WHERE id = ?";
        $stmt = mysqli_prepare($idcom, $update_query);
        mysqli_stmt_bind_param($stmt, "i", $transaction_id);
        mysqli_stmt_execute($stmt);

        // 4. Notifier le vendeur
        $message = $_SESSION['prenom'] . " a confirmé la réception de la commande #" . $transaction_id;
        $insert_notif = "INSERT INTO notifications 
                        (user_id, message) 
                        VALUES (?, ?)";
        $stmt = mysqli_prepare($idcom, $insert_notif);
        mysqli_stmt_bind_param($stmt, "is", $transaction['vendeur_id'], $message);
        mysqli_stmt_execute($stmt);

        $_SESSION['confirmation_success'] = "Réception confirmée avec succès";

    } catch (Exception $e) {
        $_SESSION['confirmation_error'] = "Erreur : " . $e->getMessage();
    } finally {
        mysqli_close($idcom);
    }
}

header("Location: mes_commandes.php");
exit;
?>