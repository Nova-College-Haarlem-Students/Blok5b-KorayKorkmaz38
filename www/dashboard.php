<?php
require 'medewerker_check.php';
require 'database.php';

try {
    // een paar tellers voor het dashboard
    $stmt = $conn->prepare("SELECT COUNT(id) AS total FROM fietsen WHERE deleted_at IS NULL");
    $stmt->execute();
    $in_verhuur = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT COUNT(id) AS total FROM fietsen WHERE deleted_at IS NOT NULL");
    $stmt->execute();
    $uit_verhuur = $stmt->fetch(PDO::FETCH_ASSOC);

    // reserveringen waarvan de fiets nog niet is ingeleverd
    $stmt = $conn->prepare("SELECT COUNT(id) AS total FROM reserveringen WHERE inleverdatum IS NULL");
    $stmt->execute();
    $open = $stmt->fetch(PDO::FETCH_ASSOC);
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
        <h1 class="section-title">Welkom <?php echo htmlspecialchars($_SESSION['naam'] ?? '') ?></h1>
        <p>Je bent ingelogd als <strong><?php echo htmlspecialchars($_SESSION['role'] ?? '') ?></strong>.</p>

        <div class="stats">
            <a href="fietsen_index.php" class="stat">
                <span class="stat-getal"><?php echo htmlspecialchars($in_verhuur['total']) ?></span>
                <span>Fietsen in de verhuur</span>
            </a>
            <a href="fietsen_deleted.php" class="stat">
                <span class="stat-getal"><?php echo htmlspecialchars($uit_verhuur['total']) ?></span>
                <span>Fietsen uit de verhuur</span>
            </a>
            <a href="reserveringen_index.php" class="stat">
                <span class="stat-getal"><?php echo htmlspecialchars($open['total']) ?></span>
                <span>Reserveringen nog niet ingeleverd</span>
            </a>
        </div>

        <div class="beheer-knoppen">
            <a href="fietsen_create.php" class="btn btn-orange">Nieuwe fiets toevoegen</a>
            <a href="fietsen_nooit_verhuurd.php" class="btn btn-light">Fietsen die nooit verhuurd zijn</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="statistieken.php" class="btn">Statistieken</a>
                <a href="logboek.php" class="btn">Logboek</a>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require 'footer.php' ?>
