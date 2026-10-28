<?php
// $paginaTitel kan per pagina worden ingesteld voordat de header wordt ingeladen
$paginaTitel = $paginaTitel ?? 'Trapkracht';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($paginaTitel) ?> - Trapkracht</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inhoud">
        <a href="/index.php" class="logo">Trapkracht</a>
        <nav>
            <a href="/index.php">Fietsen</a>
        </nav>
    </div>
</header>
<main class="container">
