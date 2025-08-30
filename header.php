<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LATAM Voyages</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="top-bar">
    <div class="nav-container">
        <div class="logo-left">
            <a href="bienvenue.php" class="logo">LATAM Voyages</a>
        </div>
        <div class="nav-links">
            <a href="vitrine.php">Destinations</a>
            <a href="contact.php">Contact</a>
            <?php if (isset($_SESSION["id_client"])): ?>
                <a href="profil_client.php?id=<?= $_SESSION[
                    "id_client"
                ] ?>">Mon Profil</a>
                <a href="historique.php?id=<?= $_SESSION[
                    "id_client"
                ] ?>">Historique</a>
                <a href="deconnexion.php">Déconnexion</a>
            <?php else: ?>
                <a href="connexion.php">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</nav>