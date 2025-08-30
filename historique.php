<?php
session_start();
if (!isset($_SESSION["id_client"])) {
    header("Location: connexion.php");
    exit();
}

include "config.php";
$id_client = $_SESSION["id_client"];

// Fonction pour vérifier si un paiement a été effectué
function paiementEffectue($id_voyage, $conn): bool
{
    $query = "SELECT 1 FROM Paiements p
              JOIN Reservations r ON r.id_reservation = p.id_reservation
              WHERE r.id_voyage = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_voyage);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

$sql = "SELECT v.id_voyage, d.nom AS destination, v.date_depart, v.date_retour, v.prix, r.statut, r.date_reservation
        FROM Reservations r
        JOIN Voyages v ON r.id_voyage = v.id_voyage
        JOIN Destinations d ON v.id_destination = d.id_destination
        WHERE r.id_client = ?
        ORDER BY r.date_reservation DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_client);
$stmt->execute();
$result = $stmt->get_result();
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

<main class="container historique-page glass-container">
  <h1 class="vitrine-title">Mes réservations</h1>
    <?php if ($result->num_rows > 0): ?>
      <div class="table-container">
        <table class="styled-table">
          <thead>
          <tr>
            <th>Destination</th>
            <th>Date réservation</th>
            <th>Date départ</th>
            <th>Date retour</th>
            <th>Prix</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
          </thead>
          <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
              <?php
              $est_paye =
                  $row["statut"] === "Confirmée" &&
                  paiementEffectue($row["id_voyage"], $conn);
              $statut_affiche = $est_paye ? "Payée" : $row["statut"];
              $classe_badge = match ($row["statut"]) {
                  "Annulée" => "badge-danger",
                  "Confirmée" => $est_paye ? "badge-success" : "badge-success",
                  default => "badge-warning",
              };
              ?>
            <tr>
              <td><?= htmlspecialchars($row["destination"]) ?></td>
              <td><?= htmlspecialchars($row["date_reservation"]) ?></td>
              <td><?= htmlspecialchars($row["date_depart"]) ?></td>
              <td><?= htmlspecialchars($row["date_retour"]) ?></td>
              <td><?= htmlspecialchars($row["prix"]) ?> €</td>
              <td>
                <span class="<?= $classe_badge ?>"><?= $statut_affiche ?></span>
              </td>
              <td>
                  <?php if ($row["statut"] === "En attente"): ?>
                    <form method="POST" action="annuler_reservation.php" style="display:inline;">
                      <input type="hidden" name="id_voyage" value="<?= $row[
                          "id_voyage"
                      ] ?>">
                      <button type="submit" class="btn danger">Annuler</button>
                    </form>
                    <a href="modifier_reservation.php?id=<?= $row[
                        "id_voyage"
                    ] ?>" class="btn secondary">Modifier</a>
                    <a href="paiement.php?id=<?= $row[
                        "id_voyage"
                    ] ?>" class="btn">Payer</a>
                  <?php else: ?>
                    -
                  <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="message">Vous n'avez encore effectué aucune réservation.</p>
    <?php endif; ?>
</main>

<div style="text-align: center; margin-top: 40px;">
  <a href="bienvenue.php" class="back-btn">Retour à l'accueil</a>
</div>

<?php include "footer.php"; ?>

<?php $conn->close(); ?>
