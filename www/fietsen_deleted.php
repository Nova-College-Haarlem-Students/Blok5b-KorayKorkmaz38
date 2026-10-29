<?php
require 'medewerker_check.php';
require 'database.php';

try {
    // fietsen die uit de verhuur zijn gehaald (soft deleted)
    $stmt = $conn->prepare("SELECT fietsen.*, fiets_types.naam AS type_naam
                            FROM fietsen
                            LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
                            WHERE fietsen.deleted_at IS NOT NULL
                            ORDER BY fietsen.deleted_at DESC");
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
        <div class="beheer-top">
            <h1 class="section-title">Fietsen uit de verhuur <span>(<?php echo count($fietsen) ?>)</span></h1>
            <div>
                <a href="fietsen_index.php" class="btn btn-light">Terug naar fietsen</a>
            </div>
        </div>

        <?php if (empty($fietsen)) : ?>
            <p class="empty">Alle fietsen zijn in de verhuur.</p>
        <?php else : ?>
            <div class="tabel-wrapper">
                <table class="tabel">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Merk</th>
                            <th>Framemaat</th>
                            <th>Dagprijs</th>
                            <th>Uit verhuur sinds</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fietsen as $fiets) : ?>
                            <tr>
                                <td><span class="badge"><?php echo htmlspecialchars($fiets['type_naam']) ?></span></td>
                                <td><strong><?php echo htmlspecialchars($fiets['merk']) ?></strong></td>
                                <td><?php echo htmlspecialchars($fiets['framemaat']) ?></td>
                                <td>€ <?php echo number_format($fiets['dagprijs'], 2, ',', '') ?></td>
                                <td><?php echo date('d-m-Y H:i', strtotime($fiets['deleted_at'])) ?></td>
                                <td class="acties">
                                    <form action="fietsen_deleted_restore.php" method="post">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($fiets['id']) ?>">
                                        <button type="submit" class="btn btn-orange btn-klein">Terugzetten</button>
                                    </form>
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
