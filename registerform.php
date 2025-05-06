<?php
session_start();
include("connex.inc.php");
$idcom = connex("projetweb", "myparam");

function verifierIBAN($iban) {
    $iban = strtoupper(str_replace(' ', '', $iban));
    if (!preg_match('/^[A-Z0-9]+$/', $iban)) return false;
    $iban = substr($iban, 4) . substr($iban, 0, 4);
    $iban = preg_replace_callback('/[A-Z]/', fn($m) => ord($m[0]) - 55, $iban);
    $mod = intval(substr($iban, 0, 1));
    for ($i = 1; $i < strlen($iban); $i++) {
        $mod = ($mod * 10 + intval($iban[$i])) % 97;
    }
    return $mod === 1;
}

// Si formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['password'])) {
    // Récupération sécurisée des champs
    $email = mysqli_real_escape_string($idcom, $_POST['email']);
    $password = mysqli_real_escape_string($idcom, $_POST['password']);
    $nom = mysqli_real_escape_string($idcom, $_POST['name']);
    $prenom = mysqli_real_escape_string($idcom, $_POST['firstname']);
    $adresse = mysqli_real_escape_string($idcom, $_POST['address']);
    $est_vendeur = isset($_POST['is_seller']) ? intval($_POST['is_seller']) : 0;
    $iban = $est_vendeur ? mysqli_real_escape_string($idcom, $_POST['iban']) : null;
    if ($iban && !verifierIBAN($iban)) {
        $_SESSION['register_error'] = "IBAN invalide.";
        header("Location: login.php");
        exit;
    }

    // Vérifie si email déjà utilisé
    $check = mysqli_query($idcom, "SELECT id FROM utilisateur WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION['register_error'] = "Cet email est déjà utilisé.";
        header("Location: login.php");
        exit;
    }

    // Hash du mot de passe
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Gestion du fichier CNI
    if ($est_vendeur && isset($_FILES['cni']) && $_FILES['cni']['error'] === 0) {
        $cni_tmp = $_FILES['cni']['tmp_name'];
        $cni_extension = pathinfo($_FILES['cni']['name'], PATHINFO_EXTENSION);
        $cni_name = $email . "." . $cni_extension;
        $cni_path = "uploads/" . uniqid("cni_") . "_" . $cni_name; // On génère un nom unique pour éviter les collisions

        if (!move_uploaded_file($cni_tmp, $cni_path)) {
            $_SESSION['register_error'] = "Erreur lors du téléchargement du fichier CNI.";
            header("Location: login.php");
            exit;
        }
        // Le fichier est enregistré, mais on ne le stocke pas dans la BDD
    }

    // Insertion en BDD
    $query = "INSERT INTO utilisateur (nom, prenom, email, password, adresse, est_vendeur, iban) 
              VALUES ('$nom', '$prenom', '$email', '$password_hash', '$adresse', $est_vendeur, " . ($iban ? "'$iban'" : "NULL") . ")";
    
    if (mysqli_query($idcom, $query)) {
        $_SESSION['email'] = $email;
        $_SESSION['id_user'] = mysqli_insert_id($idcom);
        header("Location: main.php");
        exit;
    } else {
        $_SESSION['register_error'] = "Erreur d'enregistrement.";
        header("Location: login.php");
        exit;
    }
}
?>
