<?php
http_response_code(404);
$paginaTitel = 'Pagina niet gevonden';
require __DIR__ . '/../includes/header.php';
?>
<section class="foutpagina">
    <h1>404</h1>
    <p>Deze pagina of fiets bestaat niet.</p>
    <a href="/index.php" class="knop">Terug naar alle fietsen</a>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
