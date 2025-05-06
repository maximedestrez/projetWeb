<?php
// Fonction permettant de vérifier si un utilisateur et un mot de passe correspondent
if (isset($_POST['email']) && isset($_POST['password'])) {
    session_start();
    include("connex.inc.php");
    $idcom = connex("projetweb", "myparam");

    $email = mysqli_real_escape_string($idcom, htmlspecialchars($_POST['email'])); 
    $password_saisi = mysqli_real_escape_string($idcom, htmlspecialchars($_POST['password']));

    // On récupère le mot de passe haché depuis la BDD
    $requete = "SELECT email, password FROM utilisateur WHERE email = ?";
    $stmt = mysqli_prepare($idcom, $requete);
    mysqli_stmt_bind_param($stmt, "s", $pseudo);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id_user, $password_hache);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Vérification du mot de passe
    if ($password_hache && password_verify($password_saisi, $password_hache)) {
        $_SESSION['id_user'] = $id_user;
        header('Location: main.php');
        exit;
    } else {
        $_SESSION['register_error'] = "Utilisateur ou mot de passe incorrect";
        header('Location: login.php');
        exit;
    }

    mysqli_close($idcom);
}
?>