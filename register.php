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
        <form id="registerForm" method="POST" enctype="multipart/form-data">
            <h1>S'enregistrer</h1>
         
            <label><b>Email</b></label>
            <input type="text" placeholder="Entrer votre adresse email" name="email" required>
        
            <label><b>Mot de passe</b></label>
            <input type="password" placeholder="Entrer le mot de passe" name="password" required>
    
            <label><b>Nom</b></label>
            <input type="text" placeholder="Entrer votre nom" name="name" required>

            <label><b>Prénom</b></label>
            <input type="text" placeholder="Entrer votre prénom" name="firstname" required>

            <label><b>Adresse</b></label>
            <input type="text" placeholder="Entrer votre adresse" name="address" required>

            <label><b>Êtes-vous vendeur ? : </b></label>
            <input type="radio" name="is_seller" value="1" id="sellerYes"> Oui
            <input type="radio" name="is_seller" value="0" id="sellerNo"> Non

            <div id="sellerFields" style="display: none;">
                <br/>
                <label><b>CNI</b></label>
                <input type="file" name="cni" placeholder="Carte d'identité" accept="image/png, image/jpeg">
                <br/><br/>
                <label><b>IBAN</b></label>
                <input type="text" name="iban" placeholder="IBAN">
                
            </div>

            <button type="submit" id="registerButton" name="action" value="register">REGISTER</button>
            <?php
            // Vérifie si une erreur est stockée dans la variable de session
            if(isset($_SESSION['register_error'])) {
                echo "<div class='error-message'>" . $_SESSION['register_error'] . "</div>";
                unset($_SESSION['register_error']);
            }
            ?>
        </form>

        <script>
            // Récupère les radios
            const radioYes = document.getElementById("sellerYes");
            const radioNo = document.getElementById("sellerNo");
            const sellerFields = document.getElementById("sellerFields");

            // Fonction pour gérer l'affichage des champs
            function toggleSellerFields() {
                if (radioYes.checked) {
                    sellerFields.style.display = "block";
                } else {
                    sellerFields.style.display = "none";
                }
            }

            // Écoute les changements
            radioYes.addEventListener("change", toggleSellerFields);
            radioNo.addEventListener("change", toggleSellerFields);
        </script>

    </div>

    <script>
        document.getElementById("registerButton").addEventListener("click", function() {
            document.getElementById("registerForm").action = "registerform.php";
        });
    </script>
</body>
</html>
