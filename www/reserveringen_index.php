<?php
require 'medewerker_check.php';
require 'database.php';

try {
    // alle reserveringen, met de klant en de fiets erbij (LEFT JOIN)
    $stmt = $conn->prepare("SELECT reserveringen.*, gebruikers.naam AS klant_naam, gebruikers.email AS klant_email,
                                   fietsen.merk, fiets_types.naam AS type_naam
                            FROM reserveringen
                            LEFT JOIN gebruikers ON reserveringen.gebruiker_id = gebruikers.id
                            LEFT JOIN fietsen ON reserveringen.fiets_id = fietsen.id
                            LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
                            ORDER BY reserveringen.ophaaldatum DESC");
    $stmt->execute();
    $reserveringen = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h1 class="section-title">Alle reserveringen <span>(<?php echo count($reserveringen) ?>)</span></h1>

        <div class="tabel-wrapper">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>Nr</th>
                        <th>Klant</th>
                        <th>Fiets</th>
                        <th>Ophalen</th>
                        <th>Terugbrengen</th>
                        <th>Ingeleverd</th>
                        <th>Totaalprijs</th>
                        <th>Opmerking</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reserveringen as $reservering) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservering['id']) ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($reservering['klant_naam']) ?></strong><br>
                                <small><?php echo htmlspecialchars($reservering['klant_email']) ?></small>
                            </td>
                            <td>
                                <span class="badge"><?php echo htmlspecialchars($reservering['type_naam']) ?></span>
                                <?php echo htmlspecialchars($reservering['merk']) ?>
                            </td>
                            <td><?php echo date('d-m-Y', strtotime($reservering['ophaaldatum'])) ?></td>
                            <td><?php echo date('d-m-Y', strtotime($reservering['terugbrengdatum'])) ?></td>
                            <td>
                                <?php if ($reservering['inleverdatum'] !== null) : ?>
                                    <span class="status status-klaar"><?php echo date('d-m-Y', strtotime($reservering['inleverdatum'])) ?></span>
                                <?php else : ?>
                                    <span class="status status-gepland">Nog niet</span>
                                <?php endif; ?>
                            </td>
                            <td>€ <?php echo number_format($reservering['totaalprijs'], 2, ',', '') ?></td>
                            <td class="opmerking"><?php echo htmlspecialchars($reservering['opmerking'] ?? '-') ?></td>
                            <td>
                                <a href="reservering_update.php?id=<?php echo htmlspecialchars($reservering['id']) ?>" class="btn btn-klein">Bijwerken</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require 'footer.php' ?>
