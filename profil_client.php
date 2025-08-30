<?php
session_start();
include "config.php";

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION["id_client"])) {
    echo "<p style='text-align:center; margin-top: 50px; font-size: 1.2rem;'>Vous n'êtes pas connecté. <a href='connexion.php'>Cliquez ici pour vous connecter</a>.</p>";
    exit();
}

// Récupère l'ID à afficher (via URL ou session)
$id_client = isset($_GET["id"]) ? intval($_GET["id"]) : $_SESSION["id_client"];

// Requête SQL
$sql = "SELECT * FROM Clients WHERE id_client = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_client);
$stmt->execute();
$result = $stmt->get_result();

// Cas : client introuvable
if ($result->num_rows === 0) {
    include "header.php";
    echo "<main class='container profile-page' style='text-align:center;'>
            <h2>Client non trouvé</h2>
            <p>Le profil demandé n'existe pas ou a été supprimé.</p>
            <a class='back-btn' href='connexion.php'>Retour à la connexion</a>
          </main>";
    include "footer.php";
    exit();
}

$client = $result->fetch_assoc();
$stmt->close();
$conn->close();
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

<main class="profile-page container glass-container">
    <h2>Profil du Client</h2>
    <div class="client-info">
        <p><strong>Nom :</strong> <?= htmlspecialchars($client["nom"]) ?></p>
        <p><strong>Prénom :</strong> <?= htmlspecialchars(
            $client["prenom"]
        ) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars(
            $client["email"]
        ) ?></p>
        <p><strong>Téléphone :</strong> <?= htmlspecialchars(
            $client["telephone"]
        ) ?></p>
        <p><strong>Adresse :</strong> <?= nl2br(
            htmlspecialchars($client["adresse"])
        ) ?></p>
    </div>
    <a href="bienvenue.php" class="back-btn">Retour à l'accueil</a>
</main>

<?php include "footer.php"; ?>
