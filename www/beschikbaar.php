<?php
session_start();
require 'database.php';

header('Content-Type: application/json');//vertelt de browser dat we JSON teruggeven

// Lees de JSON body
$input = file_get_contents('php://input');//leest de JSON body
$data = json_decode($input, true);//decode de JSON body

// niet ingelogd of geen fiets meegestuurd = niet controleren
if (!isset($_SESSION['user_id']) || !isset($data['fiets_id']) || !is_numeric($data['fiets_id'])) {
    echo json_encode(['success' => FALSE, 'melding' => 'Log eerst in.']);// geven aan de browser dat het NIET gelukt is
    exit;
}

$fiets_id = (int) $data['fiets_id'];
$ophaaldatum = $data['ophaaldatum'] ?? '';
$terugbrengdatum = $data['terugbrengdatum'] ?? '';

// dezelfde controles als in reservering-process.php
if ($ophaaldatum == '' || $terugbrengdatum == '' || strtotime($ophaaldatum) === false || strtotime($terugbrengdatum) === false) {
    echo json_encode(['success' => FALSE, 'melding' => 'Kies eerst een ophaal- en terugbrengdatum.']);
    exit;
}

$ophaaldatum = date('Y-m-d', strtotime($ophaaldatum));
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
    $stmt = $conn->prepare("SELECT * FROM fietsen WHERE id = :id AND in_verhuur = 1");
    $stmt->execute(['id' => $fiets_id]);
    $fiets = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fiets) {
        echo json_encode(['success' => FALSE, 'melding' => 'Deze fiets kan nu niet gereserveerd worden.']);
        exit;
    }

    // tellen hoeveel reserveringen deze periode overlappen
    $stmt = $conn->prepare("SELECT COUNT(*) FROM reserveringen
                            WHERE fiets_id = :fiets_id
                              AND ophaaldatum <= :terugbrengdatum
                              AND terugbrengdatum >= :ophaaldatum");
    $stmt->execute([
        'fiets_id' => $fiets_id,
        'ophaaldatum' => $ophaaldatum,
        'terugbrengdatum' => $terugbrengdatum,
    ]);
    $vrij = $stmt->fetchColumn() == 0;
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => FALSE, 'melding' => 'Er ging iets mis, probeer het later opnieuw.']);
    exit;
}

if ($vrij) {
    $dagen = round((strtotime($terugbrengdatum) - strtotime($ophaaldatum)) / 86400) + 1;
    $totaalprijs = $dagen * $fiets['dagprijs'];
    $melding = 'Deze fiets is vrij! ' . $dagen . ' dag(en) kost € ' . number_format($totaalprijs, 2, ',', '') . '.';
} else {
    $melding = 'Helaas, deze fiets is in die periode al gereserveerd.';
}

echo json_encode(['success' => TRUE, 'vrij' => $vrij, 'melding' => $melding]);//geven aan de browser dat het GELUKT is
