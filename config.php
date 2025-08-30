<?php
$servername = "127.0.0.1"; // Utilisez 127.0.0.1 pour éviter les problèmes de résolution de 'localhost'
$username = "root";
$password = "";
$dbname = "AgenceVoyage";

// Connexion sans spécifier de socket
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifiez la connexion
/*if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
} else {
    echo "Connexion réussie !";
}
?>
*/
