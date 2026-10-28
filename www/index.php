<?php
session_start();
require 'database.php';

// Filteren en sorteren met GET parameters: type, framemaat en sorteer
$type      = $_GET['type'] ?? '';
$framemaat = $_GET['framemaat'] ?? '';
$sorteer   = $_GET['sorteer'] ?? '';

// type is een id, dus moet een getal zijn
if ($type !== '' && !is_numeric($type)) {
    $type = '';
}

try {
    // alleen fietsen die in de verhuur zijn, met de naam van het type erbij
    $sql = "SELECT fietsen.*, fiets_types.naam AS type_naam
            FROM fietsen
            LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
            WHERE fietsen.in_verhuur = 1";
    $params = [];

    if ($type !== '') {
        $sql .= " AND fietsen.fiets_type_id = :type";
        $params['type'] = $type;
    }
    if ($framemaat !== '') {
        $sql .= " AND fietsen.framemaat = :framemaat";
        $params['framemaat'] = $framemaat;
    }

    // sorteren op dagprijs: laag-hoog of hoog-laag
    if ($sorteer === 'laag') {
        $sql .= " ORDER BY fietsen.dagprijs ASC";
    } elseif ($sorteer === 'hoog') {
        $sql .= " ORDER BY fietsen.dagprijs DESC";
    } else {
        $sql .= " ORDER BY fiets_types.naam, fietsen.merk";
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $fietsen = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // types voor de dropdown
    $stmt = $conn->prepare("SELECT DISTINCT fiets_types.id, fiets_types.naam
                            FROM fietsen
                            LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
                            WHERE fietsen.in_verhuur = 1
                            ORDER BY fiets_types.naam");
    $stmt->execute();
    $types = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // framematen voor de dropdown
    $stmt = $conn->prepare("SELECT DISTINCT framemaat FROM fietsen WHERE in_verhuur = 1 ORDER BY framemaat");
    $stmt->execute();
    $framematen = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    // Log de error (niet tonen aan gebruiker!)
    error_log("Database error: " . $e->getMessage());
    header('Location: 500.php');
    exit;
}

require 'header.php';
?>
<main>
    <section class="hero">
        <div class="hero-content">
            <h1>Fiets naar het strand en de duinen</h1>
            <p>Stadsfietsen, e-bikes, tandems en bakfietsen. Huur je fiets vlak bij het station, voor een of meer dagen.</p>
            <a href="#fietsen" class="btn btn-orange">Bekijk alle fietsen</a>
        </div>
    </section>

    <div class="container">
        <form action="index.php#fietsen" method="get" class="filter">
            <div>
                <label for="type">Type:</label>
                <select name="type" id="type">
                    <option value="">Alle types</option>
                    <?php foreach ($types as $t) : ?>
                        <option value="<?php echo htmlspecialchars($t['id']) ?>" <?php echo $t['id'] == $type ? 'selected' : '' ?>><?php echo htmlspecialchars($t['naam']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="framemaat">Framemaat:</label>
                <select name="framemaat" id="framemaat">
                    <option value="">Alle framematen</option>
                    <?php foreach ($framematen as $maat) : ?>
                        <option value="<?php echo htmlspecialchars($maat) ?>" <?php echo $maat === $framemaat ? 'selected' : '' ?>><?php echo htmlspecialchars($maat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="sorteer">Sorteren:</label>
                <select name="sorteer" id="sorteer">
                    <option value="">Standaard</option>
                    <option value="laag" <?php echo $sorteer === 'laag' ? 'selected' : '' ?>>Dagprijs laag - hoog</option>
                    <option value="hoog" <?php echo $sorteer === 'hoog' ? 'selected' : '' ?>>Dagprijs hoog - laag</option>
                </select>
            </div>
            <button type="submit" class="btn">Zoek</button>
            <a href="index.php" class="btn btn-light">Wis filters</a>
        </form>
    </div>

    <div class="container" id="fietsen">
        <h2 class="section-title">Onze fietsen <span>(<?php echo count($fietsen) ?>)</span></h2>
        <?php if (empty($fietsen)) : ?>
            <p class="empty">Geen fietsen gevonden. Probeer een ander filter.</p>
        <?php endif; ?>
        <div class="products">
            <?php foreach ($fietsen as $fiets) : ?>
                <div class="product">
                    <img src="<?php echo isset($fiets['foto']) ? 'images/' . htmlspecialchars($fiets['foto']) : 'https://placehold.co/200' ?>" alt="<?php echo htmlspecialchars($fiets['merk']) ?>">
                    <div class="product-info">
                        <span class="badge"><?php echo htmlspecialchars($fiets['type_naam']) ?></span>
                        <h3><?php echo htmlspecialchars($fiets['merk']) ?></h3>
                        <p>Framemaat <?php echo htmlspecialchars($fiets['framemaat']) ?></p>
                        <div class="product-bottom">
                            <p class="price">€ <?php echo number_format($fiets['dagprijs'], 2, ',', '') ?> <span>/ dag</span></p>
                            <a href="fietsen_detail.php?id=<?php echo htmlspecialchars($fiets['id']) ?>" class="btn">Bekijk</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>
<?php require 'footer.php' ?>
