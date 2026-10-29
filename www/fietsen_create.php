<?php
require 'medewerker_check.php';
require 'database.php';

try {
    // types voor de keuzelijst
    $stmt = $conn->prepare("SELECT * FROM fiets_types ORDER BY naam");
    $stmt->execute();
    $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <form action="fietsen_create_process.php" method="post" class="login-form">
            <h1>Nieuwe fiets</h1>
            <div>
                <label for="fiets_type_id">Type*</label>
                <select id="fiets_type_id" name="fiets_type_id">
                    <option value="">Kies een type</option>
                    <?php foreach ($types as $type) : ?>
                        <option value="<?php echo htmlspecialchars($type['id']) ?>"><?php echo htmlspecialchars($type['naam']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="merk">Merk*</label>
                <input type="text" id="merk" name="merk" maxlength="255">
            </div>
            <div>
                <label for="framemaat">Framemaat*</label>
                <input type="text" id="framemaat" name="framemaat" maxlength="255" placeholder="bijv. 53 of one size">
            </div>
            <div>
                <label for="dagprijs">Dagprijs*</label>
                <input type="number" step="0.01" min="0.01" id="dagprijs" name="dagprijs">
            </div>
            <div>
                <label for="foto">Foto (bestandsnaam in www/images)</label>
                <input type="text" id="foto" name="foto" maxlength="255" placeholder="bijv. gazelle-stad.jpg">
            </div>
            <button type="submit" class="btn btn-orange">Toevoegen</button>
        </form>
    </div>
</main>
<?php require 'footer.php' ?>
