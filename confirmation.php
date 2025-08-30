<?php
session_start();
include "config.php";

if (!isset($_SESSION["id_client"])) {
    header("Location: connexion.php");
    exit();
}

$id_client = $_SESSION["id_client"];
$message = "";

// Récupérer la dernière réservation
$sql = "
SELECT r.id_reservation, r.date_reservation, r.statut, 
       v.id_voyage, v.date_depart, v.date_retour, v.prix,
       d.nom AS destination, d.pays, d.image
FROM Reservations r
JOIN Voyages v ON r.id_voyage = v.id_voyage
JOIN Destinations d ON v.id_destination = d.id_destination
WHERE r.id_client = ?
ORDER BY r.date_reservation DESC
LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_client);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $reservation = $result->fetch_assoc();

    // Vérifier si paiement a été fait
    $check = $conn->prepare("SELECT 1 FROM Paiements WHERE id_reservation = ?");
    $check->bind_param("i", $reservation["id_reservation"]);
    $check->execute();
    $check->store_result();
    $est_paye = $check->num_rows > 0;
    $check->close();
} else {
    $message = "Aucune réservation trouvée.";
}

$stmt->close();
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

<main class="container glass-container">
    <h2>Confirmation de votre réservation</h2>

    <?php if (!empty($message)): ?>
        <p class="message"><?= $message ?></p>
    <?php else: ?>
        <div class="welcome-message">
            <h3>Destination : <?= htmlspecialchars(
                $reservation["destination"]
            ) ?> (<?= htmlspecialchars($reservation["pays"]) ?>)</h3>

            <img src="<?= htmlspecialchars(
                $reservation["image"]
            ) ?>" alt="Image destination" style="max-width: 100%; border-radius: 10px; margin: 20px 0;">

            <p><strong>Date de départ :</strong> <?= htmlspecialchars(
                $reservation["date_depart"]
            ) ?></p>
            <p><strong>Date de retour :</strong> <?= htmlspecialchars(
                $reservation["date_retour"]
            ) ?></p>
            <p><strong>Prix total :</strong> <?= htmlspecialchars(
                $reservation["prix"]
            ) ?> €</p>
            <p><strong>Statut :</strong> <?= htmlspecialchars(
                $reservation["statut"]
            ) ?></p>
            <p><strong>Date de réservation :</strong> <?= htmlspecialchars(
                $reservation["date_reservation"]
            ) ?></p>

            <?php if (!$est_paye): ?>
                <div style="margin-top: 20px;">
                    <a href="paiement.php?id=<?= $reservation[
                        "id_voyage"
                    ] ?>" class="btn">💳 Payer maintenant</a>
                </div>
            <?php else: ?>
                <p class="success-message" style="margin-top: 20px;">Merci d'avoir payé ce voyage ✈️</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <a href="bienvenue.php" class="back-btn">Retour à l'accueil</a>
</main>

<?php include "footer.php"; ?>

<?php $conn->close(); ?>
