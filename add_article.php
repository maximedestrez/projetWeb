<?php
session_start();
include("connex.inc.php");
$idcom = connex("projetweb", "myparam");

$erreur = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = mysqli_real_escape_string($idcom, $_POST['nom']);
    $description = mysqli_real_escape_string($idcom, $_POST['description']);
    $prix = floatval($_POST['prix']);
    $categorie = $_POST['categorie'];
    $kilometrage = !empty($_POST['kilometrage']) ? intval($_POST['kilometrage']) : null;
    $etat = !empty($_POST['etat']) ? mysqli_real_escape_string($idcom, $_POST['etat']) : null;

    // Gestion du fichier photo (si fourni)
    $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $photo_name = uniqid() . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $upload_dir . $photo_name;

        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo_path = $target_file;
        } else {
            $erreur = "Erreur lors de l'upload de la photo.";
        }
    }

    // Vérifie que l'utilisateur est connecté
    if (!isset($_SESSION['id_user'])) {
        $erreur = "Vous devez être connecté pour publier un article.";
    }

    if (!$erreur) {
        $vendeur_id = $_SESSION['id_user'];

        $query = "INSERT INTO articles (nom, description, prix, categorie, kilometrage, etat, photos, vendeur_id)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($idcom, $query);
        mysqli_stmt_bind_param($stmt, "ssdssssi", $nom, $description, $prix, $categorie, $kilometrage, $etat, $photo_path, $vendeur_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Article ajouté avec succès !";
        } else {
            $erreur = "Erreur SQL : " . mysqli_error($idcom);
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($idcom);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un article</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        label { display: block; margin-top: 10px; }
        input, textarea, select { width: 100%; padding: 8px; margin-top: 5px; }
        .message { margin-top: 15px; color: green; }
        .erreur { color: red; }
    </style>
</head>
<body>
    <h1>Ajouter un article</h1>

    <?php if ($success): ?><p class="message"><?= htmlspecialchars($success) ?></p><?php endif; ?>
    <?php if ($erreur): ?><p class="erreur"><?= htmlspecialchars($erreur) ?></p><?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label for="nom">Nom :</label>
        <input type="text" name="nom" required>

        <label for="description">Description :</label>
        <textarea name="description" required></textarea>

        <label for="prix">Prix (€) :</label>
        <input type="number" name="prix" step="0.01" required>

        <label for="categorie">Catégorie :</label>
        <select name="categorie" id="categorie" required>
            <option value="">-- Sélectionner --</option>
            <option value="voiture">Voiture</option>
            <option value="carte">Carte de collection</option>
            <option value="vetement">Vêtement</option>
            <option value="livre">Livre</option>
        </select>

        <div id="champ-voiture" style="display:none;">
            <label for="kilometrage">Kilométrage :</label>
            <input type="number" name="kilometrage" min="0">
            <label for="photo">Photo :</label>
            <input type="file" name="photo" accept="image/*">
        </div>

        <div id="champ-carte" style="display:none;">
            <label for="etat">État :</label>
            <input type="text" name="etat">

            <label for="photo">Photo :</label>
            <input type="file" name="photo" accept="image/*">
        </div>

        <div id="champ-vetement" style="display:none;">
            <label for="taille">Taille :</label>
            <input type="text" name="taille">
            <label for="photo">Photo :</label>
            <input type="file" name="photo" accept="image/*">
        </div>

        <div id="champ-livre" style="display:none;">
            <label for="etat">État :</label>
            <input type="text" name="auteur">
        </div>

        <button type="submit">Publier</button>
    </form>

    <script>
    document.getElementById('categorie').addEventListener('change', function () {
        const voiture = document.getElementById('champ-voiture');
        const carte = document.getElementById('champ-carte');
        const vetement = document.getElementById('champ-vetement');
        const livre = document.getElementById('champ-livre');

        voiture.style.display = 'none';
        carte.style.display = 'none';
        vetement.style.display = 'none';
        livre.style.display = 'none';

        if (this.value === 'voiture') {
            voiture.style.display = 'block';
        } else if (this.value === 'carte') {
            carte.style.display = 'block';
        } else if (this.value === 'vetement') {
            vetement.style.display = 'block';
        } else if (this.value === 'livre') {
            livre.style.display = 'block';
        }
    });
    </script>
</body>
</html>
