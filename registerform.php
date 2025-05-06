<?php
session_start();
function mot_de_passe_regex($mdp){
    return preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,}$/", $mdp);
}

//Fonction permettant d'enregistrer de nouveau utilisateurs
if(isset($_POST['pseudo']) && isset($_POST['password'])) {

    // Vérification de la validité du mot de passe
    // if(!mot_de_passe_regex($_POST['password'])) {
    //     $_SESSION['register_error'] = "Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
    //     header("Location: login.php");
    //     exit;
    // }

    // Vérification de la présence du pseudo et du mot de passe dans la BDD
    include("connex.inc.php");
    $idcom = connex("projetweb", "myparam");
    
    $pseudo = mysqli_real_escape_string($idcom, htmlspecialchars($_POST['pseudo'])); 
    $password = mysqli_real_escape_string($idcom, htmlspecialchars($_POST['password']));
    
    $check_query = "SELECT pseudo FROM utilisateur WHERE pseudo = '".$pseudo."'";
    $check_result = mysqli_query($idcom, $check_query);
    
    if(mysqli_num_rows($check_result) > 0) {
        $_SESSION['register_error'] = "Ce pseudonyme existe déjà.";
        header("Location: login.php");
        exit;
    } else {
        $password = password_hash($password, PASSWORD_BCRYPT); // Hashage du mot de passe
        $insert_query = "INSERT INTO utilisateur (pseudo, password) VALUES ('".$pseudo."', '".$password."')";
        $insert_result = mysqli_query($idcom, $insert_query);
        
        if($insert_result) {
            $_SESSION['pseudo'] = $pseudo;
            $_SESSION['id_user'] = mysqli_insert_id($idcom);
            header('Location: main.php');
            exit;
        } else {
            echo "Erreur lors de l'inscription. Veuillez réessayer plus tard.";
        }
    }
    
    mysqli_close($idcom);
}
?>
