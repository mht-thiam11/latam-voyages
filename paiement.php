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

// Vérification : le voyage existe et appartient au client
$sql = "SELECT v.prix, d.nom AS destination, r.id_reservation, r.statut
        FROM Voyages v
        JOIN Reservations r ON r.id_voyage = v.id_voyage
        JOIN Destinations d ON d.id_destination = v.id_destination
        WHERE v.id_voyage = ? AND r.id_client = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_voyage, $id_client);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $message = "❌ Réservation introuvable ou non autorisée.";
} else {
    $reservation = $result->fetch_assoc();

    if ($reservation["statut"] !== "En attente") {
        $message = "⚠️ Ce voyage a déjà été payé ou annulé.";
    } elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Validation expiration carte
        $mois = $_POST["expiration_mois"];
        $annee = $_POST["expiration_annee"];
        $expiration_timestamp = strtotime("$annee-$mois-01");
        $now = strtotime(date("Y-m-01"));

        if ($expiration_timestamp < $now) {
            $message =
                "❌ La carte est expirée. Veuillez choisir une date valide.";
        } else {
            // Simuler un paiement : insertion dans la table Paiements
            $id_reservation = $reservation["id_reservation"];
            $montant = $reservation["prix"];

            $insert = $conn->prepare(
                "INSERT INTO Paiements (id_reservation, montant) VALUES (?, ?)"
            );
            $insert->bind_param("id", $id_reservation, $montant);
            $insert->execute();
            $insert->close();

            // Mise à jour du statut
            $update = $conn->prepare(
                "UPDATE Reservations SET statut = 'Confirmée' WHERE id_reservation = ?"
            );
            $update->bind_param("i", $id_reservation);
            $update->execute();
            $update->close();

            header("Location: confirmation.php");
            exit();
        }
    }
}
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

<main class="container glass-container">
    <h2>Paiement de votre réservation</h2>

    <?php if (!empty($message)): ?>
        <p class="message"><?= $message ?></p>
    <?php elseif (isset($reservation)): ?>
        <p><strong>Destination :</strong> <?= htmlspecialchars(
            $reservation["destination"]
        ) ?></p>
        <p><strong>Montant :</strong> <?= htmlspecialchars(
            $reservation["prix"]
        ) ?> €</p>

        <form method="POST" action="">
            <div class="form-group">
                <label for="nom">Nom sur la carte :</label>
                <input type="text" id="nom" name="nom" placeholder="Jean Dupont"
                       pattern="[A-Za-zÀ-ÿ '-]{2,50}"
                       title="Le nom ne doit contenir que des lettres, espaces ou tirets"
                       required>
            </div>

            <div class="form-group">
                <label for="numero">Numéro de carte :</label>
                <input type="text" id="numero" name="numero" placeholder="1234567812345678"
                       pattern="\d{16}" maxlength="16" inputmode="numeric"
                       oninput="this.value = this.value.replace(/\D/g, '')"
                       required>
            </div>

            <div class="form-group">
                <label for="expiration_mois">Date d'expiration :</label>
                <div style="display: flex; gap: 10px;">
                    <select name="expiration_mois" id="expiration_mois" required>
                        <?php
                        $mois = [
                            "01" => "Janvier",
                            "02" => "Février",
                            "03" => "Mars",
                            "04" => "Avril",
                            "05" => "Mai",
                            "06" => "Juin",
                            "07" => "Juillet",
                            "08" => "Août",
                            "09" => "Septembre",
                            "10" => "Octobre",
                            "11" => "Novembre",
                            "12" => "Décembre",
                        ];
                        foreach ($mois as $num => $nom) {
                            echo "<option value=\"$num\">$nom</option>";
                        }
                        ?>
                    </select>

                    <select name="expiration_annee" id="expiration_annee" required>
                        <?php
                        $annee_actuelle = date("Y");
                        for ($i = 0; $i <= 15; $i++) {
                            $annee = $annee_actuelle + $i;
                            echo "<option value=\"$annee\">$annee</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="cvv">CVV :</label>
                <input type="text" id="cvv" name="cvv" placeholder="123"
                       pattern="\d{3,4}" maxlength="4" inputmode="numeric"
                       oninput="this.value = this.value.replace(/\D/g, '')"
                       title="Entrez un code de sécurité de 3 ou 4 chiffres"
                       required>
            </div>

            <div class="form-group">
                <input type="submit" value="Payer maintenant">
            </div>
        </form>
    <?php endif; ?>
</main>

<div style="text-align: center; margin-top: 40px;">
    <a href="historique.php" class="back-btn">⬅ Retour à l'historique</a>
</div>

<?php include "footer.php"; ?>

<!-- JS : validation nom + expiration -->
<script>
document.getElementById("nom").addEventListener("input", function () {
    this.value = this.value.replace(/[^A-Za-zÀ-ÿ '-]/g, '');
});

document.querySelector("form").addEventListener("submit", function (e) {
    const mois = parseInt(document.getElementById("expiration_mois").value);
    const annee = parseInt(document.getElementById("expiration_annee").value);
    const now = new Date();
    const currentMonth = now.getMonth() + 1;
    const currentYear = now.getFullYear();

    if (annee < currentYear || (annee === currentYear && mois < currentMonth)) {
        alert("❌ La carte est expirée. Veuillez corriger la date.");
        e.preventDefault();
    }
});
</script>