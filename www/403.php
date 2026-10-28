<?php
session_start();
http_response_code(403); // Zet de HTTP status code naar 403
require 'header.php';
?>
<main class="error-page">
    <h1 class="error-code">403</h1>
    <h2>Toegang geweigerd</h2>
    <p>Je hebt geen toestemming om deze pagina te bekijken.</p>
    <p>
        <?php if (isset($_SESSION['user_id'])): ?>
            Je bent ingelogd als <strong><?php echo htmlspecialchars($_SESSION['email'] ?? 'gebruiker'); ?></strong>
            <br>maar je hebt niet de juiste rechten voor deze pagina.
        <?php else: ?>
            Je bent niet ingelogd.
        <?php endif; ?>
    </p>
    <div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="index.php" class="btn">Terug naar de fietsen</a>
        <?php else: ?>
            <a href="login.php" class="btn btn-orange">Inloggen</a>
        <?php endif; ?>
    </div>
</main>
<?php require 'footer.php'; ?>
