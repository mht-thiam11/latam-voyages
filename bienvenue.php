<?php
session_start();
$id_client = $_SESSION["id_client"] ?? null;
?>

<?php include "header.php"; ?>

<style>
body {
    background-image: url('images/guaranda.jpeg'); /* mets ton image ici */
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    background-attachment: fixed;
}
</style>

<div class="container">
    <div class="welcome-message">
        <h1>Bienvenue sur LATAM Voyages</h1>
        <p>Nous sommes heureux de vous accueillir sur notre site.</p>
        <p>Profitez de nos voyages et destinations !</p>

        <!-- ✅ Bouton centré au-dessus de l'image -->
        <div style="margin: 30px 0;">
            <a href="vitrine.php" class="back-btn">🌎 Accédez à nos destinations</a>
        </div>

        <figure>
            <img src="images/amerique_du_sud.jpg" alt="Amérique du Sud" class="image-accueil">
            <figcaption>Les îles Galápagos - Un trésor naturel en plein océan Pacifique</figcaption>
        </figure>
    </div>
</div>

<?php include "footer.php"; ?>
