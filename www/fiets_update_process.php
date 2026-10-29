<?php
require 'medewerker_check.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: fietsen_index.php');
    exit;
}

require 'database.php';
require 'logboek_toevoegen.php';

$id       = $_POST['id'] ?? '';
$dagprijs = $_POST['dagprijs'] ?? '';

// valideren
if (!is_numeric($id)) {
    header('Location: 404.php');
    exit;
}
if (!is_numeric($dagprijs) || $dagprijs <= 0 || $dagprijs > 999999.99) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Dagprijs moet een positief getal zijn.'];
    header('Location: fiets_update.php?id=' . (int) $id);
    exit;
}

try {
    // bestaat de fiets? en wat was de oude prijs (voor het logboek)
    $stmt = $conn->prepare("SELECT * FROM fietsen WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $fiets = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fiets) {
        header('Location: 404.php');
        exit;
    }

    // LET OP: vergeet WHERE niet — anders worden ALLE fietsen bijgewerkt!
    $stmt = $conn->prepare(
        "UPDATE fietsen
         SET dagprijs = :dagprijs,
             updated_at = NOW()
         WHERE id = :id"
    );
    $stmt->execute([
        'dagprijs' => $dagprijs,
        'id'       => $id,
    ]);

    logboek($conn, 'Dagprijs van fiets ' . $fiets['id'] . ' (' . $fiets['merk'] . ') aangepast van € '
        . number_format($fiets['dagprijs'], 2, ',', '') . ' naar € ' . number_format($dagprijs, 2, ',', ''));
} catch (PDOException $e) {
    // Log de error (niet tonen aan gebruiker!)
    error_log("Database error: " . $e->getMessage());
    header('Location: 500.php');
    exit;
}

$_SESSION['message'] = ['type' => 'success', 'text' => 'De dagprijs van ' . $fiets['merk'] . ' is opgeslagen.'];
header('Location: fietsen_index.php');
exit;
