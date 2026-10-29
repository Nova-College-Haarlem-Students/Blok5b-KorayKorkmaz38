<?php
require 'medewerker_check.php';
require 'database.php';

try {
    // alleen fietsen die in de verhuur zijn (niet soft deleted)
    $stmt = $conn->prepare("SELECT fietsen.*, fiets_types.naam AS type_naam
                            FROM fietsen
                            LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
                            WHERE fietsen.deleted_at IS NULL
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
        <div class="beheer-top">
            <h1 class="section-title">Fietsen in de verhuur <span>(<?php echo count($fietsen) ?>)</span></h1>
            <div>
                <a href="fietsen_create.php" class="btn btn-orange">Nieuwe fiets</a>
                <a href="fietsen_deleted.php" class="btn btn-light">Uit de verhuur bekijken</a>
            </div>
        </div>

        <div class="tabel-wrapper">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Type</th>
                        <th>Merk</th>
                        <th>Framemaat</th>
                        <th>Dagprijs</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fietsen as $fiets) : ?>
                        <tr>
                            <td><img class="mini-foto" src="<?php echo isset($fiets['foto']) ? 'images/' . htmlspecialchars($fiets['foto']) : 'https://placehold.co/80' ?>" alt="<?php echo htmlspecialchars($fiets['merk']) ?>"></td>
                            <td><span class="badge"><?php echo htmlspecialchars($fiets['type_naam']) ?></span></td>
                            <td><strong><?php echo htmlspecialchars($fiets['merk']) ?></strong></td>
                            <td><?php echo htmlspecialchars($fiets['framemaat']) ?></td>
                            <td>€ <?php echo number_format($fiets['dagprijs'], 2, ',', '') ?></td>
                            <td class="acties">
                                <a href="fietsen_detail.php?id=<?php echo htmlspecialchars($fiets['id']) ?>" class="btn btn-light btn-klein">Bekijk</a>
                                <a href="fiets_update.php?id=<?php echo htmlspecialchars($fiets['id']) ?>" class="btn btn-klein">Prijs wijzigen</a>
                                <!-- soft delete via POST, de fiets wordt niet echt verwijderd -->
                                <form action="fietsen_delete.php" method="post" onsubmit="return confirm('Weet je zeker dat je deze fiets uit de verhuur wilt halen?')">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($fiets['id']) ?>">
                                    <button type="submit" class="btn btn-rood btn-klein">Uit verhuur</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require 'footer.php' ?>
