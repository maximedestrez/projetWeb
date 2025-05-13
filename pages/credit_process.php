<?php
session_start();
include("../includes/connex.inc.php");
$idcom = connex("projetweb", "myparam");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['id_user'])) {
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    
    if ($amount === false || $amount <= 0) {
        $_SESSION['credit_error'] = "Montant invalide.";
    } else {
        $user_id = $_SESSION['id_user'];
        $query = "UPDATE utilisateur SET solde = solde + ? WHERE id = ?";
        $stmt = mysqli_prepare($idcom, $query);
        mysqli_stmt_bind_param($stmt, "di", $amount, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['solde'] += $amount;
            $_SESSION['credit_success'] = "Votre compte a été crédité de " . number_format($amount, 2) . " €.";
        } else {
            $_SESSION['credit_error'] = "Erreur lors du crédit.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($idcom);
}

header("Location: credit_account.php");
exit;
?>