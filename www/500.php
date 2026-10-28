<?php
session_start();
http_response_code(500);
require 'header.php';
?>
<main class="error-page">
    <h1 class="error-code">500</h1>
    <h2>Server Error</h2>
    <p>Er is iets fout gegaan op de server.</p>
    <p>Probeer het later opnieuw.</p>
    <div>
        <a href="index.php" class="btn">Terug naar de fietsen</a>
    </div>
</main>
<?php require 'footer.php'; ?>
