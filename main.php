<?php
session_start();

include("connex.inc.php");
$idcom = connex("ex9", "myparam");
$requete = "SELECT * FROM commentaires ORDER BY date_commentaire DESC";
$resultat = mysqli_query($idcom, $requete);
$commentaires = mysqli_fetch_all($resultat, MYSQLI_ASSOC);
mysqli_free_result($resultat);
mysqli_close($idcom);

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}
?>


<p>Bienvenue, <?php echo htmlspecialchars($_SESSION['pseudo']); ?> !</p>
<a href="logout.php">Se déconnecter</a>


<h1>Une belle image</h1>
<img src="https://c0.lestechnophiles.com/images.frandroid.com/wp-content/uploads/2019/05/windows-xp-hd.jpg?resize=1200&key=3f11a5a8&watermark" alt="Belle image" style="width:300px;"><br>
<br>
<p>Espace commentaire</p>
<form method="POST" action="form/commentaire.php">
    <label for="commentaire">Votre commentaire :</label><br>
    <textarea id="commentaire" name="commentaire" rows="4" cols="50"></textarea><br>
    <input type="submit" value="Envoyer">
</form>
<?php
if (isset($_SESSION['error_main'])) {
    echo "<div class='error-message'>" . $_SESSION['error_main'] . "</div>";
    unset($_SESSION['error_main']);
}
?>

<style type="text/css">
    .error-message {
        color: red;
        font-weight: bold;
        margin-top: 10px;
    }
</style>

<?php if ($commentaires): ?>
    <h3>Commentaires :</h3>
<?php foreach ($commentaires as $c): ?>
        <div style="border: 1px solid; padding: 10px; margin: 10px 0; width: 500px;">
            <strong><?= htmlspecialchars($c['pseudo']) ?></strong> le <?= $c['date_commentaire'] ?><br>
            <?= nl2br(htmlspecialchars($c['commentaire'])) ?>
        </div>
<?php endforeach; ?>
<?php else: ?>
    <p>Aucun commentaire trouvé.</p>
<?php endif; ?>