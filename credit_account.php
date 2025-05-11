<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}
?>
<?php
include("header.php");
?>
<link rel="stylesheet" href="assets/css/index.css">
<!DOCTYPE html>
<html>
<head>
    <title>Créditer mon compte</title>
    <style>
        .error { color: red; }
        .success { color: green; }
        .submit-b { margin: 5px 0; width: 100%; padding: 8px; max-width: 150px; }
        .principal {
            margin: 0 auto;
            text-align: center;
        }
    </style>
</head>
<main>
    <div class="principal">
        <h1>Créditer mon compte</h1>
        <?php
        if (isset($_SESSION['credit_error'])) {
            echo '<p class="error">' . htmlspecialchars($_SESSION['credit_error']) . '</p>';
            unset($_SESSION['credit_error']);
        }
        if (isset($_SESSION['credit_success'])) {
            echo '<p class="success">' . htmlspecialchars($_SESSION['credit_success']) . '</p>';
            unset($_SESSION['credit_success']);
        }
        ?>
        <form method="post" action="credit_process.php">
            <label>Montant (€) :</label>
            <input type="number" name="amount" step="0.01" min="0.01" required>
            <button class="submit-b" type="submit">Créditer</button>
        </form>
    </div>
</main>
<?php include("footer.php"); ?>
</html>