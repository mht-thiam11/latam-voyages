<?php
session_start();
if (!isset($_SESSION["id_client"])) {
    header("Location: connexion.php");
    exit();
}

include "config.php";

$id_client = $_SESSION["id_client"];
$id_voyage = $_GET["id"] ?? null;
$message = "";
$prix_calculé = null;

if (!$id_voyage) {
    header("Location: historique.php");
    exit();
}

// Récupération des infos réservation
$sql = "SELECT v.date_depart, v.date_retour, v.id_destination, d.distance, r.statut
        FROM Voyages v
        JOIN Reservations r ON v.id_voyage = r.id_voyage
        JOIN Destinations d ON v.id_destination = d.id_destination
        WHERE v.id_voyage = ? AND r.id_client = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_voyage, $id_client);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    $message = "❌ Réservation introuvable ou accès non autorisé.";
} else {
    $reservation = $res->fetch_assoc();

    if ($reservation["statut"] !== "En attente") {
        $message =
            "⚠️ Seules les réservations en attente peuvent être modifiées.";
    } elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nouvelle_date_depart = $_POST["date_depart"];
        $nouvelle_date_retour = $_POST["date_retour"];

        $start = new DateTime($nouvelle_date_depart);
        $end = new DateTime($nouvelle_date_retour);
        $today = new DateTime();
        $duree = $start->diff($end)->days;

        if ($start < $today || $end < $today) {
            $message = "❌ Les dates doivent être postérieures à aujourd'hui.";
        } elseif ($nouvelle_date_retour <= $nouvelle_date_depart) {
            $message =
                "❌ La date de retour doit être après la date de départ.";
        } else {
            // Calcul réel du prix
            $transport = round($reservation["distance"] * 0.09); // 0.09 €/km
            $sejour = $duree * 20; // 20 €/jour
            $variation = 1 + rand(-5, 5) / 100; // ±5 %
            $prix_calculé = round(($transport + $sejour) * $variation, 2);

            $update = $conn->prepare(
                "UPDATE Voyages SET date_depart = ?, date_retour = ?, prix = ? WHERE id_voyage = ?"
            );
            $update->bind_param(
                "ssdi",
                $nouvelle_date_depart,
                $nouvelle_date_retour,
                $prix_calculé,
                $id_voyage
            );

            if ($update->execute()) {
                header("Location: historique.php");
                exit();
            } else {
                $message = "❌ Erreur lors de la mise à jour.";
            }
            $update->close();
        }
    }
}
$stmt->close();
$conn->close();
?>

<?php include "header.php"; ?>

<main class="container">
    <h2>Modifier ma réservation</h2>

    <?php if (!empty($message)): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <?php if (
        isset($reservation) &&
        $reservation["statut"] === "En attente"
    ): ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="date_depart">Nouvelle date de départ :</label>
                <input type="date" id="date_depart" name="date_depart"
                       value="<?= $reservation["date_depart"] ?>"
                       min="<?= date("Y-m-d") ?>" required>
            </div>

            <div class="form-group">
                <label for="date_retour">Nouvelle date de retour :</label>
                <input type="date" id="date_retour" name="date_retour"
                       value="<?= $reservation["date_retour"] ?>"
                       min="<?= date("Y-m-d") ?>" required>
            </div>

            <p id="prix-estime" style="font-weight: bold; color: green; margin: 10px 0;"></p>

            <?php if ($prix_calculé): ?>
                <p style="font-weight: bold; color: green; margin: 10px 0;">
                    💰 Nouveau prix confirmé : <?= number_format(
                        $prix_calculé,
                        2
                    ) ?> €
                </p>
            <?php endif; ?>

            <div class="form-group">
                <input type="submit" value="Enregistrer les modifications">
            </div>
        </form>
    <?php endif; ?>
</main>

<!-- ✅ Bouton retour centré -->
<div style="text-align: center; margin: 40px 0;">
    <a href="historique.php" class="back-btn">⬅ Retour à l'historique</a>
</div>

<?php include "footer.php"; ?>

<!-- 💡 JS pour afficher prix estimé en direct -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const dateDepart = document.getElementById("date_depart");
    const dateRet = document.getElementById("date_retour");
    const prixAffiche = document.getElementById("prix-estime");
    const distance = <?= $reservation["distance"] ?>;

    const updatePrix = () => {
        const d1 = new Date(dateDepart.value);
        const d2 = new Date(dateRet.value);
        const today = new Date();

        if (dateDepart.value && dateRet.value && d2 > d1 && d1 >= today) {
            const jours = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
            const prix = (distance * 0.09) + (jours * 20);
            prixAffiche.textContent = `💰 Prix estimé : ${prix.toFixed(2)} €`;
        } else {
            prixAffiche.textContent = "";
        }
    };

    dateDepart.addEventListener("change", () => {
        dateRet.min = dateDepart.value;
        if (dateRet.value < dateDepart.value) dateRet.value = "";
        updatePrix();
    });

    dateRet.addEventListener("change", updatePrix);

    // Initialiser si valeurs présentes
    if (dateDepart.value && dateRet.value) updatePrix();
});
</script>