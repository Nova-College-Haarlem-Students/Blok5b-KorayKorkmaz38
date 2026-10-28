<?php
session_start();
http_response_code(404); // Zet de HTTP status code naar 404
require 'header.php';
?>
<main class="error-page">
    <h1 class="error-code">404</h1>
    <h2>Pagina Niet Gevonden</h2>
    <p>De pagina of fiets die je zoekt bestaat niet (meer).</p>
    <div>
        <a href="index.php" class="btn">Terug naar de fietsen</a>
    </div>
</main>
<?php require 'footer.php'; ?>
