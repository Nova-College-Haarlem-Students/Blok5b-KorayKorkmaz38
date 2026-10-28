<?php
// Overzicht van alle beschikbare fietsen, met filter (type + framemaat) en sortering op dagprijs
require_once __DIR__ . '/includes/database.php';

// Gekozen filters uit de URL
$gekozenType = $_GET['type'] ?? '';
$gekozenFramemaat = $_GET['framemaat'] ?? '';
$gekozenSortering = $_GET['sorteer'] ?? '';

// Type moet een id zijn, anders negeren we het filter
if ($gekozenType !== '' && !is_numeric($gekozenType)) {
    $gekozenType = '';
}

// Alleen deze twee sorteringen zijn toegestaan (whitelist), want ORDER BY kan geen placeholder gebruiken
$sorteringen = [
    'prijs_op' => 'f.dagprijs ASC',
    'prijs_af' => 'f.dagprijs DESC',
];
$orderBy = $sorteringen[$gekozenSortering] ?? 't.naam ASC, f.merk ASC';

try {
    // Keuzelijst types: alleen types waar beschikbare fietsen van zijn, zonder dubbele waardes
    $stmt = $pdo->prepare('
        SELECT DISTINCT t.id, t.naam
        FROM fietsen f
        LEFT JOIN fiets_types t ON t.id = f.fiets_type_id
        WHERE f.in_verhuur = 1
        ORDER BY t.naam
    ');
    $stmt->execute();
    $types = $stmt->fetchAll();

    // Keuzelijst framematen, zonder dubbele waardes
    $stmt = $pdo->prepare('
        SELECT DISTINCT framemaat
        FROM fietsen
        WHERE in_verhuur = 1
        ORDER BY framemaat
    ');
    $stmt->execute();
    $framematen = $stmt->fetchAll();

    // De fietsen zelf. Filters worden alleen toegevoegd als ze gekozen zijn.
    $sql = '
        SELECT f.id, f.merk, f.framemaat, f.foto, f.dagprijs, t.naam AS type
        FROM fietsen f
        LEFT JOIN fiets_types t ON t.id = f.fiets_type_id
        WHERE f.in_verhuur = 1
    ';
    $parameters = [];

    if ($gekozenType !== '') {
        $sql .= ' AND f.fiets_type_id = :type';
        $parameters[':type'] = $gekozenType;
    }
    if ($gekozenFramemaat !== '') {
        $sql .= ' AND f.framemaat = :framemaat';
        $parameters[':framemaat'] = $gekozenFramemaat;
    }
    $sql .= ' ORDER BY ' . $orderBy;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($parameters);
    $fietsen = $stmt->fetchAll();
} catch (PDOException $e) {
    require __DIR__ . '/errors/500.php';
    exit;
}

$paginaTitel = 'Onze fietsen';
require __DIR__ . '/includes/header.php';
?>

<section class="intro">
    <h1>Huur een fiets bij Trapkracht</h1>
    <p>Naar het strand of door de duinen: kies je fiets en reserveer voor een of meer dagen.</p>
</section>

<form method="get" action="/index.php" class="filter">
    <label>
        Type
        <select name="type">
            <option value="">Alle types</option>
            <?php foreach ($types as $type): ?>
                <option value="<?= htmlspecialchars($type['id']) ?>"
                    <?= (string)$type['id'] === $gekozenType ? 'selected' : '' ?>>
                    <?= htmlspecialchars($type['naam']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Framemaat
        <select name="framemaat">
            <option value="">Alle maten</option>
            <?php foreach ($framematen as $maat): ?>
                <option value="<?= htmlspecialchars($maat['framemaat']) ?>"
                    <?= $maat['framemaat'] === $gekozenFramemaat ? 'selected' : '' ?>>
                    <?= htmlspecialchars($maat['framemaat']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Sorteren
        <select name="sorteer">
            <option value="">Standaard</option>
            <option value="prijs_op" <?= $gekozenSortering === 'prijs_op' ? 'selected' : '' ?>>Dagprijs laag - hoog</option>
            <option value="prijs_af" <?= $gekozenSortering === 'prijs_af' ? 'selected' : '' ?>>Dagprijs hoog - laag</option>
        </select>
    </label>

    <div class="filter-knoppen">
        <button type="submit" class="knop">Toon fietsen</button>
        <a href="/index.php" class="knop knop-licht">Wis filters</a>
    </div>
</form>

<p class="aantal"><?= htmlspecialchars(count($fietsen)) ?> fiets(en) gevonden</p>

<?php if (count($fietsen) === 0): ?>
    <p class="leeg">Er zijn geen fietsen die aan je filters voldoen.</p>
<?php else: ?>
    <div class="fietsen-grid">
        <?php foreach ($fietsen as $fiets): ?>
            <a href="/fiets.php?id=<?= htmlspecialchars($fiets['id']) ?>" class="fiets-kaart">
                <?php if ($fiets['foto'] !== null && file_exists(__DIR__ . '/' . $fiets['foto'])): ?>
                    <img src="/<?= htmlspecialchars($fiets['foto']) ?>" alt="<?= htmlspecialchars($fiets['merk']) ?>">
                <?php else: ?>
                    <div class="foto-placeholder"><?= htmlspecialchars($fiets['type']) ?></div>
                <?php endif; ?>
                <div class="fiets-info">
                    <span class="label"><?= htmlspecialchars($fiets['type']) ?></span>
                    <h2><?= htmlspecialchars($fiets['merk']) ?></h2>
                    <p>Framemaat <?= htmlspecialchars($fiets['framemaat']) ?></p>
                    <p class="prijs">&euro; <?= htmlspecialchars(number_format($fiets['dagprijs'], 2, ',', '.')) ?> <small>per dag</small></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
