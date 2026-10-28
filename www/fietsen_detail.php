<?php
session_start();
require 'database.php';

// Controleer of id meegegeven is
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: 404.php');
    exit;
}

$id = (int)$_GET['id'];

try {
    $stmt = $conn->prepare("SELECT fietsen.*, fiets_types.naam AS type_naam
                            FROM fietsen
                            LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
                            WHERE fietsen.id = :id");
    $stmt->execute(['id' => $id]);
    $fiets = $stmt->fetch(PDO::FETCH_ASSOC);

    // Als fiets niet bestaat, toon 404
    if (!$fiets) {
        header('Location: 404.php');
        exit;
    }
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
        <div class="product-detail">
            <div class="row">
                <div class="col">
                    <img src="<?php echo isset($fiets['foto']) ? htmlspecialchars($fiets['foto']) : 'https://placehold.co/400' ?>"
                        alt="<?php echo htmlspecialchars($fiets['merk']) ?>">
                </div>
                <div class="col">
                    <h3><?php echo htmlspecialchars($fiets['merk']) ?></h3>
                    <p>Type: <?php echo htmlspecialchars($fiets['type_naam']) ?></p>
                    <p>Framemaat: <?php echo htmlspecialchars($fiets['framemaat']) ?></p>
                    <p>€ <?php echo number_format($fiets['dagprijs'], 2, ',', '') ?> per dag</p>
                    <?php if ($fiets['in_verhuur'] == 0) : ?>
                        <p class="message message-error">Deze fiets is tijdelijk uit de verhuur.</p>
                    <?php endif; ?>
                    <p><a href="index.php" class="btn btn-light">Terug naar alle fietsen</a></p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require 'footer.php' ?>
