<?php
session_start();

if (!isset($_SESSION["id_client"])) {
    header("Location: connexion.php");
    exit();
}

include "config.php";

if (isset($_POST["id_voyage"])) {
    $id_voyage = intval($_POST["id_voyage"]);
    $id_client = $_SESSION["id_client"];

    // Vérifie que la réservation appartient au client
    $sql = "UPDATE Reservations 
            SET statut = 'Annulée' 
            WHERE id_voyage = ? AND id_client = ? AND statut = 'En attente'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_voyage, $id_client);
    $stmt->execute();
    $stmt->close();
}

header("Location: historique.php");
exit();
?>
