<?php
require 'medewerker_check.php';
require 'database.php';

try {
    // LEFT JOIN houdt ook fietsen ZONDER reservering. Bij die fietsen zijn alle
    // kolommen van reserveringen NULL, dus met WHERE reserveringen.id IS NULL
    // houd je precies de fietsen over die nog nooit verhuurd zijn.
    $stmt = $conn->prepare("SELECT fietsen.*, fiets_types.naam AS type_naam
                            FROM fietsen
                            LEFT JOIN reserveringen ON reserveringen.fiets_id = fietsen.id
                            LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
                            WHERE reserveringen.id IS NULL
                            ORDER BY fiets_types.naam, fietsen.merk");
    $stmt->execute();
    $fietsen = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h1 class="section-title">Nog nooit verhuurd <span>(<?php echo count($fietsen) ?>)</span></h1>

        <?php if (empty($fietsen)) : ?>
            <p class="empty">Elke fiets is minstens één keer verhuurd.</p>
        <?php else : ?>
            <div class="tabel-wrapper">
                <table class="tabel">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Merk</th>
                            <th>Framemaat</th>
                            <th>Dagprijs</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fietsen as $fiets) : ?>
                            <tr>
                                <td><span class="badge"><?php echo htmlspecialchars($fiets['type_naam']) ?></span></td>
                                <td><strong><?php echo htmlspecialchars($fiets['merk']) ?></strong></td>
                                <td><?php echo htmlspecialchars($fiets['framemaat']) ?></td>
                                <td>€ <?php echo number_format($fiets['dagprijs'], 2, ',', '') ?></td>
                                <td>
                                    <?php if ($fiets['deleted_at'] === null) : ?>
                                        <span class="status status-klaar">In de verhuur</span>
                                    <?php else : ?>
                                        <span class="status status-gepland">Uit de verhuur</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require 'footer.php' ?>
