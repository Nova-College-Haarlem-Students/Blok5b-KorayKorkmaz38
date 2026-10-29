<?php
require 'medewerker_check.php';
require 'database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: 404.php');
    exit;
}

$id = (int) $_GET['id'];

try {
    $stmt = $conn->prepare("SELECT reserveringen.*, gebruikers.naam AS klant_naam, fietsen.merk, fiets_types.naam AS type_naam
                            FROM reserveringen
                            LEFT JOIN gebruikers ON reserveringen.gebruiker_id = gebruikers.id
                            LEFT JOIN fietsen ON reserveringen.fiets_id = fietsen.id
                            LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
                            WHERE reserveringen.id = :id");
    $stmt->execute(['id' => $id]);
    $reservering = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log de error (niet tonen aan gebruiker!)
    error_log("Database error: " . $e->getMessage());
    header('Location: 500.php');
    exit;
}

if (!$reservering) {
    header('Location: 404.php');
    exit;
}

require 'header.php';
?>

<main>
    <div class="container">
        <form method="POST" action="reservering_update_process.php" class="login-form">
            <h1>Reservering <?php echo htmlspecialchars($reservering['id']) ?></h1>
            <ul class="specs">
                <li><strong>Klant</strong> <?php echo htmlspecialchars($reservering['klant_naam']) ?></li>
                <li><strong>Fiets</strong> <?php echo htmlspecialchars($reservering['type_naam'] . ' ' . $reservering['merk']) ?></li>
                <li><strong>Ophalen</strong> <?php echo date('d-m-Y', strtotime($reservering['ophaaldatum'])) ?></li>
                <li><strong>Terugbrengen</strong> <?php echo date('d-m-Y', strtotime($reservering['terugbrengdatum'])) ?></li>
                <li><strong>Totaalprijs</strong> € <?php echo number_format($reservering['totaalprijs'], 2, ',', '') ?></li>
            </ul>

            <!-- Stuur het id mee als verborgen veld -->
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($reservering['id']) ?>">

            <!-- inleverdatum en opmerking mogen leeg blijven (NULL in de database) -->
            <div>
                <label for="inleverdatum">Inleverdatum (leeg = nog niet ingeleverd)</label>
                <input type="date" id="inleverdatum" name="inleverdatum" value="<?php echo htmlspecialchars($reservering['inleverdatum'] ?? '') ?>">
            </div>
            <div>
                <label for="opmerking">Opmerking</label>
                <textarea id="opmerking" name="opmerking" rows="4" maxlength="1000" placeholder="bijv. lekke band"><?php echo htmlspecialchars($reservering['opmerking'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-orange">Opslaan</button>
            <p><a href="reserveringen_index.php">Terug naar reserveringen</a></p>
        </form>
    </div>
</main>
<?php require 'footer.php' ?>
