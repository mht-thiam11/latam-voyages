<?php
session_start();
require_once "config.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $sql = "
        SELECT
            id_destination,
            nom,
            description,
            image
        FROM Destinations
    ";
    $result = $conn->query($sql);
    $destinations = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
    $conn->close();
} catch (mysqli_sql_exception $e) {
    die("Erreur base de données : " . $e->getMessage());
}

include "header.php";
?>

<style>
html, body {
    margin: 0;
    padding: 0;
    height: 100%;
}

body {
    background: url('images/guaranda.jpeg') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Segoe UI', sans-serif;
    color: #333;
}

/* Bloc central avec fond translucide */
.vitrine-page {
    background-color: rgba(255, 255, 255, 0.5); /* translucide */
    padding: 40px 20px;
    margin: 40px auto;
    max-width: 1200px;
    border-radius: 12px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}
</style>

<main class="vitrine-page">
    <h1 class="vitrine-title">
        <?php if (!empty($_SESSION["id_client"])): ?>
            Bienvenue <?= htmlspecialchars(
                $_SESSION["prenom"]
            ) ?>, explorez nos destinations !
        <?php else: ?>
            Bienvenue invité, explorez nos destinations !
        <?php endif; ?>
    </h1>

    <div class="destination-list">
        <?php if (!empty($destinations)): ?>
            <?php foreach ($destinations as $row): ?>
                <div class="destination-item">
                    <img src="<?= htmlspecialchars(
                        $row["image"]
                    ) ?>" alt="<?= htmlspecialchars($row["nom"]) ?>">
                    <div class="details">
                        <h3><?= htmlspecialchars($row["nom"]) ?></h3>
                        <p><?= htmlspecialchars($row["description"]) ?></p>
                        <a href="choisir_voyage.php?id=<?= $row[
                            "id_destination"
                        ] ?>" class="btn">Choisir cette destination</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune destination disponible.</p>
        <?php endif; ?>
    </div>

    <div style="text-align: center;">
        <a href="bienvenue.php" class="back-btn">⬅ Retour à l'accueil</a>
    </div>
</main>

<?php include "footer.php"; ?>
