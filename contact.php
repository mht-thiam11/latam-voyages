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
    <h2 style="text-align:center; margin-bottom: 40px;">Contactez nos fondateurs</h2>

    <div class="founders-grid">
        <!-- Fondateur 1 -->
        <div class="founder-card">
            <img src="images/star.jpg" alt="Fondateur 1">
            <h3>Erick Jana</h3>
            <p class="role">Co-fondateur & PDG</p>
            <p class="bio">Erick est passionné par le développement web et a dirigé la création de la plateforme LATAM Voyages. Il veille à l’innovation et à la qualité technique du site.</p>
            <p class="email"><strong>Email :</strong> erick.jana@latamvoyages.fr</p>
        </div>

        <!-- Fondateur 2 -->
        <div class="founder-card">
            <img src="images/thiam.jpeg" alt="Fondateur 2">
            <h3>Mohamet Thiam</h3>
            <p class="role">Co-fondateur & Directeur des opérations</p>
            <p class="bio">Mohamet est expert en tourisme latino-américain. Il est responsable des destinations proposées et de la relation client.</p>
            <p class="email"><strong>Email :</strong> mohamet.thiam@latamvoyages.fr</p>
        </div>
    </div>

    <div style="text-align: center; margin-top: 40px;">
        <a href="bienvenue.php" class="back-btn">⬅ Retour à l'accueil</a>
    </div>
</main>
<section class="remerciement-section">
    <p>🙏 Un immense merci à <strong>Thibault Anani</strong>, sans qui rien de tout cela n’aurait été possible.</p>
</section>
<?php include "footer.php"; ?>
