<?php
require 'login_check.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: index.php');
    exit;
}

require 'database.php';

$fiets_id        = $_POST['fiets_id'] ?? '';
$ophaaldatum     = $_POST['ophaaldatum'] ?? '';
$terugbrengdatum = $_POST['terugbrengdatum'] ?? '';

if (!is_numeric($fiets_id)) {
    header('Location: 404.php');
    exit;
}

// bij een fout gaan we terug naar de pagina van de fiets
$terug = 'fietsen_detail.php?id=' . (int) $fiets_id;

// valideren
if ($ophaaldatum == '' || $terugbrengdatum == '' || strtotime($ophaaldatum) === false || strtotime($terugbrengdatum) === false) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vul een geldige ophaal- en terugbrengdatum in.'];
    header('Location: ' . $terug);
    exit;
}

// datums altijd als jjjj-mm-dd, dan kun je ze met < en > vergelijken
$ophaaldatum     = date('Y-m-d', strtotime($ophaaldatum));
$terugbrengdatum = date('Y-m-d', strtotime($terugbrengdatum));

if ($ophaaldatum < date('Y-m-d')) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'De ophaaldatum mag niet in het verleden liggen.'];
    header('Location: ' . $terug);
    exit;
}
if ($terugbrengdatum < $ophaaldatum) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'De terugbrengdatum mag niet vóór de ophaaldatum liggen.'];
    header('Location: ' . $terug);
    exit;
}

try {
    // de fiets moet bestaan en in de verhuur zijn
    $stmt = $conn->prepare("SELECT * FROM fietsen WHERE id = :id AND in_verhuur = 1");
    $stmt->execute(['id' => $fiets_id]);
    $fiets = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fiets) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Deze fiets kan nu niet gereserveerd worden.'];
        header('Location: ' . $terug);
        exit;
    }

    // is de fiets in deze periode al gereserveerd?
    // twee periodes overlappen als de ene begint voordat de andere eindigt (en andersom)
    $stmt = $conn->prepare("SELECT COUNT(*) FROM reserveringen
                            WHERE fiets_id = :fiets_id
                              AND ophaaldatum <= :terugbrengdatum
                              AND terugbrengdatum >= :ophaaldatum");
    $stmt->execute([
        'fiets_id'        => $fiets_id,
        'ophaaldatum'     => $ophaaldatum,
        'terugbrengdatum' => $terugbrengdatum,
    ]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Deze fiets is in die periode al gereserveerd. Kies andere datums.'];
        header('Location: ' . $terug);
        exit;
    }

    // totaalprijs op de server berekenen, nooit uit het formulier halen
    // de ophaal- en terugbrengdag tellen allebei mee (86400 = seconden in een dag)
    $dagen = round((strtotime($terugbrengdatum) - strtotime($ophaaldatum)) / 86400) + 1;
    $totaalprijs = $dagen * $fiets['dagprijs'];

    $stmt = $conn->prepare("INSERT INTO reserveringen (gebruiker_id, fiets_id, ophaaldatum, terugbrengdatum, totaalprijs, created_at, updated_at)
                            VALUES (:gebruiker_id, :fiets_id, :ophaaldatum, :terugbrengdatum, :totaalprijs, NOW(), NOW())");
    $stmt->execute([
        'gebruiker_id'    => $_SESSION['user_id'],
        'fiets_id'        => $fiets_id,
        'ophaaldatum'     => $ophaaldatum,
        'terugbrengdatum' => $terugbrengdatum,
        'totaalprijs'     => $totaalprijs,
    ]);
} catch (PDOException $e) {
    // Log de error (niet tonen aan gebruiker!)
    error_log("Database error: " . $e->getMessage());
    header('Location: 500.php');
    exit;
}

$_SESSION['message'] = ['type' => 'success', 'text' => 'Je reservering is opgeslagen: ' . $dagen . ' dag(en) voor € ' . number_format($totaalprijs, 2, ',', '') . '.'];
header('Location: mijn_reserveringen.php');
exit;
