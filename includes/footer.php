<?php 
if (basename(path: $_SERVER['PHP_SELF']) === 'index.php') {
    $base_url = '';
} else {
    $base_url = '../';
}
?>

<footer>
    <p>&copy; 2025 - Projet E-commerce</p>
</footer>
<stylesheet>
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/footer.css" media="screen" type="text/css" />
</stylesheet>
