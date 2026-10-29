<?php
require 'medewerker_check.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: reserveringen_index.php');
    exit;
}

require 'database.php';
require 'logboek_toevoegen.php';

$id           = $_POST['id'] ?? '';
$inleverdatum = trim($_POST['inleverdatum'] ?? '');
$opmerking    = trim($_POST['opmerking'] ?? '');

if (!is_numeric($id)) {
    header('Location: 404.php');
    exit;
}

$terug = 'reservering_update.php?id=' . (int) $id;

try {
    $stmt = $conn->prepare("SELECT * FROM reserveringen WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $reservering = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservering) {
        header('Location: 404.php');
        exit;
    }

    // valideren: inleverdatum en opmerking mogen leeg zijn, dan wordt het NULL
    if ($inleverdatum === '') {
        $inleverdatum = null;
    } elseif (strtotime($inleverdatum) === false) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Dit is geen geldige inleverdatum.'];
        header('Location: ' . $terug);
        exit;
    } else {
        $inleverdatum = date('Y-m-d', strtotime($inleverdatum));
        if ($inleverdatum < $reservering['ophaaldatum']) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'De inleverdatum mag niet vóór de ophaaldatum liggen.'];
            header('Location: ' . $terug);
            exit;
        }
    }

    if ($opmerking === '') {
        $opmerking = null;
    } elseif (strlen($opmerking) > 1000) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'De opmerking is te lang (max. 1000 karakters).'];
        header('Location: ' . $terug);
        exit;
    }

    // LET OP: vergeet WHERE niet — anders worden ALLE reserveringen bijgewerkt!
    $stmt = $conn->prepare(
        "UPDATE reserveringen
         SET inleverdatum = :inleverdatum,
             opmerking = :opmerking,
             updated_at = NOW()
         WHERE id = :id"
    );
    $stmt->execute([
        'inleverdatum' => $inleverdatum,
        'opmerking'    => $opmerking,
        'id'           => $id,
    ]);

    logboek($conn, 'Reservering ' . $reservering['id'] . ' bijgewerkt (inleverdatum: ' . ($inleverdatum ?? 'leeg') . ', opmerking: ' . ($opmerking ?? 'leeg') . ')');
} catch (PDOException $e) {
    // Log de error (niet tonen aan gebruiker!)
    error_log("Database error: " . $e->getMessage());
    header('Location: 500.php');
    exit;
}

$_SESSION['message'] = ['type' => 'success', 'text' => 'Reservering ' . $reservering['id'] . ' is opgeslagen.'];
header('Location: reserveringen_index.php');
exit;
