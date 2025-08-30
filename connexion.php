<?php
session_start();
if (isset($_SESSION["id_client"])) {
    header("Location: bienvenue.php");
    exit();
}
include "config.php"; // Connexion à la base

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $mot_de_passe = $_POST["mot_de_passe"];

    $sql = $conn->prepare(
        "SELECT id_client, nom, prenom, mot_de_passe, role FROM Clients WHERE email = ?"
    );
    $sql->bind_param("s", $email);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($mot_de_passe, $user["mot_de_passe"])) {
            $_SESSION["id_client"] = $user["id_client"];
            $_SESSION["nom"] = $user["nom"];
            $_SESSION["prenom"] = $user["prenom"];
            $_SESSION["role"] = $user["role"];

            header("Location: bienvenue.php");
            exit();
        } else {
            $message = "❌ Mot de passe incorrect.";
        }
    } else {
        $message = "❌ Aucun compte trouvé avec cet email.";
    }

    $sql->close();
    $conn->close();
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

<main class="login-page container login glass-container">
    <div class="container login glass-container">
        <h2>Connexion</h2>

        <?php if (!empty($message)) {
            echo "<p class='message'>$message</p>";
        } ?>

        <form method="POST" action="connexion.php">
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>

            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Mot de passe" required>
            </div>

            <div class="form-group">
                <input type="submit" value="Se connecter">
            </div>
        </form>

        <div class="login-link">
            <p>Pas encore de compte ? <a href="inscription.php">Inscrivez-vous ici</a></p>
        </div>
    </div>
    </main>
<a href="bienvenue.php" class="back-btn">Retour à l'accueil</a>
<?php include "footer.php"; ?>
