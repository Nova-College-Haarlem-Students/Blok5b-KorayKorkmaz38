<?php
require 'admin_check.php';
require 'database.php';

try {
    // per type: aantal fietsen en de gemiddelde, laagste en hoogste dagprijs (COUNT, AVG, MIN, MAX + GROUP BY)
    $stmt = $conn->prepare("SELECT fiets_types.naam, COUNT(fietsen.id) AS aantal,
                                   AVG(fietsen.dagprijs) AS gemiddeld, MIN(fietsen.dagprijs) AS laagste, MAX(fietsen.dagprijs) AS hoogste
                            FROM fiets_types
                            LEFT JOIN fietsen ON fietsen.fiets_type_id = fiets_types.id
                            GROUP BY fiets_types.id, fiets_types.naam
                            ORDER BY fiets_types.naam");
    $stmt->execute();
    $per_type = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // omzet per type: alle totaalprijzen bij elkaar opgeteld (SUM + GROUP BY)
    $stmt = $conn->prepare("SELECT fiets_types.naam, COUNT(reserveringen.id) AS verhuurd, SUM(reserveringen.totaalprijs) AS omzet
                            FROM fiets_types
                            LEFT JOIN fietsen ON fietsen.fiets_type_id = fiets_types.id
                            LEFT JOIN reserveringen ON reserveringen.fiets_id = fietsen.id
                            GROUP BY fiets_types.id, fiets_types.naam
                            ORDER BY omzet DESC");
    $stmt->execute();
    $omzet = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // types die meer dan vijf keer verhuurd zijn
    // HAVING in plaats van WHERE, omdat je filtert op de uitkomst van COUNT (na GROUP BY)
    $stmt = $conn->prepare("SELECT fiets_types.naam, COUNT(reserveringen.id) AS verhuurd
                            FROM reserveringen
                            LEFT JOIN fietsen ON reserveringen.fiets_id = fietsen.id
                            LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
                            GROUP BY fiets_types.id, fiets_types.naam
                            HAVING COUNT(reserveringen.id) > 5");
    $stmt->execute();
    $populair = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log de error (niet tonen aan gebruiker!)
    error_log("Database error: " . $e->getMessage());
    header('Location: 500.php');
    exit;
}

require 'header.php';
?>
<main>
    <div class="container">
        <h1 class="section-title">Statistieken</h1>

        <h2>Fietsen per type</h2>
        <div class="tabel-wrapper">
        <table class="tabel">
            <tr>
                <th>Type</th>
                <th>Aantal</th>
                <th>Gemiddeld</th>
                <th>Laagste</th>
                <th>Hoogste</th>
            </tr>
            <?php foreach ($per_type as $type) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($type['naam']) ?></td>
                    <td><?php echo htmlspecialchars($type['aantal']) ?></td>
                    <td>€ <?php echo number_format($type['gemiddeld'] ?? 0, 2, ',', '') ?></td>
                    <td>€ <?php echo number_format($type['laagste'] ?? 0, 2, ',', '') ?></td>
                    <td>€ <?php echo number_format($type['hoogste'] ?? 0, 2, ',', '') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        </div>

        <h2>Omzet per type</h2>
        <div class="tabel-wrapper">
        <table class="tabel">
            <tr>
                <th>Type</th>
                <th>Keer verhuurd</th>
                <th>Omzet</th>
            </tr>
            <?php foreach ($omzet as $type) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($type['naam']) ?></td>
                    <td><?php echo htmlspecialchars($type['verhuurd']) ?></td>
                    <!-- SUM geeft NULL als een type nog nooit verhuurd is, dan tonen we 0 -->
                    <td>€ <?php echo number_format($type['omzet'] ?? 0, 2, ',', '') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        </div>

        <h2>Meer dan vijf keer verhuurd</h2>
        <ul>
            <?php foreach ($populair as $type) : ?>
                <li><?php echo htmlspecialchars($type['naam']) ?>: <?php echo htmlspecialchars($type['verhuurd']) ?> keer</li>
            <?php endforeach; ?>
        </ul>
    </div>
</main>
<?php require 'footer.php' ?>
