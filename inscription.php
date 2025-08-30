<?php
session_start();
include "config.php";

$message = "";

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $mot_de_passe = $_POST["mot_de_passe"];
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $telephone = trim($_POST["telephone"]);
    $adresse = trim($_POST["adresse"]);

    // Vérifications côté PHP
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Email invalide.";
    } elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s'-]{2,50}$/u", $nom)) {
        $message = "❌ Nom invalide. Lettres uniquement (2 à 50 caractères).";
    } elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s'-]{2,50}$/u", $prenom)) {
        $message =
            "❌ Prénom invalide. Lettres uniquement (2 à 50 caractères).";
    } elseif (
        !empty($telephone) &&
        !preg_match("/^\+?\d{10,15}$/", $telephone)
    ) {
        $message =
            "❌ Numéro de téléphone invalide. Il doit contenir entre 10 et 15 chiffres et peut commencer par +.";
    } else {
        // Vérification email en base
        $check_email = $conn->prepare(
            "SELECT id_client FROM Clients WHERE email = ?"
        );
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();

        if ($check_email->num_rows > 0) {
            $message = "❌ Cet email est déjà utilisé.";
        } else {
            $mot_de_passe_hache = password_hash(
                $mot_de_passe,
                PASSWORD_DEFAULT
            );
            $sql = $conn->prepare("INSERT INTO Clients (email, mot_de_passe, nom, prenom, telephone, adresse, role) 
                                   VALUES (?, ?, ?, ?, ?, ?, 'client')");
            $sql->bind_param(
                "ssssss",
                $email,
                $mot_de_passe_hache,
                $nom,
                $prenom,
                $telephone,
                $adresse
            );

            if ($sql->execute()) {
                $message =
                    "✅ Inscription réussie ! <a href='connexion.php'>Connectez-vous ici</a>.";
            } else {
                $message = "❌ Erreur lors de l'inscription : " . $conn->error;
            }
            $sql->close();
        }
        $check_email->close();
    }

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

<div class="login-page container login glass-container">
    <div class="container login">
        <h2>Inscription</h2>

        <?php if (!empty($message)): ?>
            <p class="message"><?= $message ?></p>
        <?php endif; ?>

        <form method="POST" action="inscription.php">
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            </div>
            
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom"
                       pattern="[a-zA-ZÀ-ÿ\s'-]{2,50}" 
                       title="Uniquement des lettres et espaces (2 à 50 caractères)" required>
            </div>
            
            <div class="form-group">
                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom"
                       pattern="[a-zA-ZÀ-ÿ\s'-]{2,50}" 
                       title="Uniquement des lettres et espaces (2 à 50 caractères)" required>
            </div>

            <div class="form-group">
                <label for="telephone">Téléphone :</label>
                <input type="text" id="telephone" name="telephone"
                       pattern="^\+?\d{10,15}$" 
                       inputmode="tel"
                       placeholder="+33612345678"
                       title="Numéro entre 10 et 15 chiffres, avec un + possible en début">
            </div>

            <div class="form-group">
                <label for="adresse">Adresse :</label>
                <textarea id="adresse" name="adresse"></textarea>
            </div>

            <div class="form-group">
                <input type="submit" value="S'inscrire">
            </div>
        </form>

        <p class="login-link">Déjà un compte ? <a href="connexion.php">Connectez-vous ici</a></p>
    </div>
</div>

<?php include "footer.php"; ?>
