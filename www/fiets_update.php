<?php
require 'medewerker_check.php';
require 'database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: 404.php');
    exit;
}

$id = (int) $_GET['id'];

try {
    $stmt = $conn->prepare("SELECT fietsen.*, fiets_types.naam AS type_naam
                            FROM fietsen
                            LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
                            WHERE fietsen.id = :id");
    $stmt->execute(['id' => $id]);
    $fiets = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log de error (niet tonen aan gebruiker!)
    error_log("Database error: " . $e->getMessage());
    header('Location: 500.php');
    exit;
}

if (!$fiets) {
    header('Location: 404.php');
    exit;
}

require 'header.php';
?>

<main>
    <div class="container">
        <form method="POST" action="fiets_update_process.php" class="login-form">
            <h1>Dagprijs wijzigen</h1>
            <p>
                <span class="badge"><?php echo htmlspecialchars($fiets['type_naam']) ?></span>
                <strong><?php echo htmlspecialchars($fiets['merk']) ?></strong>, framemaat <?php echo htmlspecialchars($fiets['framemaat']) ?>
            </p>

            <!-- Stuur het id mee als verborgen veld -->
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($fiets['id']) ?>">

            <div>
                <label for="dagprijs">Dagprijs*</label>
                <input type="number" step="0.01" min="0.01" id="dagprijs" name="dagprijs" value="<?php echo htmlspecialchars($fiets['dagprijs']) ?>" required>
            </div>
            <button type="submit" class="btn btn-orange">Opslaan</button>
            <p><a href="fietsen_index.php">Terug naar fietsen</a></p>
        </form>
    </div>
</main>
<?php require 'footer.php' ?>
