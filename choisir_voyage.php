<?php
session_start();
include "config.php";

$id_client = $_SESSION["id_client"] ?? null;

if (!isset($_GET["id"])) {
    header("Location: vitrine.php");
    exit();
}

$id_destination = intval($_GET["id"]);
$message = "";
$prix_estime = null;

$req = $conn->prepare(
    "SELECT nom, pays, description, image, distance FROM Destinations WHERE id_destination = ?"
);
$req->bind_param("i", $id_destination);
$req->execute();
$dest = $req->get_result()->fetch_assoc();
$req->close();

if ($_SERVER["REQUEST_METHOD"] === "POST" && $id_client) {
    $date_depart = $_POST["date_depart"];
    $date_retour = $_POST["date_retour"];

    $today = new DateTime();
    $start = new DateTime($date_depart);
    $end = new DateTime($date_retour);
    $duree = $start->diff($end)->days;

    if ($start < $today || $end < $today) {
        $message = "❌ Les dates doivent être postérieures à aujourd'hui.";
    } elseif ($duree <= 0) {
        $message = "❌ La date de retour doit être après la date de départ.";
    } else {
        // Calcul du prix
        $transport = round($dest["distance"] * 0.09); // 0.09 €/km
        $sejour = $duree * 20; // 20 €/jour
        $variation = 1 + rand(-5, 5) / 100; // ±5 %
        $prix_estime = round(($transport + $sejour) * $variation, 2);

        // Enregistrement en base
        $stmt = $conn->prepare(
            "INSERT INTO Voyages (id_client, id_destination, date_depart, date_retour, prix) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "iissd",
            $id_client,
            $id_destination,
            $date_depart,
            $date_retour,
            $prix_estime
        );

        if ($stmt->execute()) {
            $id_voyage = $stmt->insert_id;
            $res = $conn->prepare(
                "INSERT INTO Reservations (id_client, id_voyage) VALUES (?, ?)"
            );
            $res->bind_param("ii", $id_client, $id_voyage);
            $res->execute();
            header("Location: confirmation.php");
            exit();
        } else {
            $message = "❌ Erreur lors de la réservation : " . $conn->error;
        }

        $stmt->close();
        $res->close();
    }
}
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
    <h2>Réservez votre voyage à <?= htmlspecialchars(
        $dest["nom"]
    ) ?> (<?= htmlspecialchars($dest["pays"]) ?>)</h2>

    <img src="<?= htmlspecialchars(
        $dest["image"]
    ) ?>" alt="<?= htmlspecialchars(
    $dest["nom"]
) ?>" style="max-width: 100%; border-radius: 10px; margin: 20px 0;">

    <p><?= nl2br(htmlspecialchars($dest["description"])) ?></p>
    <p><strong>Distance depuis Paris :</strong> <?= htmlspecialchars(
        $dest["distance"]
    ) ?> km</p>

    <?php if (!empty($message)): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <?php if ($id_client): ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="date_depart">Date de départ :</label>
                <input type="date" id="date_depart" name="date_depart" min="<?= date(
                    "Y-m-d"
                ) ?>" required>
            </div>

            <div class="form-group">
                <label for="date_retour">Date de retour :</label>
                <input type="date" id="date_retour" name="date_retour" min="<?= date(
                    "Y-m-d"
                ) ?>" required>
            </div>

            <!-- ✅ Estimation dynamique du prix -->
            <div id="prix_estime" style="font-weight: bold; color: green; margin: 10px 0;"></div>

            <div class="form-group">
                <input type="submit" value="Réserver ce voyage">
            </div>
        </form>
    <?php else: ?>
        <p class="message">🛑 Vous devez être <a href="connexion.php">connecté</a> pour réserver.</p>
    <?php endif; ?>
</main>

<!-- ✅ Bouton retour centré -->
<div style="text-align: center; margin: 40px 0;">
    <a href="vitrine.php" class="back-btn">⬅ Retour à la vitrine</a>
</div>

<!-- ✅ Script dynamique pour estimation -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const dateDepart = document.getElementById("date_depart");
    const dateRetour = document.getElementById("date_retour");
    const prixDiv = document.getElementById("prix_estime");
    const distance = <?= (int) $dest["distance"] ?>;

    function calculerPrix() {
        const d1 = new Date(dateDepart.value);
        const d2 = new Date(dateRetour.value);
        if (dateDepart.value && dateRetour.value && d2 > d1) {
            const diff = (d2 - d1) / (1000 * 60 * 60 * 24);
            const transport = Math.round(distance * 0.09);
            const sejour = diff * 20;
            const variation = 1 + (Math.floor(Math.random() * 11) - 5) / 100;
            const prix = ((transport + sejour) * variation).toFixed(2);
            prixDiv.textContent = `💰 Prix estimé : ${prix} €`;
        } else {
            prixDiv.textContent = "";
        }
    }

    dateDepart.addEventListener("change", () => {
        dateRetour.min = dateDepart.value;
        if (dateRetour.value < dateDepart.value) {
            dateRetour.value = "";
        }
        calculerPrix();
    });

    dateRetour.addEventListener("change", calculerPrix);
});
</script>

<?php include "footer.php"; ?>
<?php $conn->close(); ?>
