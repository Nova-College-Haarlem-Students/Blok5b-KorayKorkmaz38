<?php
require 'medewerker_check.php';

//check method
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: fietsen_create.php');
    exit;
}
require 'database.php';
require 'logboek_toevoegen.php';

$fiets_type_id = $_POST['fiets_type_id'] ?? '';
$merk          = trim($_POST['merk'] ?? '');
$framemaat     = trim($_POST['framemaat'] ?? '');
$dagprijs      = $_POST['dagprijs'] ?? '';
$foto          = trim($_POST['foto'] ?? '');

try {
    // bestaat het gekozen type echt?
    $type_bestaat = false;
    if (is_numeric($fiets_type_id)) {
        $stmt = $conn->prepare("SELECT id FROM fiets_types WHERE id = :id");
        $stmt->execute(['id' => $fiets_type_id]);
        $type_bestaat = $stmt->fetch() ? true : false;
    }

    // input validatie
    $error = '';
    if (!$type_bestaat) {
        $error = 'Kies een type.';
    } elseif ($merk === '') {
        $error = 'Vul een merk in.';
    } elseif (strlen($merk) > 255) {
        $error = 'Merk is te lang. Gebruik max. 255 karakters.';
    } elseif ($framemaat === '' || strlen($framemaat) > 255) {
        $error = 'Vul een framemaat in (max. 255 karakters).';
    } elseif (!is_numeric($dagprijs) || $dagprijs <= 0) {
        $error = 'Dagprijs moet een positief getal zijn.';
    } elseif ($dagprijs > 999999.99) {
        $error = 'Dagprijs is te hoog.';
    } elseif (strlen($foto) > 255) {
        $error = 'Foto mag max. 255 karakters zijn.';
    }

    if ($error) {
        $_SESSION['message'] = ['type' => 'error', 'text' => $error];
        header('Location: fietsen_create.php');
        exit;
    }

    // foto is niet verplicht, leeg wordt NULL (dan toont de site een placeholder)
    if ($foto === '') {
        $foto = null;
    }

    $stmt = $conn->prepare("INSERT INTO fietsen (fiets_type_id, merk, framemaat, foto, dagprijs, created_at, updated_at)
                            VALUES (:fiets_type_id, :merk, :framemaat, :foto, :dagprijs, NOW(), NOW())");
    $stmt->execute([
        'fiets_type_id' => $fiets_type_id,
        'merk'          => $merk,
        'framemaat'     => $framemaat,
        'foto'          => $foto,
        'dagprijs'      => $dagprijs,
    ]);

    // lastInsertId geeft het id van de fiets die net is toegevoegd
    $id = $conn->lastInsertId();
    logboek($conn, 'Fiets ' . $id . ' (' . $merk . ') toegevoegd');
} catch (PDOException $e) {
    // Log de error (niet tonen aan gebruiker!)
    error_log("Database error: " . $e->getMessage());
    header('Location: 500.php');
    exit;
}

$_SESSION['message'] = ['type' => 'success', 'text' => 'Fiets ' . $merk . ' is toegevoegd.'];
header("Location: fietsen_index.php");
exit;
