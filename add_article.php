<?php
session_start();
include("connex.inc.php");
$idcom = connex("projetweb", "myparam");

function handleFileUpload($file) {
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    $photo_name = uniqid() . "_" . basename($file["name"]);
    $target_file = $upload_dir . $photo_name;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    } else {
        global $erreur;
        $erreur = "Erreur lors de l'upload de la photo.";
        return null;
    }
}

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
    if ($categorie === 'voiture' && isset($_FILES['photo_voiture']) && $_FILES['photo_voiture']['error'] == UPLOAD_ERR_OK) {
        $photo_path = handleFileUpload($_FILES['photo_voiture']);
    } elseif ($categorie === 'carte' && isset($_FILES['photo_carte']) && $_FILES['photo_carte']['error'] == UPLOAD_ERR_OK) {
        $photo_path = handleFileUpload($_FILES['photo_carte']);
    } elseif ($categorie === 'vetement' && isset($_FILES['photo_vetement']) && $_FILES['photo_vetement']['error'] == UPLOAD_ERR_OK) {
        $photo_path = handleFileUpload($_FILES['photo_vetement']);
    } elseif ($categorie === 'livre' && isset($_FILES['photo_livre']) && $_FILES['photo_livre']['error'] == UPLOAD_ERR_OK) {
        $photo_path = handleFileUpload($_FILES['photo_livre']);
    }

    // Vérifie que l'utilisateur est connecté
    if (!isset($_SESSION['id_user'])) {
        $erreur = "Vous devez être connecté pour publier un article.";
    }

    if (!$erreur) {
        $vendeur_id = $_SESSION['id_user'];
        $taille = !empty($_POST['taille']) ? mysqli_real_escape_string($idcom, $_POST['taille']) : null;

        $query = "INSERT INTO articles (nom, description, prix, categorie, kilometrage, etat, taille, photos, vendeur_id)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($idcom, $query);
        mysqli_stmt_bind_param($stmt, "ssdsssssi", $nom, $description, $prix, $categorie, $kilometrage, $etat, $taille, $photo_path, $vendeur_id);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php");
            exit();
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
    <link rel="stylesheet" href="assets/css/index.css">
    <title>Ajouter un article</title>
    <style>
        main {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #444;
        }

        .form-article {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input, textarea, select, .publish {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        .publish {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .publish:hover {
            background-color: #45a049;
        }

        .erreur {
            color: red;
            font-weight: bold;
            text-align: center;
        }

        .message {
            color: green;
            font-weight: bold;
            text-align: center;
        }

        #champ-voiture, #champ-carte, #champ-vetement, #champ-livre {
            display: none;
        }
    </style>
</head>
<?php
include("header.php");
?>

<main>
    <h1>Ajouter un article</h1>

    <?php if ($success): ?><p class="message"><?= htmlspecialchars($success) ?></p><?php endif; ?>
    <?php if ($erreur): ?><p class="erreur"><?= htmlspecialchars($erreur) ?></p><?php endif; ?>

    <form class="form-article" method="post" enctype="multipart/form-data"> <!-- enctype pour le téléchargement de fichiers -->
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
            <label for="photo_voiture">Photo :</label>
            <input type="file" name="photo_voiture" accept="image/*">
        </div>

        <div id="champ-carte" style="display:none;">
            <label for="etat">État :</label>
            <input type="text" name="etat">
            <label for="photo_carte">Photo :</label>
            <input type="file" name="photo_carte" accept="image/*">
        </div>

        <div id="champ-vetement" style="display:none;">
            <label for="taille">Taille :</label>
            <input type="text" name="taille">
            <label for="photo_vetement">Photo :</label>
            <input type="file" name="photo_vetement" accept="image/*">
        </div>

        <div id="champ-livre" style="display:none;">
            <label for="etat">État :</label>
            <input type="text" name="auteur">
            <label for="photo_livre">Photo :</label>
            <input type="file" name="photo_livre" accept="image/*">
        </div>

        <button class="publish" type="submit">Publier</button>
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
</main>
<?php include("footer.php"); ?>
</html>
