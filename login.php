<?php
session_start();
?>

<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="assets/css/stylelogin.css" media="screen" type="text/css" />
</head>
<body>
    <div id="container">
        <form id="loginForm" method="POST">
            <h1>Connexion</h1>
         
            <label><b>Email</b></label>
            <input type="text" placeholder="Entrer votre adresse email" name="email" required>
        
            <label><b>Mot de passe</b></label>
            <input type="password" placeholder="Entrer le mot de passe" name="password" required>
        
            <button type="submit" id="loginButton" name="action" value="login">LOGIN</button>
            <p>Pas encore inscrit ? <a href="register.php">Créer un compte</a></p>            
            <?php
            // Vérifie si une erreur est stockée dans la variable de session
            if(isset($_SESSION['register_error'])) {
                echo "<div class='error-message'>" . $_SESSION['register_error'] . "</div>";
                unset($_SESSION['register_error']);
            }
            ?>
        </form>
    </div>

    <script>
        // Fonction pour changer l'action du formulaire en fonction du bouton cliqué
        document.getElementById("loginButton").addEventListener("click", function() {
            document.getElementById("loginForm").action = "login.php";
        });
    </script>
</body>
</html>
