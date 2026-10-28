<?php
http_response_code(500);
$paginaTitel = 'Er ging iets mis';
require __DIR__ . '/../includes/header.php';
?>
<section class="foutpagina">
    <h1>500</h1>
    <p>Er ging iets mis aan onze kant. Probeer het later opnieuw.</p>
    <a href="/index.php" class="knop">Terug naar alle fietsen</a>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
