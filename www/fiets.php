<?php
// Detailpagina van één fiets: fiets.php?id=...
require_once __DIR__ . '/includes/database.php';

// Geen id of geen getal: 404
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    require __DIR__ . '/errors/404.php';
    exit;
}

try {
    $stmt = $pdo->prepare('
        SELECT f.id, f.merk, f.framemaat, f.foto, f.dagprijs, f.in_verhuur, t.naam AS type
        FROM fietsen f
        LEFT JOIN fiets_types t ON t.id = f.fiets_type_id
        WHERE f.id = :id
    ');
    $stmt->execute([':id' => $_GET['id']]);
    $fiets = $stmt->fetch();
} catch (PDOException $e) {
    require __DIR__ . '/errors/500.php';
    exit;
}

// Fiets bestaat niet: 404
if ($fiets === false) {
    require __DIR__ . '/errors/404.php';
    exit;
}

$paginaTitel = $fiets['type'] . ' ' . $fiets['merk'];
require __DIR__ . '/includes/header.php';
?>

<a href="/index.php" class="terug">&larr; Alle fietsen</a>

<article class="fiets-detail">
    <?php if ($fiets['foto'] !== null && file_exists(__DIR__ . '/' . $fiets['foto'])): ?>
        <img src="/<?= htmlspecialchars($fiets['foto']) ?>" alt="<?= htmlspecialchars($fiets['merk']) ?>">
    <?php else: ?>
        <div class="foto-placeholder groot"><?= htmlspecialchars($fiets['type']) ?></div>
    <?php endif; ?>

    <div class="fiets-gegevens">
        <span class="label"><?= htmlspecialchars($fiets['type']) ?></span>
        <h1><?= htmlspecialchars($fiets['merk']) ?></h1>

        <dl>
            <dt>Type</dt>
            <dd><?= htmlspecialchars($fiets['type']) ?></dd>
            <dt>Merk</dt>
            <dd><?= htmlspecialchars($fiets['merk']) ?></dd>
            <dt>Framemaat</dt>
            <dd><?= htmlspecialchars($fiets['framemaat']) ?></dd>
        </dl>

        <p class="prijs">&euro; <?= htmlspecialchars(number_format($fiets['dagprijs'], 2, ',', '.')) ?> <small>per dag</small></p>

        <?php if (!$fiets['in_verhuur']): ?>
            <p class="melding">Deze fiets is tijdelijk uit de verhuur.</p>
        <?php endif; ?>
    </div>
</article>

<?php require __DIR__ . '/includes/footer.php'; ?>
