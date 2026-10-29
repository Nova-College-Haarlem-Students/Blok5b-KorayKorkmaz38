<?php
require 'admin_check.php';
require 'database.php';

try {
    // laagste, hoogste en gemiddelde dagprijs van alle fietsen (kolomfuncties)
    $stmt = $conn->prepare("SELECT COUNT(id) AS aantal, AVG(dagprijs) AS gemiddeld, MIN(dagprijs) AS laagste, MAX(dagprijs) AS hoogste
                            FROM fietsen");
    $stmt->execute();
    $totaal = $stmt->fetch(PDO::FETCH_ASSOC);

    // aantal fietsen en dagprijzen per type (GROUP BY)
    // LEFT JOIN vanaf fiets_types: een type zonder fietsen komt er ook in, met aantal 0
    $stmt = $conn->prepare("SELECT fiets_types.naam, COUNT(fietsen.id) AS aantal,
                                   AVG(fietsen.dagprijs) AS gemiddeld, MIN(fietsen.dagprijs) AS laagste, MAX(fietsen.dagprijs) AS hoogste
                            FROM fiets_types
                            LEFT JOIN fietsen ON fietsen.fiets_type_id = fiets_types.id
                            GROUP BY fiets_types.id, fiets_types.naam
                            ORDER BY fiets_types.naam");
    $stmt->execute();
    $per_type = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // omzet per type: alle totaalprijzen van de reserveringen bij elkaar (SUM)
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
                            HAVING COUNT(reserveringen.id) > 5
                            ORDER BY verhuurd DESC");
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

        <div class="stats">
            <div class="stat">
                <span class="stat-getal"><?php echo htmlspecialchars($totaal['aantal']) ?></span>
                <span>Fietsen in totaal</span>
            </div>
            <div class="stat">
                <span class="stat-getal">€ <?php echo number_format($totaal['gemiddeld'] ?? 0, 2, ',', '') ?></span>
                <span>Gemiddelde dagprijs</span>
            </div>
            <div class="stat">
                <span class="stat-getal">€ <?php echo number_format($totaal['laagste'] ?? 0, 2, ',', '') ?></span>
                <span>Laagste dagprijs</span>
            </div>
            <div class="stat">
                <span class="stat-getal">€ <?php echo number_format($totaal['hoogste'] ?? 0, 2, ',', '') ?></span>
                <span>Hoogste dagprijs</span>
            </div>
        </div>

        <h2>Fietsen per type</h2>
        <div class="tabel-wrapper">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Aantal fietsen</th>
                        <th>Gemiddelde dagprijs</th>
                        <th>Laagste dagprijs</th>
                        <th>Hoogste dagprijs</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($per_type as $type) : ?>
                        <tr>
                            <td><span class="badge"><?php echo htmlspecialchars($type['naam']) ?></span></td>
                            <td><?php echo htmlspecialchars($type['aantal']) ?></td>
                            <td>€ <?php echo number_format($type['gemiddeld'] ?? 0, 2, ',', '') ?></td>
                            <td>€ <?php echo number_format($type['laagste'] ?? 0, 2, ',', '') ?></td>
                            <td>€ <?php echo number_format($type['hoogste'] ?? 0, 2, ',', '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h2>Omzet per type</h2>
        <div class="tabel-wrapper">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Keer verhuurd</th>
                        <th>Omzet</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($omzet as $type) : ?>
                        <tr>
                            <td><span class="badge"><?php echo htmlspecialchars($type['naam']) ?></span></td>
                            <td><?php echo htmlspecialchars($type['verhuurd']) ?></td>
                            <!-- SUM geeft NULL als een type nog nooit verhuurd is, dan tonen we 0 -->
                            <td>€ <?php echo number_format($type['omzet'] ?? 0, 2, ',', '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h2>Types die meer dan vijf keer verhuurd zijn</h2>
        <?php if (empty($populair)) : ?>
            <p class="empty">Geen enkel type is meer dan vijf keer verhuurd.</p>
        <?php else : ?>
            <div class="tabel-wrapper">
                <table class="tabel">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Keer verhuurd</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($populair as $type) : ?>
                            <tr>
                                <td><span class="badge"><?php echo htmlspecialchars($type['naam']) ?></span></td>
                                <td><?php echo htmlspecialchars($type['verhuurd']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require 'footer.php' ?>
