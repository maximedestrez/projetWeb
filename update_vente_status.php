<?php
session_start();
include("connex.inc.php");

// Vérifier si l'utilisateur est un vendeur connecté
if (!isset($_SESSION['id_user']) || $_SESSION['est_vendeur'] != 1) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idcom = connex("projetweb", "myparam");
    $vendeur_id = $_SESSION['id_user'];
    $transaction_id = intval($_POST['transaction_id']);
    $nouveau_statut = $_POST['statut'];

    try {
        mysqli_begin_transaction($idcom);

        // 1. Vérifier que la transaction appartient au vendeur
        $check_query = "SELECT id, acheteur_id, statut 
                       FROM transactions 
                       WHERE id = ? 
                       AND vendeur_id = ?";
        $stmt = mysqli_prepare($idcom, $check_query);
        mysqli_stmt_bind_param($stmt, "ii", $transaction_id, $vendeur_id);
        mysqli_stmt_execute($stmt);
        $transaction = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$transaction) {
            throw new Exception("Transaction introuvable ou non autorisée");
        }

        // 2. Mettre à jour le statut
        $update_query = "UPDATE transactions 
                        SET statut = ? 
                        WHERE id = ?";
        $stmt = mysqli_prepare($idcom, $update_query);
        mysqli_stmt_bind_param($stmt, "si", $nouveau_statut, $transaction_id);
        mysqli_stmt_execute($stmt);

        // 3. Envoyer une notification à l'acheteur
        $messages = [
            'livré' => "Votre commande #$transaction_id a été expédiée !",
            'confirmé' => "Le vendeur a confirmé la réception de votre paiement."
        ];

        if (isset($messages[$nouveau_statut])) {
            $insert_notif = "INSERT INTO notifications 
                            (user_id, message) 
                            VALUES (?, ?)";
            $stmt = mysqli_prepare($idcom, $insert_notif);
            mysqli_stmt_bind_param($stmt, "is", 
                $transaction['acheteur_id'], 
                $messages[$nouveau_statut]
            );
            mysqli_stmt_execute($stmt);
        }

        mysqli_commit($idcom);
        $_SESSION['vente_success'] = "Statut mis à jour avec succès";

    } catch (Exception $e) {
        mysqli_rollback($idcom);
        $_SESSION['vente_error'] = "Erreur : " . $e->getMessage();
    } finally {
        mysqli_close($idcom);
    }
}

header("Location: mes_ventes.php");
exit;
?>