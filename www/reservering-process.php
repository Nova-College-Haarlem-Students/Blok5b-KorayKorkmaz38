<?php
session_start();
require 'database.php';

header('Content-Type: application/json');//vertelt de browser dat we JSON teruggeven

// alleen POST, net als bij de andere process-bestanden
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['success' => FALSE, 'melding' => 'Ongeldig verzoek.']);
    exit;
}

// Lees de JSON body
$input = file_get_contents('php://input');//leest de JSON body
$data = json_decode($input, true);//decode de JSON body

// sessiecheck: niet ingelogd = niet reserveren
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => FALSE, 'melding' => 'Log eerst in om te reserveren.']);
    exit;
}

if (!isset($data['fiets_id']) || !is_numeric($data['fiets_id'])) {
    echo json_encode(['success' => FALSE, 'melding' => 'Deze fiets bestaat niet.']);
    exit;
}

$fiets_id        = (int) $data['fiets_id'];
$ophaaldatum     = $data['ophaaldatum'] ?? '';
$terugbrengdatum = $data['terugbrengdatum'] ?? '';

// valideren
if ($ophaaldatum == '' || $terugbrengdatum == '' || strtotime($ophaaldatum) === false || strtotime($terugbrengdatum) === false) {
    echo json_encode(['success' => FALSE, 'melding' => 'Vul een geldige ophaal- en terugbrengdatum in.']);
    exit;
}

// datums altijd als jjjj-mm-dd, dan kun je ze met < en > vergelijken
$ophaaldatum     = date('Y-m-d', strtotime($ophaaldatum));
$terugbrengdatum = date('Y-m-d', strtotime($terugbrengdatum));

if ($ophaaldatum < date('Y-m-d')) {
    echo json_encode(['success' => FALSE, 'melding' => 'De ophaaldatum mag niet in het verleden liggen.']);
    exit;
}
if ($terugbrengdatum < $ophaaldatum) {
    echo json_encode(['success' => FALSE, 'melding' => 'De terugbrengdatum mag niet vóór de ophaaldatum liggen.']);
    exit;
}

try {
    // de fiets moet bestaan en in de verhuur zijn
    $stmt = $conn->prepare("SELECT * FROM fietsen WHERE id = :id AND deleted_at IS NULL");
    $stmt->execute(['id' => $fiets_id]);
    $fiets = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fiets) {
        echo json_encode(['success' => FALSE, 'melding' => 'Deze fiets kan nu niet gereserveerd worden.']);
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
        echo json_encode(['success' => FALSE, 'melding' => 'Deze fiets is in die periode al gereserveerd. Kies andere datums.']);
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
    echo json_encode(['success' => FALSE, 'melding' => 'Er ging iets mis, probeer het later opnieuw.']);
    exit;
}

$melding = 'Je reservering is opgeslagen: ' . $dagen . ' dag(en) voor € ' . number_format($totaalprijs, 2, ',', '') . '.';
echo json_encode(['success' => TRUE, 'melding' => $melding]);//geven aan de browser dat het GELUKT is
