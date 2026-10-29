<?php
require 'medewerker_check.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: fietsen_deleted.php');
    exit;
}

require 'database.php';
require 'logboek_toevoegen.php';

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header('Location: 404.php');
    exit;
}

$id = (int) $_POST['id'];

try {
    // herstellen: deleted_at weer leegmaken
    $stmt = $conn->prepare("UPDATE fietsen SET deleted_at = NULL WHERE id = :id AND deleted_at IS NOT NULL");
    $stmt->execute(['id' => $id]);

    // geen rij aangepast = fiets bestaat niet (of is al in de verhuur)
    if ($stmt->rowCount() === 0) {
        header('Location: 404.php');
        exit;
    }

    $stmt = $conn->prepare("SELECT merk FROM fietsen WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $merk = $stmt->fetchColumn();

    logboek($conn, 'Fiets ' . $id . ' (' . $merk . ') terug in de verhuur gezet');
} catch (PDOException $e) {
    // Log de error (niet tonen aan gebruiker!)
    error_log("Database error: " . $e->getMessage());
    header('Location: 500.php');
    exit;
}

$_SESSION['message'] = ['type' => 'success', 'text' => 'Fiets ' . $merk . ' is terug in de verhuur.'];
header('Location: fietsen_deleted.php');
exit;
