<?php
require 'login_check.php';
require 'database.php';

try {
    // alleen de reserveringen van de ingelogde klant, met het merk en type van de fiets erbij
    $stmt = $conn->prepare("SELECT reserveringen.*, fietsen.merk, fiets_types.naam AS type_naam
                            FROM reserveringen
                            LEFT JOIN fietsen ON reserveringen.fiets_id = fietsen.id
                            LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
                            WHERE reserveringen.gebruiker_id = :gebruiker_id
                            ORDER BY reserveringen.ophaaldatum DESC");
    $stmt->execute(['gebruiker_id' => $_SESSION['user_id']]);
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
        <h1 class="section-title">Mijn reserveringen <span>(<?php echo count($reserveringen) ?>)</span></h1>

        <?php if (empty($reserveringen)) : ?>
            <p class="empty">Je hebt nog geen reserveringen. <a href="index.php">Bekijk onze fietsen</a></p>
        <?php else : ?>
            <div class="tabel-wrapper">
                <table class="tabel">
                    <thead>
                        <tr>
                            <th>Fiets</th>
                            <th>Ophalen</th>
                            <th>Terugbrengen</th>
                            <th>Totaalprijs</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reserveringen as $reservering) : ?>
                            <tr>
                                <td>
                                    <span class="badge"><?php echo htmlspecialchars($reservering['type_naam']) ?></span>
                                    <strong><?php echo htmlspecialchars($reservering['merk']) ?></strong>
                                </td>
                                <td><?php echo date('d-m-Y', strtotime($reservering['ophaaldatum'])) ?></td>
                                <td><?php echo date('d-m-Y', strtotime($reservering['terugbrengdatum'])) ?></td>
                                <td>€ <?php echo number_format($reservering['totaalprijs'], 2, ',', '') ?></td>
                                <td>
                                    <?php if ($reservering['inleverdatum'] !== null) : ?>
                                        <span class="status status-klaar">Ingeleverd</span>
                                    <?php elseif ($reservering['ophaaldatum'] > date('Y-m-d')) : ?>
                                        <span class="status status-gepland">Gepland</span>
                                    <?php else : ?>
                                        <span class="status status-onderweg">Onderweg</span>
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
